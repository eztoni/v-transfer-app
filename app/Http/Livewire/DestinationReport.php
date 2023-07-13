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

class DestinationReport extends Component
{
    use Actions;

    public int $destination;

    public $dateFrom;
    public $dateTo;
    public $partner = 0;
    public $status = 'All';

    public $pickupLocation = 0;
    public $dropoffLocation = 0;

    public $totalEur;
    public $totalCommission;

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

    public array $filteredReservations;


    public function mount()
    {
        $this->destination = \Auth::user()->destination_id;


        $this->dateFrom = Carbon::now()->startOfMonth()->format('d.m.Y');
        $this->dateTo = Carbon::now()->endOfMonth()->format('d.m.Y');
        $this->filteredReservations = [];

        #Partner Report
        if(request()?->routeIs('partner-report')){
            $this->isPartnerReporting = true;
            $this->reportType = 'partner-report';
        }

        #PPOM Report
        if(request()?->routeIs('ppom-report')){
            $this->isPPOMReporting = true;
            $this->reportType = 'ppom-report';
        }

        #RPO Report
        if(request()?->routeIs('rpo-report')){
            $this->isRPOReporting = true;
            $this->reportType = 'rpo-report';
        }

        #Agent Report
        if(request()?->routeIs('agent-report')){
            $this->isAgentReporting = true;
            $this->reportType = 'agent-report';
        }


        if($this->isPartnerReporting){
            $p = Partner::first();
            if($p){
                $this->partner = $p->id;
            }
        }


    }


    public function getPickupLocationsProperty()
    {
        return Route::query()
            ->where('destination_id', $this->destination)
            ->with('startingPoint')
            ->get()
            ->pluck('startingPoint')
            ->unique()
            ->mapWithKeys(function ($i) {
                return [$i->id => $i->name];
            })
            ->prepend('All pickup destinations', 0)
            ->toArray();
    }

    public function getdropoffLocationsProperty()
    {

        return Route::query()
            ->with('endingPoint')
            ->where('destination_id', $this->destination)
            ->where('starting_point_id', $this->pickupLocation)
            ->get()
            ->pluck('endingPoint')
            ->unique()
            ->mapWithKeys(function ($i) {
                return [$i->id => $i->name];
            })
            ->prepend('All dropoff destinations', 0)
            ->toArray();
    }


    public function generate(\Swap\Swap $swap)
    {

        $this->totalEur = Money::EUR(0);
        $this->totalCommission = \Cknow\Money\Money::EUR(0);
        $exchange = new SwapExchange($swap);

        $converter = new Converter(new ISOCurrencies(), $exchange);

        $this->filteredReservations =
            Reservation::query()
                ->whereIsMain(true)
                ->with(['leadTraveller', 'pickupLocation', 'dropoffLocation', 'returnReservation'])
                ->when($this->destination != 'All', function ($q) {
                    $q->where('destination_id', $this->destination);
                })
                ->where(function ($q)  {
                    $q->where(function ($q) {
                        $q->whereDate('date_time', '>=', Carbon::createFromFormat('d.m.Y', $this->dateFrom))
                            ->whereDate('date_time', '<=', Carbon::createFromFormat('d.m.Y', $this->dateTo));
                    })->orWHereHas('returnReservation',function ($q){
                        $q->whereDate('date_time', '>=', Carbon::createFromFormat('d.m.Y', $this->dateFrom))
                            ->whereDate('date_time', '<=', Carbon::createFromFormat('d.m.Y', $this->dateTo));
                    });
                })
                ->when($this->partner != 0, function ($q) {
                    $q->where('partner_id', $this->partner);
                })
                ->when($this->pickupLocation != 0, function ($q) {
                    $q->where('pickup_location', $this->pickupLocation);
                })
                ->when($this->dropoffLocation != 0, function ($q) {
                    $q->where('dropoff_location', $this->dropoffLocation);
                })
                ->when($this->status != 'All', function ($q) {
                    $q->where('status', $this->status);
                })
                ->get()
                ->map(function (Reservation $i) use ($converter) {

                    $priceEur = \Cknow\Money\Money::EUR($i->price);

                    if ($i->isConfirmed()) {
                        $this->totalEur = $this->totalEur->add($priceEur->getMoney());
                        $this->totalCommission = $this->totalCommission->add($i->total_commission_amount);
                    }else{

                        if($i->cancellation_type == 'cancellation'){

                            if($i->cancellation_fee > 0){


                                $cf_money = \CKnow\Money\Money::EUR($i->cancellation_fee*100);

                                $this->totalEur = $this->totalEur->add($cf_money->getMoney());
                                $this->totalCommission = $this->totalCommission->add($cf_money);
                            }else{
                                $this->totalEur = $this->totalEur->add($priceEur->getMoney());
                                $this->totalCommission = $this->totalCommission->add($i->total_commission_amount);
                            }
                        }elseif($i->cancellation_type == 'no_show'){
                            $this->totalEur = $this->totalEur->add($priceEur->getMoney());
                            $this->totalCommission = $this->totalCommission->add($i->total_commission_amount);
                        }
                    }


                    $inv = $priceEur->subtract($i->total_commission_amount)->getMoney();

                    $invEur = \Cknow\Money\Money::EUR($inv->getAmount());

                    $pdv = $i->total_commission_amount->multiply('0.20');
                    $pdv = \Cknow\Money\Money::EUR($pdv->getAmount());

                    $invoice_data = \DB::table('invoices')->where('reservation_id','=',$i->id)->first();

                    $net_profit = $i->total_commission_amount->subtract($pdv)->getMoney();

                    $net_profit = \Cknow\Money\Money::EUR($net_profit->getAmount());

                    $invoice_number = '-';

                    if(!empty($invoice_data)) {
                        $invoice_number = $invoice_data->invoice_id.'/'.$invoice_data->invoice_establishment.'/'.$invoice_data->invoice_device;
                    }

                    $net_profit = (string)$net_profit;
                    $net_profit = preg_replace('!€!','',$net_profit);

                    $priceEur = (string)$priceEur;
                    $priceEur = preg_replace('!€!','',$priceEur);


                    $total_comm = (string)$i->total_commission_amount;
                    $total_comm= preg_replace('!€!','',$total_comm);

                    $invEur = (string)$invEur;
                    $invEur = preg_replace('!€!','',$invEur);


                    $pdv = (string)$pdv;
                    $pdv = preg_replace('!€!','',$pdv);

                    if($i->status == 'cancelled'){

                        if($i->cancellation_type == 'cancellation'){

                            if($i->cancellation_fee > 0){

                                $cf_money = \CKnow\Money\Money::EUR($i->cancellation_fee*100);
                                $priceEur = \Cknow\Money\Money::EUR($cf_money->getAmount());

                                $priceEur = (string)$priceEur;
                                $priceEur = preg_replace('!€!','',$priceEur);

                                $net_profit = $priceEur;
                                $total_comm = $priceEur;
                                $pdv = 0;
                                $invEur = 0;
                            }else{
                                /*
                                $priceEur = 0;
                                $net_profit = 0;
                                $total_comm = 0;
                                $pdv = 0;
                                $invEur = 0;
                                */
                            }
                        }
                    }

                    $selling_place = '';

                    if($i->pickupAddress->type == 'accommodation'){
                        $selling_place = $i->pickupAddress->name;
                    }elseif($i->dropoffAddress->type == 'accommodation'){
                        $selling_place = $i->dropoffAddress->name;
                    }

                    $sales_agent = 'VEC Agent';

                    if($i->createdBy){
                        $sales_agent = $i->createdBy->name;
                    }


                    $status = 'RP';

                    if($i->isCancelled()){
                        if($i->hasCancellationFee()){
                            $status = 'CF';
                        }else{
                            $status = 'Storno';
                        }
                    }

                    return [
                        'id' => $i->id,
                        'name' => $i->leadTraveller?->first()->full_name,
                        'date_time' => $i->date_time?->format('d.m.Y @ H:i'),
                        'partner' => $i->partner->name,
                        'adults' => $i->adults,
                        'children' => $i->children,
                        'infants' => $i->infants,
                        'transfer' => $i->transfer?->name,
                        'vehicle' => $i->transfer?->vehicle?->type,
                        'status' => $i->status,
                        'price_eur' => $priceEur,
                        'round_trip' => $i->is_round_trip,
                        'round_trip_date' => $i->returnReservation?->date_time?->format('d.m.Y @ H:i'),
                        'voucher_date' => $i->created_at->toDateString(),
                        'tax_level'=>  \Arr::get($i->transfer_price_state,'price_data.tax_level'),
                        'commission'=>  \Arr::get($i->transfer_price_state,'price_data.commission'),
                        'commission_amount'=>  $total_comm,
                        'net_income' => $net_profit,
                        'invoice_charge' =>  $invEur,
                        'invoice_number' => (string) $invoice_number,
                        'pdv' => $pdv,
                        'procedure' => $status,
                        'selling_place' => $selling_place,
                        'sales_agent' => $sales_agent
                    ];
                })->toArray();

        $this->totalEur = (string)\Cknow\Money\Money::fromMoney($this->totalEur);
        $this->totalCommission = (string)$this->totalCommission;

    }

    public function updated($property)
    {
        $this->validateOnly($property);
    }

    public function getPartnersProperty()
    {
        $partners = Partner::query();

        $partners = $partners->get()->mapWithKeys(function ($i) {
            return [$i->id => $i->name];
        });

        $partners->prepend('All partners', 0);

        $this->partner = 0;

        return $partners->toArray();
    }

    public function exportToExcel(){

        $destination = Destination::withoutGlobalScopes()->find($this->destination)?->name;
        $partner = Partner::find($this->partner)?->name;
        $owner = \Auth::user()->owner->name;

        $export = new DestinationExport($this->filteredReservations,$this->reportType);

        $export->setFilterData([

                Carbon::make($this->dateFrom)->format('d.m.Y'),
                Carbon::make($this->dateTo)->format('d.m.Y'),
                $this->totalEur,
                $this->totalCommission

        ]);

        switch($this->reportType){
            case 'partner-report':
                $fileName = "reporting_".gmdate('dmy').'_partner_voucher';
                break;
            case 'ppom-report':
                $fileName = "reporting_".gmdate('dmy').'_PPOM';
                break;
            case 'rpo-report':
                $fileName = "reporting_".gmdate('dmy').'_RPO';
                break;

            case 'agent-report':
                $fileName = "reporting_".gmdate('dmy').'_agent_efficiency';
                break;
        }

        return Excel::download($export,"$fileName.xlsx");
    }

    public function getAdminDestinationsProperty()
    {
        $destinations = Destination::all()->mapWithKeys(function ($i) {
            return [$i->id => $i->name];
        });

        if ($this->isPartnerReporting) {
            $destinations->prepend('All partners', 0);
        }

        return $destinations;
    }


    public function render()
    {
        return view('livewire.destination-report');
    }
}
