<?php

namespace App\BusinessModels\Reservation\Actions;

use App\Events\ReservationCancelledEvent;
use App\Events\ReservationCreatedEvent;
use App\Models\Reservation;
use App\Services\Api\ValamarFiskalizacija;
use App\Services\Api\ValamarOperaApi;

class CancelReservation extends \App\BusinessModels\Reservation\Reservation
{


    public function cancelRoundTrip(){
        if($this->model->is_main){

            $roundTripReservation =  Reservation::find($this->model->round_trip_id);

            if($roundTripReservation && !$roundTripReservation->isCancelled()){

                $roundTripReservation->status =  Reservation::STATUS_CANCELLED;
                $roundTripReservation->save();

                ReservationCancelledEvent::dispatch($roundTripReservation,[
                    ReservationCancelledEvent::SEND_MAIL_CONFIG_PARAM => true
                ]);
            }

        }
    }

    public function cancelReservation($cancellationdate = false,$cancellation_type = 'cancellation',$cancellation_fee = 0)
    {

        $this->model->status = Reservation::STATUS_CANCELLED;

        if($cancellationdate){
            $this->model->setUpdatedAt($cancellationdate);
        }

        $this->model->cancellation_type = $cancellation_type;
        $this->model->cancellation_fee = $cancellation_fee;
        $this->model->cancelled_at = $cancellationdate;

        /*
        #if the reservation is roundtrip and only the first route is cancelled
        if($this->model->is_main && $this->model->round_trip_id){

           $round_trip_res = Reservation::findOrFail($this->model->round_trip_id);

           if($round_trip_res->status == 'confirmed'){
               $this->model->is_main = 0;
               $this->model->round_trip_id = 0;
           }

           $round_trip_res->is_main = 1;

           $round_trip_res->save();

        }
        */

        $this->model->save();


        ReservationCancelledEvent::dispatch($this->model,[
            ReservationCancelledEvent::SEND_MAIL_CONFIG_PARAM => true
        ]);

    }
}
