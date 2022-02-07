<?php
/**
 * Created by PhpStorm
 * User: Tin Modrić
 * Date: 10/13/2021
 */

namespace App\Http\Livewire;

use App\Models\Comment;
use App\Models\Partner;
use App\Models\PastTask;
use App\Models\PastTaskWorker;
use App\Models\Task;
use App\Models\Worker;
use Carbon\Carbon;
use Debugbar;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Livewire\Component;
use function Symfony\Component\String\b;

class Dashboard extends Component
{
    public $tasks;
    public $selectedWorkers = [];
    public $comment;


    public function render()
    {
        return view('livewire.dashboard');
    }
}
