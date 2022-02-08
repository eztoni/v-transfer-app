<?php
/**
 * Created by PhpStorm
 * User: Tin Modrić
 * Date: 10/13/2021
 */

namespace App\Http\Livewire;


use App\Models\Destination;
use App\Scopes\CompanyScope;
use App\Scopes\DestinationScope;
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


    public function mount()
    {
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
