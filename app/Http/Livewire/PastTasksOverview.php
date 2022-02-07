<?php
/**
 * Created by PhpStorm
 * User: Tin Modrić
 * Date: 10/21/2021
 */

namespace App\Http\Livewire;

use App\Models\PastTask;
use App\Models\Task;
use Livewire\Component;

class PastTasksOverview extends Component
{
    public function render()
    {
        $pastTasks = PastTask::with(['workers','partner','comment'])->whereNotIn(
            'task_id',
            Task::onlyTrashed()
                ->get()
                ->pluck('id')
                ->toArray())->orderByDesc('date_completed')->paginate(10);

        return view('livewire.past-tasks-overview',compact('pastTasks'));
    }
}
