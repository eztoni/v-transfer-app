<?php

namespace App\Http\Livewire;

use App\Models\Vehicle;
use Livewire\Component;

class VehicleEdit extends Component
{
    public Vehicle $vehicle;

    public function render()
    {
        return view('livewire.vehicle-edit');
    }
}
