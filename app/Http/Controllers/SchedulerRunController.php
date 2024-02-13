<?php

namespace App\Http\Controllers;

use App\BusinessModels\Reservation\Actions\CancelReservation;
use App\BusinessModels\Reservation\Actions\UpdateReservation;
use App\Events\ReservationAlertEvent;
use App\Models\Traveller;
use App\Services\Api\ValamarClientApi;
use App\Services\Api\ValamarOperaApi;
use App\Services\AzureStorageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use App\Models\Reservation;
use App\Models\Point;
use Illuminate\Support\Facades\Artisan;
use DB;

class SchedulerRunController extends Controller
{

    private $update_destinations = array(
        11
    );

    function __construct()
    {

    }

    public function update(){

        $azure = new AzureStorageController();
        $azure->uploadDocuments();

    }


}
