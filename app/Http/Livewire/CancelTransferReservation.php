<?php

namespace App\Http\Livewire;

use App\BusinessModels\Reservation\Actions\CancelReservation;
use App\BusinessModels\Reservation\Actions\UpdateReservation;
use App\Facades\EzMoney;
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
    public $cancellation_fee_percent = 0;
    public $cancellation_fee_nominal = 0;
    public $partnerID;
    public $infoMessage = '';
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

    public function mount(){
        $this->loadPartnerCommissionDetails();
    }

    protected function getRules(){
        $rules = [
            "cancellation_fee_percent" => 'required|integer|min:0|max:100',
            #"cancellation_fee_nominal" => 'required|numeric|min:1|max:'.(int)$this->reservation->getPrice()->formatByDecimal().'|regex:'. \App\Services\Helpers\EzMoney::MONEY_REGEX,
        ];

        return $rules;
    }
    public function cancelReservation()
    {

        if(!$this->cancellationDate){
            $this->cancellationDate = Carbon::now()->format('Y-m-d H:i:s');
        }

        $cancelAction = new CancelReservation($this->reservation);

        $cancelAction->cancelReservation(
            $this->cancellationDate,
            $this->cancellationType,
            $this->cancellation_fee_nominal
        );

        if($this->cancelRoundTrip){

            $cancelAction->cancelRoundTrip();
        }

        $this->emit('cancelCompleted');
    }

    public function updated($property){

        $this->validate($this->getRules());
        $reservationTotal = $this->reservation->getPrice();

        switch ($property){
            case 'cancellation_fee_percent':
                $this->cancellation_fee_nominal = number_format($reservationTotal->formatByDecimal()*($this->cancellation_fee_percent/100),2);
                break;
            case 'cancellation_fee_nominal':
                $this->cancellation_fee_percent = number_format(($this->cancellation_fee_nominal*$reservationTotal->formatByDecimal())/100);
                $this->cancellation_fee_nominal = number_format($this->cancellation_fee_nominal,2);
                break;
        }

        $this->cancellationDate = Carbon::now()->addHour()->format('Y-m-d H:i:s');

    }

    public function render()
    {
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
