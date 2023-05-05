<?php

namespace App\Http\Livewire;

use App\BusinessModels\Reservation\Actions\CancelReservation;
use App\BusinessModels\Reservation\Actions\UpdateReservation;
use App\Models\Partner;
use App\Models\Reservation;
use Carbon\Carbon;
use http\Env\Request;
use Livewire\Component;
use WireUi\Traits\Actions;
use function Symfony\Component\String\b;

class CancelTransferReservation extends Component
{
use Actions;
    public Reservation $reservation;
    public $cancellationDate;
    public bool $cancelRoundTrip = true;
    public $cancellation_fee_percent = 50;
    public $cancellation_fee_nominal = 0;
    public $partnerID;
    public $infoMessage = 'asd';
    public $partnerName = 'Partner';

    public $cancellationTypeOptions = array(
        'cancellation' => 'Cancellation',
        'refund' => 'Refund'
    );


    public $cancellationType = 'cancellation';

    public function close()
    {
        $this->emit('cancelCancelled');
    }

    public function cancelReservation()
    {

        if(!$this->cancellationDate){
            $this->cancellationDate = Carbon::now()->format('Y-m-d H:i:ss');
        }

        $cancelAction = new CancelReservation($this->reservation);
        $cancelAction->cancelReservation($this->cancellationDate);

        if($this->cancelRoundTrip){

            $cancelAction->cancelRoundTrip();
        }

        $this->emit('cancelCompleted');
    }



    public function render()
    {
        $this->loadPartnerCommissionDetails();
        return view('livewire.cancel-transfer-reservation');
    }

    private function loadPartnerCommissionDetails(){

        if($this->partnerID > 0){

            $partner = Partner::findOrFail($this->partnerID);

            $this->partnerName = $partner->name;

            $now = Carbon::now();

            $this->cancellationDate = $now->addHour();

            $transferDateTime = $this->reservation->date_time;

            #Calculate Hours Difference
            $hours_difference = $transferDateTime->diffInHours($now);

            $cancel_type = 0;
            $hours_difference = 24;



            switch ($hours_difference){
                case $hours_difference > 24:
                    $this->infoMessage = 'Cancellation more than 24 hours prior to transfer. No Cancellation Fee applies';
                    $this->cancellation_fee_nominal = 0;
                    $this->cancellation_fee_percent = 0;
                    break;
                default:
                    if($hours_difference >= 12){
                        $cancel_type = 24;
                    }

                    if($hours_difference < 12){
                        $cancel_type = 12;
                    }

                    $cancellation_fee_type = $partner->cf_type;

                    if($cancellation_fee_type == 'percent'){

                        $reservationTotal = $this->reservation->getPrice();

                        if($cancel_type == 24){
                            $cf_calc_amount = $partner->cf_amount_24;
                            $this->infoMessage = 'Cancellation less than 24 hours prior to transfer. Cancellation Fee of '.$cf_calc_amount.'% should apply';
                        }elseif ($cancel_type == 12){
                            $cf_calc_amount = $partner->cf_amount_12;
                            $this->infoMessage = 'Cancellation less than 12 hours prior to transfer. Cancellation Fee of '.$cf_calc_amount.'% should apply';
                        }

                        $cf_amount = number_format($reservationTotal->formatByDecimal()*($cf_calc_amount/100),2);

                        $this->cancellation_fee_percent = $cf_calc_amount;
                        $this->cancellation_fee_nominal = $cf_amount;
                    }

            }

        }
    }
}
