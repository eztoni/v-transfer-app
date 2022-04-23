<?php

namespace App\Http\Livewire;

use App\Models\Point;
use App\Models\Route;
use App\Models\Transfer;
use App\Services\TransferAvailability;
use Livewire\Component;

class InternalReservation extends Component
{

    public $stepOneFields = [
        'destinationId' => null,
        'startingPointId' => null,
        'endingPointId' => null,
        'dateTo'=> null,
        'timeTo'=>null,
        'dateFrom'=>null,
        'timeFrom'=>null,
        'seniors'=>0,
        'adults'=>1,
        'children'=>0,
        'infants'=>0,
        'luggage'=>1,
    ];
    public bool $twoWay = false;
    public int $step = 1;
    public int $selectedTransfer;


    /*
     * CLEAN
     */

    //reset the points when we change destination

    public function updatedStepOneFieldsDestinationId()
    {
        $this->stepOneFields['startingPointId'] = $this->stepOneFields['endingPointId'] = '';
    }
    //reset the points when we change destination
    public function updatedStepOneFieldsStartingPointId()
    {
        $this->stepOneFields['endingPointId'] = '';
    }

    public function getStartingPointsProperty()
    {
        return Point::query()
            ->where('destination_id',$this->stepOneFields['destinationId'])
            ->get();
    }

    public function getEndingPointsProperty()
    {

        return Route::query()
                ->with('endingPoint')
                ->where('destination_id',$this->stepOneFields['destinationId'])
                ->where('starting_point_id',$this->stepOneFields['startingPointId'])
                ->get()
                ->pluck('endingPoint') ;
    }


    public function getAvailableTransfersProperty(){

        $route = Route::where('starting_point_id',$this->stepOneFields['startingPointId'])
            ->where('ending_point_id',$this->stepOneFields['endingPointId'])
            ->first();

        if(!$route){
            return collect([]);
        }

        return (new TransferAvailability())
            ->setAdults($this->stepOneFields['adults'])
            ->setChildren($this->stepOneFields['children'])
            ->setSeniors($this->stepOneFields['seniors'])
            ->setInfants($this->stepOneFields['infants'])
            ->setLuggage($this->stepOneFields['luggage'])
            ->setRoute($route)
            ->getAvailablePartnerTransfers();

    }

    /*
     * CLEAN
     */
    public $fakeData = [
        'title'=>'',
        'fName'=>'',
        'lName'=>'',
        'resNum'=>'',
        'email'=>'',
        'phone'=>'',
    ];



    public array $travellers = [1];
    public array $seats = [1];

    public function addTraveller()
    {
        $this->travellers[] = 1;
    }

    public function removeTraveller()
    {
        array_pop($this->travellers);
    }

    public function addSeat()
    {
        $this->seats[] = 1;
    }
    public function pullTraveller()
    {
       $this->fakeData = [
           'title'=>'Mr.',
           'fName'=>'John',
           'lName'=>'Doe',
           'resNum'=>'3127863',
           'email'=>'john@doe.test',
           'phone'=>'0959105570',
       ];
    }
    public function removeSeat()
    {
        array_pop($this->seats);
    }



    public function selectTransfer($transferId)
    {
        //Simple validation
        $transfer = Transfer::findOrFail($transferId);

        //TODO: Validate

        $this->selectedTransfer = $transfer->id;

        $this->step = 2;
    }

    public function render()
    {
        return view('livewire.internal-reservation');
    }
}
