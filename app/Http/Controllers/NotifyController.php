<?php

namespace App\Http\Controllers;

use App\Services\Api\ValamarClientApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use App\Models\Reservation;
use App\Models\Point;
use DB;

class NotifyController extends Controller
{
    private $api_handler;

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

            $this->api_handler = new ValamarClientApi();

            foreach($reservation_change as $reservation_data){

                $update_response = $this->update_reservation($reservation_data['resvNameId'],$reservation_data['confirmationno']);

                if($update_response === true){
                    $response[$reservation_data['resvNameId']] = array(
                        'status' => 'success',
                        'message' => 'Reservation Updated Successfully: '.$reservation_data['resvNameId'].' - Confirmation:'.$reservation_data['confirmationno'],
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

    private function update_reservation($opera_id,$opera_confirmation){

        $return = true;

        $result = DB::table('travellers')->where('reservation_opera_confirmation',$opera_confirmation)->where('reservation_opera_id',$opera_id)->first();

        if(!empty($result)){

            $res_traveller_data = DB::table('reservation_traveller')->where('traveller_id',$result->id)->first();

            if(!empty($res_traveller_data)){

                $reservation = Reservation::find($res_traveller_data->reservation_id);
                $return_reservation = false;

                $is_round_trip = false;

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
                            $reservation->save();
                            $change = true;
                        }
                    }elseif($res_type == 'incoming'){

                        if($current_accommodation_checkin != $reservation_date){
                            $reservation->date_time = Carbon::create($current_accommodation_checkout.' '.$reservation_time)->toDateTimeString();
                            $reservation->save();
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
                                $reservation->date_time = Carbon::create($current_accommodation_checkout.' '.$return_reservation_time)->toDateTimeString();
                                $return_reservation->save();
                                $change = true;
                            }
                        }elseif($res_type == 'incoming'){
                            if($current_accommodation_checkin != $return_reservation_date){
                                $return_reservation->date_time = Carbon::create($current_accommodation_checkout.' '.$return_reservation_time)->toDateTimeString();
                                $return_reservation->save();
                                $change = true;
                            }
                        }
                    }

                    return true;

                }else{
                    return 'Unable to fetch reservation data for reservation '.$result->reservation_number;
                }

            }
        }else{
            return 'Reservation not present in the system';
        }

        return $return;
    }
}
