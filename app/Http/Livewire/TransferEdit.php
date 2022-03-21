<?php

namespace App\Http\Livewire;

use App\Models\Transfer;
use Livewire\Component;

class TransferEdit extends Component
{
    public Transfer $transfer;

    public function render()
    {
        return view('livewire.transfer-edit');
    }
}
