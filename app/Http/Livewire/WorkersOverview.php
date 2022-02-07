<?php
/**
 * Created by PhpStorm
 * User: Tin Modrić
 * Date: 10/7/2021
 */

namespace App\Http\Livewire;

use App\Models\Worker;
use Livewire\Component;

class WorkersOverview extends Component
{
    public $search='';
    public function softDeleteWorker($workerId){
        Worker::find($workerId)->delete();
        $this->showToast('Radnik Izbrisan','',);
    }
    public function render()
    {
        $workers = Worker::search('name',$this->search)->paginate(10);
        return view('livewire.workers-overview',compact('workers'));
    }
}
