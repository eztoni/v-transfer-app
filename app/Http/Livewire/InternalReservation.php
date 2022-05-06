<?php

namespace App\Http\Livewire;

use App\Models\Destination;
use App\Models\Extra;
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
use function PHPUnit\Framework\arrayHasKey;

class InternalReservation extends Component
{
    public $stepOneFields = [
        'destinationId' => null,
        'startingPointId' => null,
        'endingPointId' => null,
        'dropoffAddress' => null,
        'pickupAddress' => null,
        'date' => null,
        'time' => null,
        'returnDate' => null,
        'returnTime' => null,
        'adults' => 1,
        'children' => 0,
        'infants' => 0,
        'luggage' => 1,
    ];
//    USE FOR TEST
//    public $stepOneFields = [
//        "destinationId" => 1,
//        "startingPointId" => "11",
//        "endingPointId" => "20",
//        "dropoffAddress" => "
//        Lake Mittie 999 Boyle Ridge Apt. 418\n
//        North Maximillia, HI 24454-0370
//        ",
//        "pickupAddress" => "
//        Nathanmouth 843 Jaskolski Crest\n
//        Gibsonfurt, AL 79408-5409
//        ",
//        "date" => "04.05.2022",
//        "time" => "21:00",
//        "returnDate" => "05.05.2022",
//        "returnTime" => "16:00",
//        "adults" => "3",
//        "children" => "2",
//        "infants" => 0,
//        "luggage" => 1,
//    ];
    public $fieldNames = [
        'stepOneFields.destinationId' => 'destination',
        'stepOneFields.startingPointId' => 'pickup location',
        'stepOneFields.endingPointId' => 'dropoff location',
        'stepOneFields.dropoffAddress' => 'dropoff address',
        'stepOneFields.pickupAddress' => 'pickup address',
        'stepOneFields.date' => 'date',
        'stepOneFields.time' => 'time',
        'stepOneFields.returnDate' => 'round trip date',
        'stepOneFields.returnTime' => 'round trip time',
        'stepOneFields.adults' => 'adults',
        'stepOneFields.children' => 'children',
        'stepOneFields.infants' => 'infants',
        'stepOneFields.luggage' => 'luggage',
        'stepTwoFields.arrivalFlightNumber' => 'arrival flight number',
        'stepTwoFields.departureFlightNumber' => 'departure flight number',
        'stepTwoFields.remark' => 'remark',
        'stepTwoFields.leadTraveller.title'=>' title',
        'stepTwoFields.leadTraveller.firstName'=>' first name',
        'stepTwoFields.leadTraveller.lastName'=>' last name',
        'stepTwoFields.leadTraveller.reservationNumber'=>' reservation number',
        'stepTwoFields.leadTraveller.email'=>' email',
        'stepTwoFields.leadTraveller.phone'=>' phone',
        'stepTwoFields.otherTravellers'=>'other travellers',
        'stepTwoFields.otherTravellers.*.firstName'=>'first name',
        'stepTwoFields.otherTravellers.*.lastName'=>'last naem',

    ];

    private function stepOneRules()
    {
        $rules = [
            'stepOneFields.destinationId' => 'required',
            'stepOneFields.startingPointId' => 'required',
            'stepOneFields.endingPointId' => 'required',
            'stepOneFields.dropoffAddress' => 'required|string|min:3',
            'stepOneFields.pickupAddress' => 'required|string|min:3',
            'stepOneFields.date' => 'required|date|after_or_equal:' . Carbon::now()->format('d.m.Y') . '|date_format:d.m.Y',
            'stepOneFields.time' => 'required|date_format:H:i',
            'stepOneFields.adults' => 'required|integer|integer|min:1|max:50',
            'stepOneFields.children' => 'numeric|integer|max:50',
            'stepOneFields.infants' => 'numeric|integer|max:50',
            'stepOneFields.luggage' => 'numeric|integer|max:50',

        ];
        if ($this->roundTrip) {
            $rules['stepOneFields.returnDate'] = 'date|date_format:d.m.Y|after_or_equal:stepOneFields.date';
            $rules['stepOneFields.returnTime'] = 'date_format:H:i';

            if ($this->stepOneFields['date'] && $this->stepOneFields['returnDate']) {

                if (Carbon::make($this->stepOneFields['date'])->eq($this->stepOneFields['returnDate'])) {
                    $rules['stepOneFields.returnTime'] .= '|after:stepOneFields.time';
                }
            }
        }
        return $rules;
    }



    private function stepTwoRules()
    {
         $rules = [
            'stepTwoFields.arrivalFlightNumber' => 'nullable|string',
            'stepTwoFields.departureFlightNumber' => 'nullable|string',
            'stepTwoFields.remark' => 'nullable|string',
            'stepTwoFields.leadTraveller.title'=>'nullable|string',
            'stepTwoFields.leadTraveller.firstName'=>'required|string',
            'stepTwoFields.leadTraveller.lastName'=>'required|string',
            'stepTwoFields.leadTraveller.reservationNumber'=>'nullable|string',
            'stepTwoFields.leadTraveller.email'=>'nullable|string|email',
            'stepTwoFields.leadTraveller.phone'=>'required|string',
        ];

         if($this->activateOtherTravellersInput){
             $rules['stepTwoFields.otherTravellers']= 'array|min:'.$this->getTotalPassengersProperty()-1;
             $rules['stepTwoFields.otherTravellers.*.firstName']= 'required|string';
             $rules['stepTwoFields.otherTravellers.*.lastName']= 'required|string';
         }

        return $rules;
    }

    public $stepTwoFields = [
        'arrivalFlightNumber' => null,
        'departureFlightNumber' => null,
        'remark' => null,

        'leadTraveller' => [
            'title' => null,
            'firstName' => null,
            'lastName' => null,
            'reservationNumber' => null,
            'email' => null,
            'phone' => null,
        ],
        'seats' => [

        ],
        'extras' => [

        ],
        'otherTravellers' => [

        ],
    ];


    public $resSaved = false;

    public bool $roundTrip = false;
    public int $step = 1;
    public $selectedTransfer = 1;
    public $selectedPartner = 1;
    public $activateOtherTravellersInput = false;
    public $activateChildSeats = false;
    public $activateExtras = false;

    public function getExtrasProperty(){
        return Extra::getExtrasByPartnerIdWithPrice($this->selectedPartner);
    }


    public function saveReservation()
    {

        $this->validate($this->stepTwoRules(),[],$this->fieldNames);

        $reservation = new \App\Models\Reservation();


        $traveller = new Traveller();

        $traveller->first_name = $this->stepTwoFields['leadTraveller']['firstName'];
        $traveller->last_name = $this->stepTwoFields['leadTraveller']['lastName'];
        $traveller->email = $this->stepTwoFields['leadTraveller']['email'];
        $traveller->title = $this->stepTwoFields['leadTraveller']['title'];
        $traveller->reservation_number = $this->stepTwoFields['leadTraveller']['reservationNumber'];
        $traveller->phone = $this->stepTwoFields['leadTraveller']['phone'];

        $route = $this->getSelectedRouteProperty();

        $priceHandler = (new \App\Services\TransferPrices())
            ->setPartnerId($this->selectedPartner)
            ->setTransferId($this->selectedTransfer)
            ->setRouteId($route ? $route->id : null);


        $reservation->date = Carbon::make($this->stepOneFields['date']);
        $reservation->time = Carbon::make($this->stepOneFields['time']);
        $reservation->pickup_location = Point::find($this->stepOneFields['startingPointId'])->id;
        $reservation->pickup_address = $this->stepOneFields['pickupAddress'];
        $reservation->dropoff_location = Point::find($this->stepOneFields['endingPointId'])->id;
        $reservation->dropoff_address = $this->stepOneFields['dropoffAddress'];
        $reservation->adults = $this->stepOneFields['adults'];
        $reservation->children = $this->stepOneFields['children'];
        $reservation->infants = $this->stepOneFields['infants'];

        $reservation->luggage = $this->stepOneFields['luggage'];
        $reservation->route = json_encode((array)$priceHandler->getRouteData());
        $reservation->transfer = Transfer::findOrFail($this->selectedTransfer)->toJson();
        $reservation->partner_id = Partner::findOrFail($this->selectedPartner)->id;
        $reservation->price = $priceHandler->getPrice()->getAmount();

        $reservation->round_trip = $this->roundTrip;

        $businessModel = new \App\BusinessModels\Reservation\Reservation($reservation);

        $businessModel->addLeadTraveller($traveller);

        if($this->activateOtherTravellersInput){
            foreach ($this->stepTwoFields['otherTravellers'] as $tr){
                $traveller = new Traveller();

                $traveller->first_name = $tr['firstName'];
                $traveller->last_name =$tr['lastName'];
                $traveller->title = $tr['title'];

                $businessModel->addOtherTraveller($traveller,$tr['comment']);
            }
        }


        if ($this->roundTrip) {
            $businessModel->roundTrip(
                Carbon::make($this->stepOneFields['returnDate']),
                Carbon::make($this->stepOneFields['returnTime'])
            );
        }


       $id= $businessModel->saveReservation();
        $this->showToast('Reservation saved');
        Redirect::route('reservation-view',['id'=>$id]);
    }


    /*
     * CLEAN
     */

    public function mount()
    {
        $this->stepOneFields['destinationId'] = \Auth::user()->destination_id;


        $this->initiateFields();

    }

    private function initiateFields()
    {
        $this->stepOneFields['date'] = Carbon::now()->format('d.m.Y');
        $this->stepOneFields['time'] = Carbon::now()->addHour()->setMinutes(0)->format('H:i');
    }


    public function updated($property)
    {

            if (in_array($property, [
                'stepOneFields.startingPointId',
                'stepOneFields.endingPointId',
            ])) {
                $this->resetAdresses();
            }

            if (in_array($property, [
                'stepOneFields.adults',
                'stepOneFields.children',
                'stepOneFields.infants',
            ])) {
                if (!\Arr::get($this->stepOneFields, explode('.', $property)[1])) {
                    \Arr::set($this->stepOneFields, explode('.', $property)[1], 0);
                }
                $this->setOtherTravellers();
            }


        $this->validateOnly($property, array_merge( $this->stepOneRules(),$this->stepTwoRules()), [], $this->fieldNames);
    }

    public function resetAdresses()
    {
        $this->stepOneFields['pickupAddress'] = $this->stepOneFields['dropoffAddress'] = '';
        $this->pickupAddressPointId = $this->dropoffAddressPointId = null;
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
            ->pluck('startingPoint')
            ->unique();
    }

    public function getEndingPointsProperty()
    {

        return Route::query()
            ->with('endingPoint')
            ->where('destination_id', $this->stepOneFields['destinationId'])
            ->where('starting_point_id', $this->stepOneFields['startingPointId'])
            ->get()
            ->pluck('endingPoint')
            ->unique();
    }

    public function getPickupAddressPointsProperty()
    {
        return Point::query()
            ->whereNotIn('id', [
                (int)$this->stepOneFields['startingPointId'],
                (int)$this->stepOneFields['endingPointId'],
                (int)$this->dropoffAddressPointId
            ])
            ->get();
    }

    public function getDropoffAddressPointsProperty()
    {
        return Point::query()
            ->whereNotIn('id', [
                (int)$this->stepOneFields['startingPointId'],
                (int)$this->stepOneFields['endingPointId'],
                (int)$this->pickupAddressPointId
            ])
            ->get();

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
        return (int)$this->stepOneFields['adults'] + (int)$this->stepOneFields['infants'] + (int)$this->stepOneFields['children'];
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

        if(\Arr::hasAny($this->getErrorBag()->messages(),[
            'stepOneFields.adults',
            'stepOneFields.children',
            'stepOneFields.infants',
            'stepOneFields.luggage',
            ])){
            return collect([]);
        }


        return (new TransferAvailability(
            $this->stepOneFields['adults'],
            $route,
            $this->stepOneFields['children'],
            $this->stepOneFields['infants'],
            $this->stepOneFields['luggage'],
        ))
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




    public function addSeat()
    {
        $this->stepTwoFields['seats'][] = 0;
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
        array_pop($this->stepTwoFields['seats']);
    }


    public function selectTransfer($transferId, $partnerId)
    {

        $this->validate($this->stepOneRules(), [], $this->fieldNames);

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
