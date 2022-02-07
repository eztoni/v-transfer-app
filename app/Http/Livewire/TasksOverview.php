<?php
/**
 * Created by PhpStorm
 * User: Tin Modrić
 * Date: 10/7/2021
 */

namespace App\Http\Livewire;

use App\Models\Alarm;
use App\Models\Partner;
use App\Models\Task;
use Livewire\Component;

class TasksOverview extends Component
{

    public $partner;
    public $task;
    public $taskId;
    public $editTask;
    public $showForm = false;
    public $editData =['recurring_metrics' =>'day'];
    public $daysBefore = [];

    protected $rules = [
        'editData.name' => 'required|max:200',
        'editData.start_date' => 'required|date',
        'editData.end_date' => 'required|date',
        'editData.recurring_value' => 'required|numeric|min:1',
        'editData.recurring_metrics' => 'required',
    ];


    public function mount(Partner $partner){
        $this->partner = $partner;
        $this->partner->load(['tasks','tasks.alarm']);
        $this->loadTaskDaysBefore();
    }

    public function loadTaskDaysBefore(){
        foreach($this->partner->tasks as $task){
            if(!empty($task->alarm) ){
                $this->daysBefore[$task->id] = $task->alarm->before_offset;
            }
        }
    }
    public function softDelete($id){
        Task::find($id)->delete();
        $this->showToast('Posao Izbrisan','',);
        $this->partner = $this->partner->fresh();

    }
    public function render()
    {
        //Get Partner Tasks
        return view('livewire.tasks-overview');
    }
    public function addNewTask(){
        $this->resetForm();
        $this->showForm();
    }
    public function resetForm(){
        $this->editData =['recurring_metrics' =>'day'];
        $this->taskId = 0;
    }
    public function showForm(){
        $this->showForm = true;
    }

    public function closeForm(){
        $this->showForm = false;
    }

    public function updateTaskAlarmValue(Task $task){

        if((!empty($this->daysBefore[$task->id]) && is_numeric($this->daysBefore[$task->id])) || $this->daysBefore[$task->id] == 0 ){
            $alarm = $task->load('alarm')->alarm;
            if(empty($alarm)){
                $alarm = new Alarm(['task_id' => $task->id]);
            }
            $alarm->before_offset = $this->daysBefore[$task->id];
            if($task->recurring_metrics =='day'){
                if($task->recurring_value <= $this->daysBefore[$task->id]){
                    $this->addError('reccError'.$task->id,
                        'Vrijednost polja za alarm ne smije biti manje od vrijednosti ponavljanja posla.');
                    return false;
                }
            }
            $alarm->save();
            $this->showToast('Alarm ažuriran');
        }else{
            $this->showToast('Molim unesite ispravnu vrijednost!','','error');
            return false;
        }


    }

    public function saveOrUpdateTask(){
        $this->validate();

        if(empty($this->taskId)){
            $task = new Task();
            $task->partner_id = $this->partner->id;
        }else{
            $task =  $this->partner->tasks->find($this->taskId);
        }
        $task->fill($this->editData);
        $task->save();

        $this->partner = $this->partner->fresh();

        $this->loadTaskDaysBefore();

        $this->showToast('Spremljeno','Posao Spremljen','success');

        $this->closeForm();

    }

    public function editTask($taskId){
        $this->showForm();
        $this->taskId = $taskId;

        $this->editData = $this->partner->tasks->find($taskId)->only(['name',
            'price',
            'recurring_value',
            'recurring_metrics',
            'start_date',
            'end_date',
        ]);
    }

}
