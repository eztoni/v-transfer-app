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

        $api = new ValamarOperaApi();
        $api->syncReservationWithOperaFull($this->model->id);

        ReservationUpdatedEvent::dispatch($this->model,[ReservationUpdatedEvent::SEND_MAIL_CONFIG_PARAM => $this->sendMail]);

    }

    public function setSendMailBool(bool $sendMail)
    {
        $this->sendMail = $sendMail;
        return $this;
    }

}
