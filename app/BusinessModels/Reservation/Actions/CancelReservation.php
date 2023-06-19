<?php

namespace App\BusinessModels\Reservation\Actions;

use App\Events\ReservationCancelledEvent;
use App\Events\ReservationCreatedEvent;
use App\Events\ReservationUpdatedEvent;
use App\Models\Reservation;
use App\Services\Api\ValamarFiskalizacija;
use App\Services\Api\ValamarOperaApi;

class CancelReservation extends \App\BusinessModels\Reservation\Reservation
{

    public $cancelRoundTrip = false;

    public function cancelRoundTrip(){

        if($this->model->is_main){

            $roundTripReservation =  Reservation::find($this->model->round_trip_id);

            if($roundTripReservation && !$roundTripReservation->isCancelled()){

                $roundTripReservation->status =  Reservation::STATUS_CANCELLED;
                $roundTripReservation->save();
            }

        }
    }

    public function cancelReservation($cancellationdate = false,$cancellation_type = 'cancellation',$cancellation_fee = 0,$cancel_round_trip = false)
    {

        $this->cancelRoundTrip = $cancel_round_trip;

        $this->model->status = Reservation::STATUS_CANCELLED;

        if($cancellationdate){
            $this->model->setUpdatedAt($cancellationdate);
        }

        $this->model->cancellation_type = $cancellation_type;
        $this->model->cancellation_fee = $cancellation_fee;
        $this->model->cancelled_at = $cancellationdate;

        $this->model->save();

        #Default Settings - cancellation and called model as notification model
        $event = 'cancellation';
        $notification_model = $this->model;

        #If Cancellation of the roundtrip - everything stays the same
        if($this->cancelRoundTrip){
            $this->cancelRoundTrip();
        }else{
            if($this->model->is_main){

                if($this->model->isRoundTrip()){

                    $round_trip_reservation = Reservation::where('round_trip_id',$this->model->round_trip_id)->get()->first();
                    #If we are canceling one way ( main ) and other is confirmed
                    if($round_trip_reservation->status == Reservation::STATUS_CONFIRMED){
                        $event = 'updated';
                    }
                }

            }else{

                #Clicked on non-main way
                $main_booking = Reservation::where('round_trip_id',$this->model->id)->get()->first();

                #If the main is confirmed and we are cancelling non main route
                if($main_booking->status == Reservation::STATUS_CONFIRMED){
                    $event = 'updated';
                    $notification_model = $main_booking;
                }

            }

        }

        if($event == 'cancellation'){
            ReservationCancelledEvent::dispatch($notification_model,[
                ReservationCancelledEvent::SEND_MAIL_CONFIG_PARAM => true
            ]);
        }elseif($event == 'updated'){
            ReservationUpdatedEvent::dispatch($notification_model,[ReservationUpdatedEvent::SEND_MAIL_CONFIG_PARAM => true);
        }

    }
}
