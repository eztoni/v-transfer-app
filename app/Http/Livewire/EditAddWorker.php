<?php

namespace App\Http\Livewire;

use App\Models\Worker;
use Carbon\Carbon;
use Livewire\Component;

class EditAddWorker extends Component
{
    public $worker;
    public $editData =[];
    public $workerId;

    protected $rules = [
        'editData.name' => 'required',
        'editData.surname' => 'required',
        'editData.city' => 'required',
        'editData.email' => 'required|email',
        'editData.OIB' => 'required|min:11',
        'editData.phone_number' => 'required',
    ];


    public function mount($id=''){
        $this->workerId = $id;
        if(!empty($this->workerId)){

            $this->worker = Worker::findOrFail($this->workerId);
            $this->editData = $this->worker->only([
                'name',
                'surname',
                'email',
                'city',
                'OIB',
                'employment_date',
                'phone_number',
            ]);
        }else{
            $this->worker = new Worker();
        }

    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function saveWorkerData(){
        $this->validate();
        $this->worker->fill($this->editData);
        $this->worker->save();
        $this->showToast('Spremljeno','Radnik Spremljen','success');

    }

    public function render()
    {
        return view('livewire.edit-add-worker');
    }
}
