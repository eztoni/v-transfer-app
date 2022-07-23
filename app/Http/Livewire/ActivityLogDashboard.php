<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;
use WireUi\Traits\Actions;

class ActivityLogDashboard extends Component
{
use Actions;

    use WithPagination;

    public $activity;
    public $search = '';
    public $activityModal;


    public function openActivityModal($activityId){
        $this->activity = Activity::find($activityId);
        $this->activityModal = true;
    }

    public function closeActivityModal(){
        $this->activityId = null;
        $this->activityModal = false;
    }

    public function render()
    {

        $activities = Activity::search('subject_type',$this->search)->paginate(10);
        return view('livewire.activity-log-dashboard', compact('activities'));

    }
}
