<?php

namespace App\BusinessModels\Reservation\Actions;

class UpdateReservation extends \App\BusinessModels\Reservation\Reservation
{
    public function updateReservation()
    {
        $this->validateReservation();

        $this->model->save();

        //TODO: Dispatch update event

    }
}
