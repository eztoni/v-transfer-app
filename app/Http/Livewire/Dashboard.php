<?php
/**
 * Created by PhpStorm
 * User: Tin Modrić
 * Date: 10/13/2021
 */

namespace App\Http\Livewire;


use Carbon\Carbon;
use Debugbar;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;
use function Symfony\Component\String\b;

class Dashboard extends Component
{
    public $tasks;
    public $selectedWorkers = [];
    public $comment;

    public function testActivityLog(){
        activity()->log('LOUG!');
    }

    public function mount()
    {

    }

    public function render()
    {
        $activites = Activity::all();
        return view('livewire.dashboard', ['activites' => $activites]);
    }
}
