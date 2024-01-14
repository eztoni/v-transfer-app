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
    public $partner;
    public $status = 'All';

    public $pickupLocation = 0;
    public $dropoffLocation = 0;

    //Bruto Prihod
    public $totalEur;
    //Bruto Profit
    public $totalCommission;
    //PDV
    public $totalPDV;
    //Total Trošak Ulaznog Računa
    public $totalInvoiceCharge;
    //Neto Profit
    public $totalNetProfit;

    public bool $isPartnerReporting = false;
    public bool $isPPOMReporting = false;
    public bool $isRPOReporting = false;
    public bool $isAgentReporting = false;

    public $reportType = 'partner';

    protected $rules = [
        'destination' => 'required',
        'dateFrom' => 'required|date',
        'dateTo' => 'required|date',
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

        $this->partner = 0;
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

    private function getCommissionMultiplier(Reservation $i){

        $return = '0.20';

        if(!empty($i->price_breakdown[0]['price_data'])){
            if(is_numeric($i->price_breakdown[0]['price_data']['commission'])){
                $return = number_format($i->price_breakdown[0]['price_data']['commission']/100,2);
            }
        }



        return $return;
    }
    public function generate(\Swap\Swap $swap)
    {

        $this->totalEur = Money::EUR(0);
        $this->totalCommission = \Cknow\Money\Money::EUR(0);
        $this->totalPDV  =\Cknow\Money\Money::EUR(0);
        $this->totalInvoiceCharge = \Cknow\Money\Money::EUR(0);
        $this->totalNetProfit = \Cknow\Money\Money::EUR(0);

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
                ->filter(function (Reservation $i){

                    if($this->isPPOMReporting){

                        if($i->getRouteTransferTaxLevel() != 'PPOM'){
                            return false;
                        }
                    }

                    if($this->isRPOReporting){
                        if($i->getRouteTransferTaxLevel() != 'RPO'){
                            return false;
                        }
                    }

                    #Return Valid Bookings - filter for PPOM and RPO reporting
                    return true;

                })
                ->map(function (Reservation $i) use ($converter) {

                    $partnerCommissionMultiplier = $this->getCommissionMultiplier($i);

                    $priceEur = \Cknow\Money\Money::EUR($i->price);

                    $priceOriginal = $priceEur;

                    if($i->isRoundTrip()){
                        $priceEur = $priceEur->multiply(2);
                    }

                    $transfer_desc = $i->pickupLocation->name.' -> '.$i->dropoffLocation->name;

                    if($i->isRoundTrip()){
                        $transfer_desc.= " <=> ".$i->dropoffLocation->name.' -> '.$i->pickupLocation->name;
                    }

                    $this->totalEur = $this->totalEur->add($priceEur->getMoney());

                    $inv = $priceEur->multiply($partnerCommissionMultiplier)->getMoney();

                    $total_comm = $priceEur->subtract($priceEur->multiply($partnerCommissionMultiplier));
                    #Add Total Commission - Bruto Profit
                    $this->totalCommission = $this->totalCommission->add($total_comm);
                    #Add Total Invoice Charge - Trošak Ulaznog Računa
                    $this->totalInvoiceCharge = $this->totalInvoiceCharge->add($inv);

                    $invEur = \Cknow\Money\Money::EUR($inv->getAmount());

                    $pdv = $total_comm->multiply($partnerCommissionMultiplier);
                    $this->totalPDV = $this->totalPDV->add($pdv);

                    $invoice_data = \DB::table('invoices')->where('reservation_id','=',$i->id)->first();

                    $net_profit = $total_comm->subtract($pdv->getMoney());

                    $this->totalNetProfit = $this->totalNetProfit->add($net_profit);

                    $invoice_number = '-';

                    if(!empty($invoice_data)) {
                        $invoice_number = $i->getInvoiceData('invoice_number');
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

                    #Change Added By Stefano . 16.11
                    $status = $i->getRouteTransferTaxLevel();

                    $return = array();

                    $i->partner->name;



                    $return[] =  [
                        'id' => $i->id,
                        'name' => $i->leadTraveller?->full_name,
                        'date_time' => $i->date_time?->format('d.m.Y'),
                        'partner' => $i->partner->name,
                        'adults' => $i->adults,
                        'children' => $i->children,
                        'infants' => $i->infants,
                        'transfer' => $i->transfer?->name,
                        'vehicle' => $i->transfer?->vehicle?->type,
                        'status' => $i->status,
                        'price_eur' => $priceEur->formatByDecimal(),
                        'round_trip' => $i->is_round_trip,
                        'round_trip_date' => $i->returnReservation?->date_time?->format('d.m.Y @ H:i'),
                        'voucher_date' => $i->created_at->format('d.m.Y'),
                        'tax_level'=>  $i->getRouteTransferTaxLevel(),
                        'commission'=>  \Arr::get($i->transfer_price_state,'price_data.commission'),
                        'commission_amount'=>  $invEur->formatByDecimal(),
                        'net_income' => $net_profit->formatByDecimal(),
                        'invoice_charge' =>  $total_comm->formatByDecimal(),
                        'invoice_number' => (string) $invoice_number,
                        'pdv' => $pdv->formatByDecimal(),
                        'procedure' => $status,
                        'selling_place' => $selling_place,
                        'sales_agent' => $sales_agent,
                        'description' => $transfer_desc
                    ];

                    $has_cancellation = false;


                    #Overall Cancel
                    if($i->getOverallReservationStatus() == Reservation::STATUS_CANCELLED){

                        $has_cancellation = true;

                        $priceEur = $priceOriginal->negative();

                        if($i->isRoundTrip()){
                            $priceEur = $priceEur->multiply(2);
                        }

                        $this->totalEur = $this->totalEur->add($priceEur->getMoney());

                        if($i->cancellation_type == 'no_show'){
                            $status = 'No Show';
                        }else{
                            $status = 'Storno';
                        }

                        $priceEur = $priceEur->formatByDecimal();
                        $this->totalCommission = $this->totalCommission->subtract($total_comm);
                        $net_profit = $net_profit->negative();
                        $invEur = $invEur->negative();
                        $total_comm = $total_comm->negative();
                        $pdv = $pdv->negative();

                        $this->totalPDV = $this->totalPDV->add($pdv);
                        $this->totalInvoiceCharge = $this->totalInvoiceCharge->add($invEur);
                        $this->totalNetProfit = $this->totalNetProfit->add($net_profit);

                        $return[] =  [
                            'id' => $i->id,
                            'name' => $i->leadTraveller?->full_name,
                            'date_time' => $i->date_time?->format('d.m.Y'),
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
                            'voucher_date' => $i->created_at->format('d.m.Y'),
                            'tax_level'=>  $i->getRouteTransferTaxLevel(),
                            'commission'=>  \Arr::get($i->transfer_price_state,'price_data.commission'),
                            'commission_amount'=>  $invEur->formatByDecimal(),
                            'net_income' => $net_profit->formatByDecimal(),
                            'invoice_charge' =>  $total_comm->formatByDecimal() ,
                            'invoice_number' => (string) $invoice_number,
                            'pdv' => $pdv->formatByDecimal(),
                            'procedure' => $status,
                            'selling_place' => $selling_place,
                            'sales_agent' => $sales_agent,
                            'description' => $transfer_desc
                        ];

                    }else{

                        #Just One Confirmed
                        if($i->isRoundTrip()){

                            #If the Second Direction is Cancelled
                            $return_direction_cancelled = $i->status == Reservation::STATUS_CONFIRMED &&  $i->returnReservation->status == Reservation::STATUS_CANCELLED;

                            if($return_direction_cancelled){

                                $transfer_desc = $i->returnReservation->pickupLocation->name.' -> '.$i->returnReservation->dropoffLocation->name;

                                $has_cancellation = true;

                                $priceEur = $i->getPrice();

                                $this->totalEur = $this->totalEur->subtract($priceEur->getMoney());

                                $status = 'Storno';

                                $inv = $priceEur->multiply($partnerCommissionMultiplier)->getMoney();

                                $total_comm = $priceEur->subtract($priceEur->multiply($partnerCommissionMultiplier));

                                $this->totalCommission = $this->totalCommission->subtract($total_comm);

                                $invEur = \Cknow\Money\Money::EUR($inv->getAmount());

                                $pdv = $i->total_commission_amount->multiply($partnerCommissionMultiplier);
                                $pdv = \Cknow\Money\Money::EUR($pdv->getAmount());

                                $invoice_data = \DB::table('invoices')->where('reservation_id','=',$i->id)->first();

                                $net_profit = $i->total_commission_amount->subtract($pdv)->getMoney();

                                $net_profit = \Cknow\Money\Money::EUR($net_profit->getAmount());

                                $this->totalNetProfit = $this->totalNetProfit->add($net_profit);

                                $invoice_number = '-';

                                if(!empty($invoice_data)) {
                                    $invoice_number = $i->getInvoiceData('invoice_number');
                                }


                                $net_profit = $net_profit->negative();
                                $priceEur = $priceEur->negative();
                                $total_comm = $total_comm->negative();
                                $invEur = $invEur->negative();
                                $pdv = $pdv->negative();

                                $this->totalInvoiceCharge = $this->totalInvoiceCharge->add($invEur);
                                $this->totalPDV = $this->totalPDV->add($pdv);

                                $return[] =  [
                                    'id' => $i->id,
                                    'name' => $i->leadTraveller?->full_name,
                                    'date_time' => $i->date_time?->format('d.m.Y'),
                                    'partner' => $i->partner->name,
                                    'adults' => $i->adults,
                                    'children' => $i->children,
                                    'infants' => $i->infants,
                                    'transfer' => $i->transfer?->name,
                                    'vehicle' => $i->transfer?->vehicle?->type,
                                    'status' => $i->status,
                                    'price_eur' => $priceEur->formatByDecimal(),
                                    'round_trip' => $i->is_round_trip,
                                    'round_trip_date' => $i->returnReservation?->date_time?->format('d.m.Y @ H:i'),
                                    'voucher_date' => $i->created_at->format('d.m.Y'),
                                    'tax_level'=>  $i->getRouteTransferTaxLevel(),
                                    'commission'=>  \Arr::get($i->transfer_price_state,'price_data.commission'),
                                    'commission_amount'=>  $invEur->formatByDecimal(),
                                    'net_income' => $net_profit->formatByDecimal(),
                                    'invoice_charge' =>  $total_comm->formatByDecimal(),
                                    'invoice_number' => (string) $invoice_number,
                                    'pdv' => $pdv->formatByDecimal(),
                                    'procedure' => $status,
                                    'selling_place' => $selling_place,
                                    'sales_agent' => $sales_agent,
                                    'description' => $transfer_desc
                                ];
                            }

                            $main_direction_cancelled = $i->status == Reservation::STATUS_CANCELLED &&  $i->returnReservation->status == Reservation::STATUS_CONFIRMED;

                            if($main_direction_cancelled){

                                $transfer_desc = $i->pickupLocation->name.' -> '.$i->dropoffLocation->name;

                                $has_cancellation = true;

                                $priceEur = $i->getPrice();

                                $this->totalEur = $this->totalEur->subtract($priceEur->getMoney());

                                $status = 'Storno';

                                $inv = $priceEur->multiply($partnerCommissionMultiplier)->getMoney();

                                $total_comm = $priceEur->subtract($priceEur->multiply($partnerCommissionMultiplier));

                                $this->totalCommission = $this->totalCommission->subtract($total_comm);

                                $invEur = \Cknow\Money\Money::EUR($inv->getAmount());


                                $pdv = $i->total_commission_amount->multiply($partnerCommissionMultiplier);
                                $pdv = \Cknow\Money\Money::EUR($pdv->getAmount());

                                $this->totalPDV = $this->totalPDV->add($pdv);

                                $invoice_data = \DB::table('invoices')->where('reservation_id','=',$i->id)->first();

                                $net_profit = $i->total_commission_amount->subtract($pdv)->getMoney();

                                $net_profit = \Cknow\Money\Money::EUR($net_profit->getAmount());
                                $this->totalNetProfit = $this->totalNetProfit->add($net_profit);
                                $invoice_number = '-';

                                if(!empty($invoice_data)) {
                                    $invoice_number = $i->getInvoiceData('invoice_number');
                                }

                                $net_profit = $net_profit->negative();
                                $priceEur = $priceEur->negative();
                                $total_comm = $total_comm->negative();
                                $invEur = $invEur->negative();
                                $pdv = $pdv->negative();

                                $this->totalInvoiceCharge = $this->totalInvoiceCharge->add($invEur);

                                $return[] =  [
                                    'id' => $i->id,
                                    'name' => $i->leadTraveller?->full_name,
                                    'date_time' => $i->date_time?->format('d.m.Y'),
                                    'partner' => $i->partner->name,
                                    'adults' => $i->adults,
                                    'children' => $i->children,
                                    'infants' => $i->infants,
                                    'transfer' => $i->transfer?->name,
                                    'vehicle' => $i->transfer?->vehicle?->type,
                                    'status' => $i->status,
                                    'price_eur' => $priceEur->formatByDecimal(),
                                    'round_trip' => $i->is_round_trip,
                                    'round_trip_date' => $i->returnReservation?->date_time?->format('d.m.Y @ H:i'),
                                    'voucher_date' => $i->created_at->format('d.m.Y'),
                                    'tax_level'=>  $i->getRouteTransferTaxLevel(),
                                    'commission'=>  \Arr::get($i->transfer_price_state,'price_data.commission'),
                                    'commission_amount'=>  $invEur->formatByDecimal(),
                                    'net_income' => $net_profit->formatByDecimal(),
                                    'invoice_charge' =>  $total_comm->formatByDecimal(),
                                    'invoice_number' => (string) $invoice_number,
                                    'pdv' => $pdv->formatByDecimal(),
                                    'procedure' => $status,
                                    'selling_place' => $selling_place,
                                    'sales_agent' => $sales_agent,
                                    'description' => $transfer_desc
                                ];
                            }

                        }
                    }

                    ##Check For Cancellation Fee
                    if($has_cancellation){

                        if($i->status == Reservation::STATUS_CANCELLED && $i->hasCancellationFee()){

                            $invoice_number = $i->getInvoiceData('invoice_number','cancellation_fee');

                            $status = 'CF';

                            $priceEur = \Cknow\Money\Money::EUR($i->getCancellationFeeAmount(true)*100);

                            $this->totalEur = $this->totalEur->add($priceEur->getMoney());

                            $invEur = $priceEur->multiply($partnerCommissionMultiplier);


                            $this->totalInvoiceCharge = $this->totalInvoiceCharge->add($invEur);

                            $total_comm = $priceEur->subtract($invEur->getMoney());
                            $this->totalCommission = $this->totalCommission->add($total_comm->getMoney());

                            $transfer_desc = $i->pickupLocation->name.' -> '.$i->dropoffLocation->name;

                            $return[] =  [
                                'id' => $i->id,
                                'name' => $i->leadTraveller?->full_name,
                                'date_time' => $i->date_time?->format('d.m.Y'),
                                'partner' => $i->partner->name,
                                'adults' => $i->adults,
                                'children' => $i->children,
                                'infants' => $i->infants,
                                'transfer' => $i->transfer?->name,
                                'vehicle' => $i->transfer?->vehicle?->type,
                                'status' => $i->status,
                                'price_eur' => $priceEur->formatByDecimal(),
                                'round_trip' => $i->is_round_trip,
                                'round_trip_date' => $i->returnReservation?->date_time?->format('d.m.Y @ H:i'),
                                'voucher_date' => $i->created_at->format('d.m.Y'),
                                'tax_level'=>  $i->getRouteTransferTaxLevel(),
                                'commission'=>  \Arr::get($i->transfer_price_state,'price_data.commission'),
                                'commission_amount'=>  $invEur->formatByDecimal(),
                                'net_income' => $net_profit->formatByDecimal(),
                                'invoice_charge' =>  $total_comm->formatByDecimal(),
                                'invoice_number' => (string) $invoice_number,
                                'pdv' => $pdv,
                                'procedure' => $status,
                                'selling_place' => $selling_place,
                                'sales_agent' => $sales_agent,
                                'description' => $transfer_desc
                            ];
                        }

                        if($i->isRoundTrip()){

                            if($i->returnReservation->status == Reservation::STATUS_CANCELLED && $i->returnReservation->hasCancellationFee()){

                                $invoice_number = $i->returnReservation->getInvoiceData('invoice_number','cancellation_fee');

                                $priceEur = \Cknow\Money\Money::EUR($i->returnReservation->getCancellationFeeAmount(true)*100);

                                $this->totalEur = $this->totalEur->add($priceEur->getMoney());

                                $status = 'CF';

                                $invEur = $priceEur->multiply($partnerCommissionMultiplier);
                                $this->totalInvoiceCharge = $this->totalInvoiceCharge->add($invEur);

                                $total_comm = $priceEur->subtract($invEur->getMoney());

                                $this->totalCommission = $this->totalCommission->add($total_comm->getMoney());

                                $transfer_desc = $i->returnReservation->pickupLocation->name.' -> '.$i->returnReservation->dropoffLocation->name;

                                $return[] =  [
                                    'id' => $i->id,
                                    'name' => $i->leadTraveller?->full_name,
                                    'date_time' => $i->date_time?->format('d.m.Y'),
                                    'partner' => $i->partner->name,
                                    'adults' => $i->adults,
                                    'children' => $i->children,
                                    'infants' => $i->infants,
                                    'transfer' => $i->transfer?->name,
                                    'vehicle' => $i->transfer?->vehicle?->type,
                                    'status' => $i->status,
                                    'price_eur' => $priceEur->formatByDecimal(),
                                    'round_trip' => $i->is_round_trip,
                                    'round_trip_date' => $i->returnReservation?->date_time?->format('d.m.Y @ H:i'),
                                    'voucher_date' => $i->created_at->format('d.m.Y'),
                                    'tax_level'=>  $i->getRouteTransferTaxLevel(),
                                    'commission'=>  \Arr::get($i->transfer_price_state,'price_data.commission'),
                                    'commission_amount'=>  $invEur->formatByDecimal(),
                                    'net_income' => $net_profit->formatByDecimal(),
                                    'invoice_charge' =>  $total_comm->formatByDecimal(),
                                    'invoice_number' => (string) $invoice_number,
                                    'pdv' => $pdv,
                                    'procedure' => $status,
                                    'selling_place' => $selling_place,
                                    'sales_agent' => $sales_agent,
                                    'description' => $transfer_desc
                                ];
                            }
                        }
                    }


                    return $return;

                })->toArray();

        $this->totalEur = \Cknow\Money\Money::fromMoney($this->totalEur)->formatByDecimal();



        $total_comm = $this->totalInvoiceCharge->formatByDecimal();
        $total_inv_charge = $this->totalCommission->formatByDecimal();
        $this->totalCommission = $total_comm;
        $this->totalInvoiceCharge = $total_inv_charge;
        $this->totalPDV = $this->totalPDV->formatByDecimal();
        $this->totalNetProfit = $this->totalNetProfit->formatByDecimal();
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

        return $partners->toArray();
    }

    public function exportToExcel(){

        $destination = Destination::withoutGlobalScopes()->find($this->destination)?->name;
        $partner = Partner::find($this->partner)?->name;
        $owner = \Auth::user()->owner->name;

        $export = new DestinationExport($this->filteredReservations,$this->reportType);

        switch ($this->reportType){
            case 'partner-report':
                $export->setFilterData([

                    Carbon::make($this->dateFrom)->format('d.m.Y'),
                    Carbon::make($this->dateTo)->format('d.m.Y'),
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    $this->totalEur,
                    $this->totalInvoiceCharge,
                    $this->totalCommission,
                    '',
                    '',
                ]);
                break;
            case 'ppom-report':
                $export->setFilterData([

                    Carbon::make($this->dateFrom)->format('d.m.Y'),
                    Carbon::make($this->dateTo)->format('d.m.Y'),
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    $this->totalEur,
                    '',
                    $this->totalInvoiceCharge,
                    $this->totalCommission,
                    $this->totalPDV,
                    $this->totalNetProfit,
                ]);
                break;
            case 'rpo-report':
                $export->setFilterData([

                    Carbon::make($this->dateFrom)->format('d.m.Y'),
                    Carbon::make($this->dateTo)->format('d.m.Y'),
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    $this->totalEur,
                    $this->totalCommission,
                    $this->totalInvoiceCharge,
                    '',
                    '',
                ]);
                break;
            default:
                $export->setFilterData([

                    Carbon::make($this->dateFrom)->format('d.m.Y'),
                    Carbon::make($this->dateTo)->format('d.m.Y'),
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    $this->totalEur,
                    $this->totalInvoiceCharge,
                    $this->totalCommission,
                    '',
                    '',

                ]);
        }



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
