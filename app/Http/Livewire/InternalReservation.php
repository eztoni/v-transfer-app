<?php

namespace App\Http\Livewire;

use App\Models\Destination;
use App\Models\Partner;
use App\Models\Point;
use App\Models\Reservation;
use App\Models\Route;
use App\Models\Transfer;
use App\Models\Traveller;
use App\Services\TransferAvailability;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;
use function Clue\StreamFilter\fun;

class InternalReservation extends Component
{
    //TODO: REMOVE HARDCODE FOR TESTING
    public $stepOneFields = [
        'destinationId' => null,
        'startingPointId' => null,
        'endingPointId' => null,
        'date' => null,
        'time' => null,
        'returnDate' => null,
        'returnTime' => null,
        'dropoffAddress' => null,
        'pickupAddress' => null,
        'adults' => 1,
        'children' => 0,
        'infants' => 0,
        'luggage' => 1,
    ];

    public $resSaved = false;

    public bool $twoWay = false;
    public int $step = 1;
    public $selectedTransfer = null;
    public $selectedPartner = null;

    public $stepTwoFields = [
        'arrivalFlightNumber' => null,
        'arrivalDate' => null,
        'timeOfArrival' => null,
        'pickupTimeArrival' => null,


        'departureFlightNumber' => null,
        'departureDate' => null,
        'timeOfDeparture' => null,
        'pickupTimeDeparture' => null,


        'remark' => null,

        'leadTraveller' => [
            'title' => null,
            'firstName' => null,
            'lastName' => null,
            'reservationNumber' => null,
            'email' => null,
            'phone' => null,
        ],

        'otherTravellers' => [

        ],
        'adults' => 1,
        'children' => 0,
        'infants' => 0,
        'luggage' => 1,
    ];

    public function saveReservation()
    {
        $reservation = new \App\BusinessModels\Reservation\Reservation(new \App\Models\Reservation());

        $traveller = new Traveller();

        $traveller->first_name = $this->stepTwoFields['leadTraveller']['firstName'];
        $traveller->last_name = $this->stepTwoFields['leadTraveller']['lastName'];

        $route = $this->getSelectedRouteProperty();

        $priceHandler =  (new \App\Services\TransferPrices())
            ->setPartnerId($this->selectedPartner)
            ->setTransferId($this->selectedTransfer)
            ->setRouteId($route ? $route->id : null);

        $reservation->setDate(Carbon::make($this->stepOneFields['date']))
            ->setTime(Carbon::make($this->stepOneFields['time']))
            ->setPickupLocation(Point::find($this->stepOneFields['startingPointId']))
            ->setDropoffLocation(Point::find($this->stepOneFields['endingPointId']))
            ->setPickupAddress($this->stepOneFields['pickupAddress'])
            ->setDropoffAddress($this->stepOneFields['dropoffAddress'])
            ->setAdults($this->stepOneFields['adults'])
            ->setInfants($this->stepOneFields['infants'])
            ->setChildren($this->stepOneFields['children'])
            ->setTransfer(Transfer::find($this->selectedTransfer))
            ->setPartner(Partner::find($this->selectedPartner))
            ->setLuggage($this->stepOneFields['luggage'])
            ->addLeadTraveller($traveller)
            ->setRouteObject($priceHandler->getRouteData())
            ->setPrice($priceHandler->getPrice()->getAmount());


        if ($this->twoWay) {
            $reservation->twoWay(
                Carbon::make($this->stepOneFields['returnDate']),
                Carbon::make($this->stepOneFields['returnTime'])
            );
        }

        $reservation->saveReservation();

        $this->showToast('Reservation saved');
        $this->resSaved=true;
        Redirect::route('reservation-view');


    }


    /*
     * CLEAN
     */

    public function mount()
    {
        $this->stepOneFields['destinationId'] = \Auth::user()->destination_id;
    }

    public function updated($property)
    {

        if (in_array($property, [
            'stepOneFields.adults',
            'stepOneFields.children',
            'stepOneFields.infants',
        ])) {
            $this->setOtherTravellers();
        }

    }

    public function setOtherTravellers()
    {
        $this->stepTwoFields['otherTravellers'] = [];
        for ($i = 0; $i < $this->getTotalPassengersProperty() - 1; $i++) {
            $this->stepTwoFields['otherTravellers'][] = [
                'title' => null,
                'firstName' => null,
                'lastName' => null,
                'comment' => null,
            ];
        }
    }


    //reset the points when we change destination

    public function updatedStepOneFieldsDestinationId()
    {
        $this->stepOneFields['startingPointId'] = $this->stepOneFields['endingPointId'] = '';
    }

    //reset the points when we change destination
    public function updatedStepOneFieldsStartingPointId()
    {
        if (!$this->getEndingPointsProperty()->contains(function ($item) {
            return $item->id === (int)$this->stepOneFields['endingPointId'];
        })) {
            $this->stepOneFields['endingPointId'] = '';
        }
    }

    public function getDestinationsWithRoutesProperty()
    {
        return Destination::whereHas('routes')->get();
    }

    public function getStartingPointsProperty()
    {
        return Route::query()
            ->where('destination_id', $this->stepOneFields['destinationId'])
            ->with('startingPoint')
            ->get()
            ->pluck('startingPoint');
    }

    public function getEndingPointsProperty()
    {

        return Route::query()
            ->with('endingPoint')
            ->where('destination_id', $this->stepOneFields['destinationId'])
            ->where('starting_point_id', $this->stepOneFields['startingPointId'])
            ->get()
            ->pluck('endingPoint');
    }


    public function getSelectedStartingPointProperty()
    {
        return Point::find($this->stepOneFields['startingPointId']);
    }

    public function getSelectedEndingPointProperty()
    {
        return Point::find($this->stepOneFields['endingPointId']);
    }

    public function getTotalPassengersProperty()
    {
        return $this->stepOneFields['adults'] + $this->stepOneFields['infants'] + $this->stepOneFields['children'];
    }

    public function getTotalPriceProperty()
    {
        $route = $this->getSelectedRouteProperty();

        return (new \App\Services\TransferPrices())
            ->setPartnerId($this->selectedPartner)
            ->setTransferId($this->selectedTransfer)
            ->setRouteId($route ? $route->id : null)
            ->getPrice();
    }

    public function getSelectedRouteProperty()
    {
        return Route::where('starting_point_id', $this->stepOneFields['startingPointId'])
            ->where('ending_point_id', $this->stepOneFields['endingPointId'])
            ->first();
    }

    public function getAvailableTransfersProperty()
    {

        $route = $this->getSelectedRouteProperty();

        if (!$route) {
            return collect([]);
        }

        return (new TransferAvailability())
            ->setAdults($this->stepOneFields['adults'])
            ->setChildren($this->stepOneFields['children'])
            ->setInfants($this->stepOneFields['infants'])
            ->setLuggage($this->stepOneFields['luggage'])
            ->setRoute($route)
            ->getAvailablePartnerTransfers();

    }

    /*
     * CLEAN
     */
    public $fakeData = [
        'title' => '',
        'fName' => '',
        'lName' => '',
        'resNum' => '',
        'email' => '',
        'phone' => '',
    ];


    public array $seats = [1];


    public function addSeat()
    {
        $this->seats[] = 1;
    }

    public function pullTraveller()
    {
        $this->fakeData = [
            'title' => 'Mr.',
            'fName' => 'John',
            'lName' => 'Doe',
            'resNum' => '3127863',
            'email' => 'john@doe.test',
            'phone' => '0959105570',
        ];
    }

    public function removeSeat()
    {
        array_pop($this->seats);
    }


    public function selectTransfer($transferId, $partnerId)
    {
        //Simple validation
        $transfer = Transfer::findOrFail($transferId);
        $partner = Partner::findOrFail($partnerId);



        $this->selectedTransfer = $transfer->id;
        $this->selectedPartner = $partner->id;

        $this->step = 2;
    }


    public function render()
    {
        return view('livewire.internal-reservation');
    }
}
