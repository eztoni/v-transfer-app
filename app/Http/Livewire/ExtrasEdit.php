<?php

namespace App\Http\Livewire;

use App\Models\Extra;
use Livewire\Component;

class ExtrasEdit extends Component
{
    public Extra $extra;

    public function render()
    {
        return view('livewire.extras-edit');
    }
}
