<?php

namespace App\Http\Livewire;

use App\Exports\AgentExport;
use App\Models\Destination;
use App\Models\Partner;
use App\Models\Point;
use App\Models\Reservation;
use App\Models\Route;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Money\Converter;
use Money\Currencies;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Exchange\SwapExchange;
use Money\Money;
use Swap\Laravel\Facades\Swap;
use WireUi\Traits\Actions;

class AgentEfficiency extends Component
{
    use Actions;

    public $dateFrom;
    public $dateTo;
    public $message = '';
    public $agent = 0;
    public $report_types = array(
        'creation_date' => 'By Creation Date',
        'transfer_date' => 'By Transfer Date'
    );

    public bool $isAgentReporting = false;
    public $totalEur = 0;

    public $report_type = 'creation_date';


    protected $rules = [
        'dateFrom' => 'required|date',
        'dateTo' => 'required|date|after_or_equal:dateFrom',
        'agent' => 'required|min:1',
    ];

    public array $filteredReservations = array();


    public function mount()
    {
        $this->isAgentReporting = true;
        $this->dateFrom = Carbon::now()->addDay()->format('d.m.Y');
        $this->dateTo = Carbon::now()->addDay()->format('d.m.Y');
    }

    public function updated($property)
    {
        if(Carbon::createFromFormat('d.m.Y',$this->dateTo)->format('Y-m-d') < Carbon::createFromFormat('d.m.Y',$this->dateFrom)->format('Y-m-d')){
            $this->dateTo = Carbon::createFromFormat('d.m.Y',$this->dateFrom)->addDay()->format('d.m.Y');
        }
    }

    public function getAgentProperty(){

        $agent_list = User::query();

        $agent_list = $agent_list->get()->filter(function ($i) {
          if($i->id > 3){
              return true;
          }

          return false;

        })->
        mapWithKeys(function ($i) {

                if($this->agent < 1){
                    $this->agent = $i->id;
                }

                return [$i->id => '#'.$i->id.' - '.$i->name.' ( '.$i->email.' )'];
            });

        return $agent_list->toArray();
    }

    public function getReportTypesProperty(){
        return $this->report_types;
    }


    public function render()
    {
        return view('livewire.agent-efficiency');
    }

    public function generate(){

        $this->message = '';
        $this->totalEur = 0;
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
            ->where('status',Reservation::STATUS_CONFIRMED)
            ->where('created_by',$this->agent);

        $query_param = 'date_time';

        if($this->report_type == 'creation_date'){
            $query_param = 'created_at';
        }



        $this->filteredReservations = $reservations->where(function ($q) use($date_from,$date_to,$query_param) {
            $q->where(function ($q) use($date_from,$date_to,$query_param){
                $q->whereDate($query_param, '>=', $date_from)
                    ->whereDate($query_param, '<=',  $date_to);
            })->orWHereHas('returnReservation',function ($q)use($date_from,$date_to,$query_param){
                $q->whereDate($query_param, '>=',  $date_from)
                    ->whereDate($query_param, '<=',  $date_to);
            });
        })->get()->map(function (Reservation $i) {


            $priceEur = $i->getPrice()->formatByDecimal();

            $this->totalEur = $this->totalEur + $priceEur;

            return [
                'id' => $i->id,
                'created_at' => $i->created_at->format('d.m.Y'),
                'round_trip' => $i->is_round_trip,
                'date_time' => $i->date_time->format('d.m.Y'),
                'price' => $priceEur,
                'partner' => $i->partner->name,
                'transfer' => $i->transfer?->name,
                'name' => $i->leadTraveller?->first()->full_name,
                'route' => $i->pickupAddress->name.' => '.$i->dropoffAddress->name
            ];
        })->toArray();


        $this->totalEur = number_format($this->totalEur,2,'.','');

        if($reservations->count() < 1){
            $this->message = 'No bookings for this agent for the selected  date range and parameters';
        }
    }

    public function exportToExcel(){


        $export = new AgentExport($this->filteredReservations);

        $export->setFilterData([
            Carbon::make($this->dateFrom)->format('d.m.Y'),
            Carbon::make($this->dateTo)->format('d.m.Y'),
            count($this->filteredReservations),
            $export->format_excel_price($this->totalEur),
        ]);

        $agent = User::findOrFail($this->agent);

        $file_name = preg_replace('! !','_',strtolower($agent->name)).'_'.Carbon::make($this->dateFrom)->format('d.m.Y').'-'.Carbon::make($this->dateTo)->format('d.m.Y');

        return Excel::download($export,"$file_name.xlsx");
    }
}
