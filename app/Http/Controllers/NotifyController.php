<?php

namespace App\Http\Controllers;

use App\BusinessModels\Reservation\Actions\CancelReservation;
use App\BusinessModels\Reservation\Actions\UpdateReservation;
use App\Models\Partner;
use App\Services\Api\ValamarClientApi;
use App\Services\Api\ValamarFiskalizacija;
use App\Services\Api\ValamarOperaApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use App\Models\Reservation;
use App\Models\Point;
use DB;

class NotifyController extends Controller
{

    const RESERVATION_STATUS_ARRAY = array(
        'NEW',
        'RESERVED',
        'CANCEL',
        'CHECKED OUT',
        'CHECKED IN',
        'NO SHOW'
    );

    private $api_handler;

    function __construct()
    {
        $this->api_handler = new ValamarClientApi();
    }

    public function update(Request $request){

        $reservation_change = array();

        $response = array();

        $changed_bookings = $request->all();

        if(!empty($changed_bookings['reservations'])){
            foreach($changed_bookings['reservations'] as $data){
               $reservation_change[] = $data;
            }
        }else{
            return Response::json($response, 200);
        }

        #If there are bookings to be changed
        if(!empty($reservation_change)){

            foreach($reservation_change as $reservation_data){

                $update_response = $this->update_reservation($reservation_data['resvNameId'],$reservation_data['confirmationno']);

                if($update_response === true){

                    $response[$reservation_data['resvNameId']] = array(
                        'status' => 'success',
                        'message' => 'Reservation Updated Successfully: '.$reservation_data['resvNameId'].' - Confirmation:'.$reservation_data['confirmationno'],
                    );


                    \DB::insert('insert into opera_sync_log (log_message,reservation_id, opera_request,opera_response,sync_status,updated_by,updated_at) values (?, ?, ?, ?, ?, ?, ?)',
                        [
                            'Reservation Updated '.$reservation_data['resvNameId'],
                            0,
                            json_encode($request->all()),
                            json_encode($response),
                            'success',
                            0,
                            \Carbon\Carbon::now()->toDateTimeString()]
                    );

                }else{

                    if(empty($update_response)){
                        $update_response = 'Unknown error';
                    }

                    $response[$reservation_data['resvNameId']] = array(
                        'status' => 'error',
                        'message' => $update_response,
                    );

                }


            }
        }else{
            return Response::json($response, 200);
        }

        return Response::json($response, 200);
    }

    public function update_reservation($opera_id,$opera_confirmation){

        $return = true;

        $results = DB::table('travellers')->where('reservation_opera_confirmation',$opera_confirmation)->where('reservation_opera_id',$opera_id);

        if(!empty($results)){

            foreach($results as $result){
                
                $res_traveller_data = DB::table('reservation_traveller')->where('traveller_id',$result->id)->first();

                if(!empty($res_traveller_data)){

                    $reservation = Reservation::findOrFail($res_traveller_data->reservation_id);

                    $return_reservation = false;

                    $is_round_trip = false;

                    if(!$reservation){
                        return "No reservation with OperaID: ".$opera_id.' \ Opera Confirmation: '.$opera_confirmation;
                    }

                    if(is_numeric($reservation->round_trip_id)){
                        $is_round_trip = true;
                        $return_reservation = Reservation::findOrFail($reservation->round_trip_id);
                    }

                    $reservation_date = Carbon::create($reservation->date_time)->toDateString();
                    $reservation_time = Carbon::create($reservation->date_time)->toTimeString();

                    if($is_round_trip){
                        $return_reservation_date = Carbon::create($return_reservation->date_time)->toDateString();
                        $return_reservation_time = Carbon::create($return_reservation->date_time)->toTimeString();
                    }

                    $this->api_handler->setReservationCodeFilter($result->reservation_number);

                    $valamar_res_data = $this->api_handler->getReservationList();

                    $is_round_trip = false;

                    if(!empty($valamar_res_data[$result->reservation_number])){

                        #Check Reservation Status Change
                        if($reservation->getOverallReservationStatus() == Reservation::STATUS_CONFIRMED){

                            $opera_res_status = $valamar_res_data[$result->reservation_number]['status'];

                            switch ($opera_res_status){

                                case 'CANCEL':
                                case 'NO SHOW':

                                    $reservation->status = Reservation::STATUS_CANCELLED;

                                    $cancel = new CancelReservation($reservation);

                                    $partner = Partner::findOrFail($reservation->partner_id);

                                    $now = Carbon::now();

                                    $cancellationDate = $now->addHour();

                                    $transferDateTime = $reservation->date_time;

                                    #Calculate Hours Difference
                                    $hours_difference = $transferDateTime->diffInHours($now);


                                    $cancel_type = 0;


                                    switch ($hours_difference){
                                        case $hours_difference > 24:
                                            $cancellation_fee_nominal = 0;
                                            $cancellation_fee_percent = 0;
                                            break;
                                        default:
                                            if($hours_difference >= 12){
                                                $cancel_type = 24;
                                            }

                                            if($hours_difference < 12){
                                                $cancel_type = 12;
                                            }

                                            $cancellation_fee_type = $partner->cf_type;

                                            if($cancellation_fee_type == 'percent'){

                                                $reservationTotal = $reservation->getPrice();

                                                if($cancel_type == 24){
                                                    $cf_calc_amount = $partner->cf_amount_24;
                                                }elseif ($cancel_type == 12){
                                                    $cf_calc_amount = $partner->cf_amount_12;
                                                }

                                                $cf_amount = number_format($reservationTotal->formatByDecimal()*($cf_calc_amount/100),2);

                                                $cancellation_fee_nominal = $cf_amount;
                                            }
                                    }


                                    $cancel->cancelReservation($cancellationDate,'cancellation',$cancellation_fee_nominal,true,29);

                                    if($reservation->included_in_accommodation_reservation == 0){

                                        $operaAPI = new ValamarOperaApi();

                                        if($reservation->is_main){
                                            $operaAPI->syncReservationWithOperaFull($reservation->id);
                                            $fiskalValamar = new ValamarFiskalizacija($reservation->id);
                                            $fiskalValamar->fiskalReservation();

                                            if($reservation->hasCancellationFee()){
                                                $fiskalValamar->fiskalReservationCF($reservation->getCancellationFeeAmount(true));
                                            }

                                        }else{
                                            $main_res = Reservation::where('round_trip_id',$reservation->id)->get()->first();

                                            if($main_res){

                                                $operaAPI->syncReservationWithOperaFull($main_res->id);
                                                $fiskalValamar = new ValamarFiskalizacija($main_res->id);
                                                $fiskalValamar->fiskalReservation();

                                                if($main_res->hasCancellationFee()){
                                                    $fiskalValamar->fiskalReservationCF($main_res->getCancellationFeeAmount(true));
                                                }
                                            }
                                        }
                                    }

                                    $change = true;

                                    return true;
                                    break;
                            }
                        }

                        $change = false;

                        #Compare Data with the existing booking
                        $current_accommodation_checkin = $valamar_res_data[$result->reservation_number]['checkIn'];
                        $current_accommodation_checkout = $valamar_res_data[$result->reservation_number]['checkOut'];

                        $res_type = 'outgoing';

                        if(is_numeric($reservation->dropoff_address_id)){
                            $point = Point::find($reservation->dropoff_address_id);

                            if($point){
                                if($point->type == Point::TYPE_ACCOMMODATION){
                                    $res_type = 'incoming';
                                }
                            }
                        }

                        if($res_type == 'outgoing'){

                            if($current_accommodation_checkout != $reservation_date){

                                $reservation->date_time = Carbon::create($current_accommodation_checkout.' '.$reservation_time)->toDateTimeString();

                                $updater = new UpdateReservation($reservation);
                                $updater->setSendMailBool(true);

                                $updater->updateReservation();
                                $change = true;
                            }

                        }elseif($res_type == 'incoming'){

                            if($current_accommodation_checkin != $reservation_date){
                                $reservation->date_time = Carbon::create($current_accommodation_checkout.' '.$reservation_time)->toDateTimeString();
                                $updater = new UpdateReservation($reservation);
                                $updater->setSendMailBool(true);

                                $updater->updateReservation();

                                $change = true;
                            }
                        }

                        if($is_round_trip){

                            $res_type = 'outgoing';

                            if(is_numeric($return_reservation->dropoff_address_id)){
                                $point = Point::find($return_reservation->dropoff_address_id);

                                if($point){
                                    if($point->type == Point::TYPE_ACCOMMODATION){
                                        $res_type = 'incoming';
                                    }
                                }
                            }

                            if($res_type == 'outgoing'){
                                if($current_accommodation_checkout != $return_reservation_date){
                                    $return_reservation->date_time = Carbon::create($current_accommodation_checkout.' '.$return_reservation_time)->toDateTimeString();
                                    $updater = new UpdateReservation($return_reservation);
                                    $updater->setSendMailBool(true);

                                    $updater->updateReservation();
                                    $change = true;
                                }
                            }elseif($res_type == 'incoming'){
                                if($current_accommodation_checkin != $return_reservation_date){
                                    $return_reservation->date_time = Carbon::create($current_accommodation_checkout.' '.$return_reservation_time)->toDateTimeString();

                                    $updater = new UpdateReservation($return_reservation);
                                    $updater->setSendMailBool(true);

                                    $updater->updateReservation();
                                    $change = true;
                                }
                            }
                        }

                        return true;

                    }else{
                        return 'Unable to fetch reservation data for reservation '.$result->reservation_number;
                    }

                }
            }


        }else{
            return 'Reservation not present in the system';
        }

        return $return;
    }
}
