<?php

namespace App\Http\Livewire;

use App\Mail\ConfirmationMail;
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
use Illuminate\Support\Facades\Mail;
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
        'stepTwoFields.leadTraveller.title' => ' title',
        'stepTwoFields.leadTraveller.firstName' => ' first name',
        'stepTwoFields.leadTraveller.lastName' => ' last name',
        'stepTwoFields.leadTraveller.reservationNumber' => ' reservation number',
        'stepTwoFields.leadTraveller.email' => ' email',
        'stepTwoFields.leadTraveller.phone' => ' phone',
        'stepTwoFields.otherTravellers' => 'other travellers',
        'stepTwoFields.otherTravellers.*.firstName' => 'first name',
        'stepTwoFields.otherTravellers.*.lastName' => 'last naem',

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
            'stepOneFields.children' => 'required|numeric|integer|max:50',
            'stepOneFields.infants' => 'required|numeric|integer|max:50',
            'stepOneFields.luggage' => 'required|numeric|integer|max:50',

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
            'stepTwoFields.leadTraveller.title' => 'nullable|string',
            'stepTwoFields.leadTraveller.firstName' => 'required|string',
            'stepTwoFields.leadTraveller.lastName' => 'required|string',
            'stepTwoFields.leadTraveller.reservationNumber' => 'nullable|string',
            'stepTwoFields.leadTraveller.email' => 'nullable|string|email',
            'stepTwoFields.leadTraveller.phone' => 'required|string',
        ];

        if ($this->activateOtherTravellersInput) {
            $rules['stepTwoFields.otherTravellers'] = 'array|min:' . $this->getTotalPassengersProperty() - 1;
            $rules['stepTwoFields.otherTravellers.*.firstName'] = 'required|string';
            $rules['stepTwoFields.otherTravellers.*.lastName'] = 'required|string';
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
        'confirmationLanguage' => 'en',
        'sendMail' => 1,
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
    public $emailList = array();
    public $sendEmailArray = [
        0 => 'No',
        1 => 'Yes',
    ];

    public function getExtrasProperty()
    {
        return Extra::getExtrasByPartnerIdWithPrice($this->selectedPartner, ['media']);
    }

    public function getSelectedExtrasProperty()
    {
        return Extra::with(['partner' => function ($q) {
            $q->where('id', $this->selectedPartner);
        }])->findMany(collect($this->stepTwoFields['extras'])->reject(function ($item) {
            return $item === false;
        })->keys()->toArray());

    }

    public function goBack()
    {
        $this->stepTwoFields['extras'] = [];
        $this->activateExtras = false;
        $this->step = 1;
    }

    public function saveReservation()
    {

        $this->validate($this->stepTwoRules(), [], $this->fieldNames);


        $traveller = new Traveller();

        $traveller->first_name = $this->stepTwoFields['leadTraveller']['firstName'];
        $traveller->last_name = $this->stepTwoFields['leadTraveller']['lastName'];
        $traveller->email = $this->stepTwoFields['leadTraveller']['email'];
        $traveller->title = $this->stepTwoFields['leadTraveller']['title'];
        $traveller->reservation_number = $this->stepTwoFields['leadTraveller']['reservationNumber'];
        $traveller->phone = $this->stepTwoFields['leadTraveller']['phone'];

        $route = $this->selectedRoute;

        $priceHandler = new \App\Services\TransferPrices($this->selectedTransfer,
            $this->selectedPartner,
            $this->roundTrip,
            $route ? $route->id : null,
            collect($this->stepTwoFields['extras'])->reject(function ($item) {
            return $item === false;
        })->keys()->toArray());

        $businessModel = new \App\BusinessModels\Reservation\Reservation(new \App\Models\Reservation());
        $businessModel->setRequiredAttributes(
            auth()->user()->destination_id,
            Carbon::make($this->stepOneFields['date']),
            Carbon::make($this->stepOneFields['time']),
            Point::find($this->stepOneFields['startingPointId'])->id,
            $this->stepOneFields['pickupAddress'],
            Point::find($this->stepOneFields['endingPointId'])->id,
            $this->stepOneFields['dropoffAddress'],
            $this->stepOneFields['adults'],
            $this->stepOneFields['children'],
            $this->stepOneFields['infants'],
            Partner::findOrFail($this->selectedPartner)->id,
            $priceHandler->getPrice(),
            $this->stepTwoFields['confirmationLanguage'],
            $this->selectedExtras,
            Transfer::findOrFail($this->selectedTransfer),
            $priceHandler->getPriceBreakdown(),
            $this->stepTwoFields['remark'] ?? '',
            $this->stepTwoFields['arrivalFlightNumber'] ?? '',
            $this->stepTwoFields['seats'],
            $this->stepOneFields['luggage'],

        );

        $businessModel->addLeadTraveller($traveller);

        if ($this->activateOtherTravellersInput) {
            foreach ($this->stepTwoFields['otherTravellers'] as $tr) {
                $traveller = new Traveller();

                $traveller->first_name = $tr['firstName'];
                $traveller->last_name = $tr['lastName'];
                $traveller->title = $tr['title'];

                $businessModel->addOtherTraveller($traveller, $tr['comment']);
            }
        }

        if ($this->roundTrip) {
            $businessModel->setRoundTrip(
                Carbon::make($this->stepOneFields['returnDate']),
                Carbon::make($this->stepOneFields['returnTime']),
                $this->stepTwoFields['departureFlightNumber'] ?? '',
            );
        }


        $id = $businessModel->saveReservation();

        if($this->stepTwoFields['sendMail'] == 1){

            /*
            $travellerMail = $this->stepTwoFields['leadTraveller']['email'];
            if($travellerMail ){
            $this->emailList = \Arr::add($this->emailList, 'travellerMail', $travellerMail);
            }

            $partnerMail = Partner::findOrFail($this->selectedPartner)->email;
            $this->emailList = \Arr::add($this->emailList, 'partnerMail', $partnerMail);

            $accommodationMail = Point::find($this->stepOneFields['endingPointId'])->reception_email;
            if($accommodationMail){
                $this->emailList = \Arr::add($this->emailList, 'accommodationMail', $accommodationMail);
            }

            $this->sendConfirmationMail($this->emailList,$id);
            */
        }

        $this->showToast('Reservation saved');
        Redirect::route('reservation-details', $id);
    }


    /*
     * CLEAN
     */

    public function mount()
    {
        $this->stepOneFields['destinationId'] = \Auth::user()->destination_id;


        $this->initiateFields();

    }

    public function sendConfirmationMail($userEmails = array(),$resId){
        Mail::to($userEmails)->send(new ConfirmationMail($resId));
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


        $this->validateOnly($property, array_merge($this->stepOneRules(), $this->stepTwoRules()), [], $this->fieldNames);
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

    public function getConfirmationLanguagesArrayProperty()
    {
        return Reservation::CONFIRMATION_LANGUAGES;
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
        $route = $this->selectedRoute;
        $priceCalculator = new \App\Services\TransferPrices($this->selectedTransfer, $this->selectedPartner, $this->roundTrip, $route ? $route->id : null, collect($this->stepTwoFields['extras'])->reject(function ($item) {
            return $item === false;
        })->keys()->toArray());



        return $priceCalculator->getPrice();
    }


    public function getSelectedRouteProperty()
    {
        return Route::where('starting_point_id', $this->stepOneFields['startingPointId'])
            ->where('ending_point_id', $this->stepOneFields['endingPointId'])
            ->first();
    }

    public function getAvailableTransfersProperty()
    {

        $route = $this->selectedRoute;

        if (!$route) {
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
        $this->stepTwoFields['seats'][] = "0";
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
