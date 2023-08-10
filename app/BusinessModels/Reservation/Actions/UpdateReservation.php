<?php

namespace App\BusinessModels\Reservation\Actions;

use App\Events\ReservationUpdatedEvent;
use App\Services\Api\ValamarOperaApi;

class UpdateReservation extends \App\BusinessModels\Reservation\Reservation
{
    public function updateReservation()
    {
        $this->validateReservation();

        $this->model->save();

        if($this->model->included_in_accommodation_reservation == 0 && $this->model->v_level_reservation == 0){

            $api = new ValamarOperaApi();
            $api->syncReservationWithOperaFull($this->model->id);
        }

        ReservationUpdatedEvent::dispatch($this->model,[ReservationUpdatedEvent::SEND_MAIL_CONFIG_PARAM => $this->sendMail]);

    }

    public function setSendMailBool(bool $sendMail)
    {
        $this->sendMail = $sendMail;
        return $this;
    }

}
