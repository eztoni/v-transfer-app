<?php

namespace App\Http\Livewire;

use App\Actions\Statistics\makeChartJsStatisticsArray;
use App\Models\PastTask;
use App\Models\Task;
use Carbon\Carbon;
use Livewire\Component;

class Statistics extends Component
{

    public function buildDataSet($year=null){
        if(empty($year)){
            $year = Carbon::now();
            $parsedYear = Carbon::parse($year);
        }else{
            $parsedYear = Carbon::createFromFormat('Y',$year);
        }

        $startOfYear = $parsedYear->copy()->startOfYear();
        $endOfYear   = $parsedYear->copy()->endOfYear();

        $rawData = Task::forDateRange($startOfYear,$endOfYear);
        $pastTasks = PastTask::whereBetween('date_completed', [$startOfYear, $endOfYear])->whereNotIn(
            'task_id',
            Task::onlyTrashed()
                ->get()
                ->pluck('id')
                ->toArray())->get()->keyBy('id');
        $chartJsArrayFactory = new makeChartJsStatisticsArray($rawData,$pastTasks);
        return $chartJsArrayFactory->getChartJsArray();
    }

    public function render(){
        $this->buildDataSet();
        return view('livewire.statistics');
    }
}
