<?php

namespace App\BusinessModels\Reservation\Actions;

use App\Events\ReservationCancelledEvent;
use App\Events\ReservationCreatedEvent;
use App\Events\ReservationUpdatedEvent;
use App\Models\Reservation;
use App\Models\Route;
use App\Services\Api\ValamarFiskalizacija;
use App\Services\Api\ValamarOperaApi;
use Carbon\Carbon;

class CancelReservation extends \App\BusinessModels\Reservation\Reservation
{

    public $cancelRoundTrip = false;

    public $has_single_way_cancellation_restriction = false;

    public function cancelRoundTrip(){

        if($this->model->is_main){

            $roundTripReservation =  Reservation::find($this->model->round_trip_id);

            if($roundTripReservation && !$roundTripReservation->isCancelled()){

                $roundTripReservation->status =  Reservation::STATUS_CANCELLED;
                $roundTripReservation->cancelled_at = Carbon::now()->format('Y-m-d H:i:s');
                $roundTripReservation->save();
            }

        }
    }

    public function cancelReservation($cancellationdate = false,$cancellation_type = 'cancellation',$cancellation_fee = 0,$cancel_round_trip = false,$user_id = false,$cancellation_reason = '')
    {

        $this->sendMail = true;

        $this->cancelRoundTrip = $cancel_round_trip;

        $this->model->status = Reservation::STATUS_CANCELLED;

        if($cancellationdate){
            $this->model->setUpdatedAt($cancellationdate);
        }

        $this->model->cancellation_type = $cancellation_type;
        $this->model->cancellation_fee = $cancellation_fee;
        $this->model->cancelled_at = $cancellationdate;
        $this->model->cancellation_reason = trim($cancellation_reason);

        if(!empty($user_id) && $user_id > 0){
            $this->model->updated_by = $user_id;
        }

        $this->model->save();

        #Default Settings - cancellation and called model as notification model
        $event = 'cancellation';
        $notification_model = $this->model;

        #If Cancellation of the roundtrip - everything stays the same
        if($this->cancelRoundTrip){
            $this->cancelRoundTrip();

            $fiskalValamar = new ValamarFiskalizacija($this->model->id);
           // $fiskalValamar->fiskalReservation();
            if($this->model->hasCancellationFee()){
            //    $fiskalValamar->fiskalReservationCF($this->model->getCancellationFeeAmount(true));
            }

        }else{
            if($this->model->is_main){

                if($this->model->isRoundTrip()){

                    $round_trip_reservation = Reservation::where('round_trip_id',$this->model->round_trip_id)->get()->first();
                    #If we are canceling one way ( main ) and other is confirmed
                    if($this->model->returnReservation->status == Reservation::STATUS_CONFIRMED){
                        $event = 'updated';
                    }
                }

                $fiskalValamar = new ValamarFiskalizacija($this->model->id);
             //   $fiskalValamar->fiskalReservation();

                if($this->model->hasCancellationFee()){
               //     $fiskalValamar->fiskalReservationCF($this->model->getCancellationFeeAmount(true));
                }

                if(!$this->model->getInvoiceData('zki')){
                    $this->sendMail = false;
                }

            }else{

                #Clicked on non-main way
                $main_booking = Reservation::where('round_trip_id',$this->model->id)->get()->first();

                #If the main is confirmed, and we are cancelling non-main route
                if($main_booking->status == Reservation::STATUS_CONFIRMED){
                    $event = 'updated';
                    $notification_model = $main_booking;
                }


                $fiskalValamar = new ValamarFiskalizacija($main_booking->id);
                //$fiskalValamar->fiskalReservation();
                if($main_booking->hasCancellationFee()){
                  //  $fiskalValamar->fiskalReservationCF($main_booking->getCancellationFeeAmount(true));
                }

                if(!$main_booking->getInvoiceData('zki')){
                    $this->sendMail = false;
                }

            }

        }

        if($event == 'cancellation'){
            ReservationCancelledEvent::dispatch($notification_model,[
                ReservationCancelledEvent::SEND_MAIL_CONFIG_PARAM => $this->sendMail
            ]);

            $notification_model->saveCancellationDocument();

        }elseif($event == 'updated'){
            ReservationUpdatedEvent::dispatch($notification_model,[ReservationUpdatedEvent::SEND_MAIL_CONFIG_PARAM => $this->sendMail]);

            $notification_model->saveModificationDocument();
        }

        if($notification_model->hasCancellationFee()){
            $notification_model->saveCancellationFeeDocument();
        }



    }

    private function resolve_pricing(){

        $round_trip = false;

        if($this->model->is_main == 1 && $this->model->round_trip_id > 0){
            $round_trip = true;
        }

        if($this->model->is_main == 0){

            $main_booking = \App\Models\Reservation::where('round_trip_id',$this->model->id)->get()->first();


            $route = Route::where('starting_point_id', $this->model->pickup_location)
                ->where('ending_point_id', $this->model->dropoff_location)
                ->first();

            $priceHandler = (new \App\Services\TransferPriceCalculator($this->model->transfer_id,
                $this->model->partner_id,
                0,
                $route ? $route->id : null,
                collect(array())->reject(function ($item) {
                    return $item === false;
                })->keys()->toArray()))
                ->setBreakdownLang($this->model->confirmation_language);


            $this->model->price = $priceHandler->getPrice()->getAmount();
            $this->model->price_breakdown = $priceHandler->getPriceBreakdown();

            $main_booking->price = $priceHandler->getPrice()->getAmount();
            $main_booking->price_breakdown = $priceHandler->getPriceBreakdown();

            $main_booking->save();
            $this->model->save();

        }else{

        }

    }
}
