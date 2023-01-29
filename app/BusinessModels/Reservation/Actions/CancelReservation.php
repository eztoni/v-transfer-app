<?php

namespace App\BusinessModels\Reservation\Actions;

use App\Events\ReservationCancelledEvent;
use App\Events\ReservationCreatedEvent;
use App\Models\Reservation;
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

    public function cancelReservation()
    {
        $this->model->status = Reservation::STATUS_CANCELLED;

        $this->model->save();

        $api = new ValamarOperaApi();
        $api->syncReservationWithOpera($this->model->id);

        ReservationCancelledEvent::dispatch($this->model,[
            ReservationCancelledEvent::SEND_MAIL_CONFIG_PARAM => true
        ]);

    }
}
