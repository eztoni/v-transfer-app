<?php

namespace App\Http\Livewire;

use App\Events\ReservationCreatedEvent;
use App\Models\Reservation;
use App\Services\Api\ValamarFiskalizacija;
use Livewire\Component;
use WireUi\Traits\Actions;

class IssueReservationInvoice extends Component
{
    use Actions;
    public Reservation $reservation;

    public function issueInvoice(){

        $fiskal = new ValamarFiskalizacija($this->reservation->id);
        $fiskal->fiskalReservation();

        if($this->reservation->getInvoiceData('zki') != '-'){
            ReservationCreatedEvent::dispatch($this->reservation,[
                ReservationCreatedEvent::SEND_MAIL_CONFIG_PARAM => true
            ]);
        }

        $this->notification()->success('Invoice Issued');
        $this->emit('invoiceIssueCompleted');
    }

    public function close()
    {
        $this->emit('cancelIssueInvoice');
    }

    public function render()
    {
        return view('livewire.issue-reservation-invoice');
    }

}
