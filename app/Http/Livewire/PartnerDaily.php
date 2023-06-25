<?php

namespace App\Http\Livewire;

use App\Exports\DestinationExport;
use App\Models\Destination;
use App\Models\Partner;
use App\Models\Reservation;
use App\Models\Route;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Money\Converter;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Exchange\SwapExchange;
use Money\Money;
use Swap\Laravel\Facades\Swap;
use WireUi\Traits\Actions;

class PartnerDaily extends Component
{
    use Actions;

    public $dateFrom;
    public $dateTo;
    public $partner = 0;
    public $status = 'All';
    public $message = '';

    public $pickupLocation = 0;
    public $dropoffLocation = 0;
    public int $destination = 0;
    public $totalEur;
    public $totalCommission;
    public $button_message = 'Generate and Download Partner PDF';
    public bool $isPartnerReporting = false;
    public bool $isPPOMReporting = false;
    public bool $isRPOReporting = false;
    public bool $isAgentReporting = false;

    public $reportType = 'partner';


    protected $rules = [
        'destination' => 'required',
        'dateFrom' => 'required|date',
        'dateTo' => 'required|date|after_or_equal:dateFrom',
        'partner' => 'required',
    ];

    public array $filteredReservations = array();


    public function mount()
    {
        $this->destination = \Auth::user()->destination_id;
        $this->dateFrom = Carbon::now()->addDay()->format('d.m.Y');
        $this->dateTo = Carbon::now()->addDay()->format('d.m.Y');
    }

    public function updated($property)
    {
    }

    public function getAdminDestinationsProperty()
    {
        $destinations = Destination::all()->mapWithKeys(function ($i) {
            return [$i->id => $i->name];
        });

       $destinations->prepend('All Destinations', 0);

        return $destinations;
    }

    public function getPartnersProperty()
    {
        $partners = Partner::query();

        $partners = $partners->get()->mapWithKeys(function ($i) {

            #Select First Partner as Pre Chosen
            if($this->partner < 1){
                $this->partner = $i->id;
            }

            return [$i->id => $i->name];
        });



        return $partners->toArray();
    }

    public function render()
    {
        return view('livewire.partner-daily');
    }

    public function generate(){

        $this->message = '';

        $this->validate($this->rules);

        $generatedDateFrom = Carbon::createFromFormat('d.m.Y',$this->dateFrom);
        $generatedDateTo = Carbon::createFromFormat('d.m.Y',$this->dateTo);

        if($generatedDateFrom->format('Y-m-d') == $generatedDateTo->format('Y-m-d')){
            $generatedDateTo->addDay();
        }

        $date_from = $generatedDateFrom->format('Y-m-d');
        $date_to = $generatedDateTo->format('Y-m-d');

        $reservations = Reservation::query()
            ->whereIsMain(true)
            ->with(['leadTraveller', 'pickupLocation', 'dropoffLocation', 'returnReservation'])
            ->where('status',Reservation::STATUS_CONFIRMED)
            ->where(function ($q) use($date_from,$date_to) {
                $q->where(function ($q) use($date_from,$date_to){
                    $q->whereDate('date_time', '>=', $date_from)
                        ->whereDate('date_time', '<=',  $date_to);
                })->orWHereHas('returnReservation',function ($q)use($date_from,$date_to){
                    $q->whereDate('date_time', '>=',  $date_from)
                        ->whereDate('date_time', '<=',  $date_to);
                });
            })->where('partner_id',$this->partner)->get();

        if($reservations->count() > 0){

            $this->redirect('preview_partner_mail_list/'.$this->partner.'/'.$generatedDateFrom->format('Y-m-d').'/'.$generatedDateTo->format('Y-m-d'));
        }else{
            $this->message = 'No bookings for selected partner \ period';
        }

    }
}
