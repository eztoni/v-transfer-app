<?php

namespace App\BusinessModels\Reservation\Actions;

use App\Events\ReservationUpdatedEvent;

class UpdateReservation extends \App\BusinessModels\Reservation\Reservation
{
    public function updateReservation()
    {
        $this->validateReservation();

        $this->model->save();

        ReservationUpdatedEvent::dispatch($this->model);

    }
}
