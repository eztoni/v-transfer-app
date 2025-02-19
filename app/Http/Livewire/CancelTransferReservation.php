<?php

namespace App\Http\Livewire;

use App\BusinessModels\Reservation\Actions\CancelReservation;
use App\BusinessModels\Reservation\Actions\UpdateReservation;
use App\Facades\EzMoney;
use App\Models\Partner;
use App\Models\Reservation;
use App\Services\Api\ValamarFiskalizacija;
use App\Services\Api\ValamarOperaApi;
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
    public $cancellationReason = '';
    public String $displayPrice = '';
    public bool $cancelRoundTrip = false;
    public $cancellation_fee_percent = 0;
    public $cancellation_fee_nominal = 0;
    public $partnerID;
    public $infoMessage = '';
    public $partnerName = 'Partner';
    public $partnerConditions = '';

    public $cfpDisabled = 1;
    public $cfnDisabled = 1;

    public $cf_null = 0;

    public $lateCancellation = 0;
    public $cancellationTypeOptions = array(
        'cancellation' => 'Cancel - Cancellation',
        'refund' => 'Cancel - Refund',
        'no_show' => 'No Show'
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
            "cancellationReason" => 'required|string|min:4|max:500'
            #"cancellation_fee_nominal" => 'required|numeric|min:1|max:'.(int)$this->reservation->getPrice()->formatByDecimal().'|regex:'. \App\Services\Helpers\EzMoney::MONEY_REGEX,
        ];

        return $rules;
    }
    public function cancelReservation()
    {

        $this->validate($this->getRules());

        if(!$this->cancellationDate){
            $this->cancellationDate = Carbon::now()->format('Y-m-d H:i:s');
        }

        $this->reservation->cf_null = $this->cf_null;

        $this->reservation->late_cancellation = $this->lateCancellation == true ? 1 : 0;



        $cancelAction = new CancelReservation($this->reservation);

        $cancelAction->cancelReservation(
            $this->cancellationDate,
            $this->cancellationType,
            $this->cancellation_fee_nominal,
            $this->cancelRoundTrip,
            false,
            $this->cancellationReason
        );

        $operaAPI = new ValamarOperaApi();

            if($this->reservation->is_main){

                if($this->reservation->included_in_accommodation_reservation == 0){
                    $operaAPI->syncReservationWithOperaFull($this->reservation->id);
                }elseif ($this->reservation->cf_null == 1){
                    $operaAPI->syncReservationWithOperaFull($this->reservation->id,true);
                }

            }else{
                $main_res = Reservation::where('round_trip_id',$this->reservation->id)->get()->first();

                if($main_res){

                    if($this->reservation->included_in_accommodation_reservation == 0) {
                        $operaAPI->syncReservationWithOperaFull($main_res->id);
                    }elseif ($main_res->cf_null == 1){
                        $operaAPI->syncReservationWithOperaFull($main_res->id,true);
                    }

                }
            }


        $this->emit('cancelCompleted');
    }

    public function updated($property){


        //$this->validate($this->getRules());

        if($this->cancelRoundTrip){
            $reservationTotal = $this->reservation->getDisplayPrice();
        }else{
            $reservationTotal = $this->reservation->getPrice();
        }

        if($property == 'cancelRoundTrip'){
            $this->cancellation_fee_nominal = number_format($reservationTotal->formatByDecimal()*($this->cancellation_fee_percent/100),2);
        }

        $this->displayPrice = '€ '.$reservationTotal->formatByDecimal();

        switch ($property){
            case 'cancellation_fee_percent':
                $this->cancellation_fee_nominal = number_format($reservationTotal->formatByDecimal()*($this->cancellation_fee_percent/100),2);
                break;
            case 'cancellation_fee_nominal':
                $this->cancellation_fee_percent = number_format(($this->cancellation_fee_nominal*$reservationTotal->formatByDecimal())/100);
                $this->cancellation_fee_nominal = number_format($this->cancellation_fee_nominal,2);
                break;
            case 'lateCancellation':
                $this->cancellation_fee_percent = number_format(100);
                $this->cancellationReason = 'Kasni Storno';
                $this->cancellation_fee_nominal = number_format($reservationTotal->formatByDecimal());
                break;
        }

        $this->cancellationDate = Carbon::now()->format('Y-m-d H:i:s');

        if($this->cancellationType == 'no_show'){
            $this->cancellation_fee_percent = 100;
            $this->cancellation_fee_nominal = $reservationTotal->formatByDecimal();
        }else{
            $this->cfnDisabled = 1;
            $this->cfpDisabled = 1;
        }

    }

    public function render()
    {
        return view('livewire.cancel-transfer-reservation');
    }

    private function loadPartnerCommissionDetails(){

        if($this->partnerID > 0){

            $this->displayPrice = $this->reservation->getPrice();

            $partner = Partner::findOrFail($this->partnerID);

            $this->partnerName = $partner->name;

            $now = Carbon::now();

            $this->cancellationDate = $now;

            $transferDateTime = $this->reservation->date_time;

            #Calculate Hours Difference
            $hours_difference = $transferDateTime->diffInHours($now);

            $cancel_type = 0;

            switch($partner->cf_type){
                case 'percent':
                    $this->partnerConditions = $partner->cf_amount_24.'% for all cancellations under 24 hours. '.$partner->cf_amount_12.'% for all cancellations under 12 hours.';
                    break;
                case 'nominal':
                    $this->partnerConditions = $partner->cf_amount_24.'€ for all cancellations under 24 hours. '.$partner->cf_amount_12.'€ for all cancellations under 12 hours.';
                    break;
            }

            if($this->reservation->included_in_accommodation_reservation || $this->reservation->v_level_reservation){
                $this->cf_null = 1;
            }

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
