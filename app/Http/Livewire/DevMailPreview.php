<?php

namespace App\Http\Livewire;

use App\Models\Reservation;
use Livewire\Component;

class DevMailPreview extends Component
{

    public $resId = null;
    public $mailType = 'CONFIRMATION';

    public function getReservationsForSelectProperty(){
        return Reservation::with(['pickupLocation','dropoffLocation','partner'])->orderByDesc('id')->get()->transform(function (Reservation $item){

            $roundTrip = $item->is_round_trip?'Round trip':'One way';

           return  [
               'name' => "#{$item->id}:  {$item->pickupLocation->name} -> {$item->dropoffLocation->name}",
               'id' => $item->id,
               'description' => "<span class='text-warning-600 font-bold'>{$item->partner->name}</span> - <span class='text-success font-bold'> {$roundTrip}</span>"

           ];
        });
    }





    public function render()
    {
        return view('livewire.dev-mail-preview');
    }
}
