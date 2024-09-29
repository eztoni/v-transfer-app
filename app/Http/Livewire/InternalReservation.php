<?php

namespace App\Http\Livewire;

use App\Mail\GuestConfirmationMail;
use App\Models\Destination;
use App\Models\Extra;
use App\Models\Partner;
use App\Models\Point;
use App\Models\Reservation;
use App\Models\Route;
use App\Models\Transfer;
use App\Models\Traveller;
use App\Services\TransferAvailability;
use App\Traits\ReservationDevTools;
use Barryvdh\Debugbar\Facades\Debugbar;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Livewire\Component;
use WireUi\Traits\Actions;
use function Clue\StreamFilter\fun;
use function PHPUnit\Framework\arrayHasKey;

class InternalReservation extends Component
{
    use Actions, ReservationDevTools;

    public $stepOneFields = [
        'destinationId' => null,
        'startingPointId' => null,
        'endingPointId' => null,
        'dropoffAddress' => null,
        'pickupAddress' => null,
        'pickupAddressId' => null,
        'dropoffAddressId' => null,
        'dateTime' => null,
        'returnDateTime' => null,
        'returnTime' => null,
        'adults' => 1,
        'children' => 0,
        'infants' => 0,
        'luggage' => 0,
        'rate_plan' => null
    ];


    public $fieldNames = [
        'stepOneFields.destinationId' => 'destination',
        'stepOneFields.startingPointId' => 'pickup location',
        'stepOneFields.endingPointId' => 'dropoff location',
        'stepOneFields.dropoffAddress' => 'dropoff address',
        'stepOneFields.pickupAddress' => 'pickup address',
        'stepOneFields.dateTime' => 'date & time',
        'stepOneFields.returnDateTime' => 'round trip date & time',
        'stepOneFields.returnTime' => 'round trip time',
        'stepOneFields.adults' => 'adults',
        'stepOneFields.rate_plan' => 'rate plan',
        'stepOneFields.children' => 'children',
        'stepOneFields.infants' => 'infants',
        'stepOneFields.luggage' => 'luggage',
        'stepTwoFields.arrivalFlightNumber' => 'arrival flight number',
        'stepTwoFields.departureFlightNumber' => 'departure flight number',
        'stepTwoFields.remark' => 'remark',
        'stepTwoFields.leadTraveller.firstName' => ' first name',
        'stepTwoFields.leadTraveller.lastName' => ' last name',
        'stepTwoFields.leadTraveller.reservationNumber' => ' reservation number',
        'stepTwoFields.leadTraveller.reservationOperaID' => ' reservation opera id',
        'stepTwoFields.leadTraveller.reservationOperaConfirmation' => ' opera confirmation number',
        'stepTwoFields.leadTraveller.email' => ' email',
        'stepTwoFields.leadTraveller.phone' => ' phone',
        'stepTwoFields.leadTraveller.check_in' => ' check in',
        'stepTwoFields.leadTraveller.check_out' => ' check out',
        'stepTwoFields.otherTravellers' => 'other travellers',
        'stepTwoFields.otherTravellers.*.firstName' => 'first name',
        'stepTwoFields.otherTravellers.*.lastName' => 'last name',
        'stepTwoFields.includedInAccommodationReservation' => 'included in reservation',
        'stepTwoFields.vlevelrateplanReservation' => 'v level rate plan reservation',
        'stepTwoFields.confirmationLanguage' => 'Confirmation language',
    ];

    private function stepOneRules()
    {
        $rules = [
            'stepOneFields.destinationId' => 'required',
            'stepOneFields.startingPointId' => 'required',
            'stepOneFields.endingPointId' => 'required',
            'stepOneFields.dropoffAddress' => 'required|string|min:3',
            'stepOneFields.pickupAddress' => 'required|string|min:3',
            'stepOneFields.dateTime' => 'required|date|after_or_equal:' . Carbon::now()->format('d.m.Y H:i'),
            'stepOneFields.adults' => 'required|integer|min:1|max:50',
            'stepOneFields.children' => 'required|numeric|integer|max:50',
            'stepOneFields.infants' => 'required|numeric|integer|max:50',
            'stepOneFields.luggage' => 'required|numeric|integer|max:50',
            'stepOneFields.rate_plan' => 'required'

        ];
        if ($this->roundTrip) {
            $rules['stepOneFields.returnDateTime'] = 'date|after_or_equal:stepOneFields.dateTime';
        }
        return $rules;
    }


    private function stepTwoRules()
    {
        $rules = [
            'stepTwoFields.remark' => 'nullable|string',
            'stepTwoFields.leadTraveller.firstName' => 'required|string',
            'stepTwoFields.leadTraveller.lastName' => 'required|string',
            'stepTwoFields.leadTraveller.reservationNumber' => 'nullable|string',
            'stepTwoFields.leadTraveller.reservationOperaID' => 'nullable|string',
            'stepTwoFields.leadTraveller.reservationOperaConfirmation' => 'nullable|string',
            'stepTwoFields.leadTraveller.email' => 'required|string|email',
            'stepTwoFields.leadTraveller.phone' => 'required|string',
            'stepTwoFields.leadTraveller.check_in' => 'required',
            'stepTwoFields.leadTraveller.check_out' => 'required',
            'stepTwoFields.includedInAccommodationReservation' => 'boolean',
            'stepTwoFields.vlevelrateplanReservation' => 'boolean',
            'stepTwoFields.confirmationLanguage' => 'required',
        ];

        if($this->roundTrip){
            if(is_numeric($this->stepOneFields['pickupAddressId']) && Point::find($this->stepOneFields['pickupAddressId'])->type == Point::TYPE_AIRPORT ||
                is_numeric($this->stepOneFields['dropoffAddressId']) && POINT::find($this->stepOneFields['dropoffAddressId'])->type == Point::TYPE_AIRPORT){
                $rules['stepTwoFields.arrivalFlightNumber'] = 'required|string';
                $rules['stepTwoFields.departureFlightNumber'] = 'required|string';
            }else{
                $rules['stepTwoFields.arrivalFlightNumber'] = 'nullable|string';
                $rules['stepTwoFields.departureFlightNumber'] = 'nullable|string';
            }
        }else{
            if(is_numeric($this->stepOneFields['pickupAddressId']) && Point::find($this->stepOneFields['pickupAddressId'])->type == Point::TYPE_AIRPORT ||
                is_numeric($this->stepOneFields['dropoffAddressId']) && POINT::find($this->stepOneFields['dropoffAddressId'])->type == Point::TYPE_AIRPORT){
                $rules['stepTwoFields.arrivalFlightNumber'] = 'required|string';
            }else{
                $rules['stepTwoFields.arrivalFlightNumber'] = 'nullable|string';
            }
        }

        if ($this->activateOtherTravellersInput) {
            $rules['stepTwoFields.otherTravellers'] = 'array';
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
            'firstName' => null,
            'lastName' => null,
            'reservationNumber' => null,
            'reservationOperaID' => null,
            'reservationOperaConfirmation' => null,
            'email' => null,
            'phone' => null,
            'check_in' => null,
            'check_out' => null,
        ],
        'confirmationLanguage' => 'en',
        'sendMail' => true,
        'seats' => [

        ],
        'extras' => [

        ],
        'otherTravellers' => [

        ],
        'includedInAccommodationReservation' => false,
        'vlevelrateplanReservation' => false
    ];


    public $pullDataFields = [
        'resId' => null,
        'fName' => null,
        'lName' => null,
        'dFrom' => null,
        'dTo' => null,
        'property' => null,

    ];

    public function pullDataRules()
    {
        return [
            'pullDataFields.resId' => 'required_without:pullDataFields.property',
            'pullDataFields.fName' => 'string|nullable',
            'pullDataFields.lName' => 'string|nullable',
            'pullDataFields.dFrom' => 'required_without:pullDataFields.resId|date|nullable',
            'pullDataFields.dTo' => 'required_without:pullDataFields.resId|date|nullable|sometimes|before:' . Carbon::createFromFormat('d.m.Y', $this->pullDataFields['dFrom'] ?: now()->format('d.m.Y'))->addYear()->format('d.m.Y'),
            'pullDataFields.property' => 'required_without:pullDataFields.resId',
        ];
    }

    private $pullDataFieldNames = [
        'pullDataFields.resId' => 'reservation code',
        'pullDataFields.fName' => 'first name',
        'pullDataFields.lName' => 'last name',
        'pullDataFields.dFrom' => 'date from',
        'pullDataFields.dTo' => 'date to',
        'pullDataFields.property' => 'property',
    ];

    public $apiData = [];
    public bool $pullModal = false;

    public $resSaved = false;

    public Reservation|null $reservationStatus = null;
    public bool $reservationStatusModal = false;

    public bool $reservationWarningModal = false;
    public bool $roundTrip = false;
    public int $step = 1;
    public $selectedTransfer = null;
    public $selectedPartner = null;
    public $activateOtherTravellersInput = false;
    public $activateChildSeats = false;
    public $activateExtras = false;
    public $emailList = array();
    public $completeReservation = 'Complete Reservation';
    public $duplicateBooking = false;
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

    public function updatedActivateExtras()
    {
        $this->stepTwoFields['extras'] = [];
    }

    public function updatedActivateOtherTravellers()
    {

    }

    public function saveReservation()
    {

        dd($this->stepTwoFields);

        $this->validate($this->stepTwoRules(), [], $this->fieldNames);

        $this->completeReservation = 'Saving Reservation ...';

        $traveller = new Traveller();

        $traveller->first_name = $this->stepTwoFields['leadTraveller']['firstName'];
        $traveller->last_name = $this->stepTwoFields['leadTraveller']['lastName'];
        $traveller->full_name = $traveller->first_name.' '.$traveller->last_name;
        $traveller->email = $this->stepTwoFields['leadTraveller']['email'];
        $traveller->reservation_number = $this->stepTwoFields['leadTraveller']['reservationNumber'];
        $traveller->reservation_opera_id = $this->stepTwoFields['leadTraveller']['reservationOperaID'];
        $traveller->reservation_opera_confirmation = $this->stepTwoFields['leadTraveller']['reservationOperaConfirmation'];

        $traveller->phone = $this->stepTwoFields['leadTraveller']['phone'];

        $check_in =  Carbon::make($this->stepTwoFields['leadTraveller']['check_in']);
        $check_out =  Carbon::make($this->stepTwoFields['leadTraveller']['check_out']);

        $traveller->reservation_check_in = $check_in->format('Y-m-d');
        $traveller->reservation_check_out = $check_out->format('Y-m-d');

        $route = $this->selectedRoute;

        $priceHandler = (new \App\Services\TransferPriceCalculator($this->selectedTransfer,
            $this->selectedPartner,
            $this->roundTrip,
            $route ? $route->id : null,
            collect($this->stepTwoFields['extras'])->reject(function ($item) {
                return $item === false;
            })->keys()->toArray()))
            ->setBreakdownLang($this->stepTwoFields['confirmationLanguage']);

        $businessModel = new \App\BusinessModels\Reservation\Actions\CreateReservation(new \App\Models\Reservation());
        $businessModel->setRequiredAttributes(
            auth()->user()->destination_id,
            Carbon::createFromFormat('d.m.Y H:i', $this->stepOneFields['dateTime']),
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
            $this->stepOneFields['pickupAddressId'],
            $this->stepOneFields['dropoffAddressId'],
            $this->stepTwoFields['includedInAccommodationReservation'],
            $this->stepOneFields['rate_plan'],
            $this->stepTwoFields['vlevelrateplanReservation']
        );

        $businessModel->addLeadTraveller($traveller);

        if ($this->activateOtherTravellersInput) {
            foreach ($this->stepTwoFields['otherTravellers'] as $tr) {
                $traveller = new Traveller();

                $traveller->first_name = $tr['firstName'];
                $traveller->last_name = $tr['lastName'];
                $traveller->full_name = $traveller->first_name.' '.$traveller->last_name;
                $businessModel->addOtherTraveller($traveller, $tr['comment']);
            }
        }

        if ($this->roundTrip) {
            $businessModel->setRoundTrip(
                Carbon::createFromFormat('d.m.Y H:i', $this->stepOneFields['returnDateTime']),
                $this->stepTwoFields['departureFlightNumber'] ?? '',
            );
        }

        $businessModel->setSendMail($this->stepTwoFields['sendMail']);

        $id = $businessModel->saveReservation();

        $this->completeReservation = 'Save complete!';

        $this->openStatusModal($id);

        $this->notification()->success('Reservation saved');

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
        $this->stepOneFields['dateTime'] = Carbon::now()->roundHour()->addMinutes(30)->format('d.m.Y H:i');
        $this->stepOneFields['returnDateTime'] = Carbon::now()->roundHour()->addHour()->format('d.m.Y H:i');
    }

    public function updated($property)
    {


        $this->validateOnly($property, array_merge($this->stepOneRules(), $this->stepTwoRules()), [], $this->fieldNames);


        if (in_array($property, [
            'stepOneFields.startingPointId',
            'stepOneFields.endingPointId',
        ])) {


            if($property == 'stepOneFields.endingPointId' && $this->stepOneFields['pickupAddressId'] != ''){
                $this->stepOneFields['dropoffAddress'] = '';
            }else{

                $this->resetAdresses();
            }
        }

        if (array_key_exists(Str::replace('stepOneFields.', '', $property), $this->stepOneFields)) {
            $this->isTransferAvailableAfterDataChange();
        }

        // Since I used availability as a computed property, it throws an error if we pass '' for some params
        // So if user deletes these params, we set them to 0.
        if (in_array($property, [
            'stepOneFields.adults',
            'stepOneFields.children',
            'stepOneFields.infants',
            'stepOneFields.luggage',
        ])) {

            $value = \Arr::get($this->stepOneFields, explode('.', $property)[1]);
            if (!is_numeric($value)) {
                \Arr::set($this->stepOneFields, explode('.', $property)[1], 0);
            }

            // If these parameters change, reset other travellers, except luggage
            if ($property !== 'stepOneFields.luggage') {
                $this->setOtherTravellers();
            }

        }


        //Special Case - overriden V Level Rate Plan
        if($property != 'stepTwoFields.vlevelrateplanReservation'){
            if(preg_match('!VL!',$this->stepOneFields['rate_plan'])){
                if($this->stepTwoFields['vlevelrateplanReservation'] == false){
                    $this->stepTwoFields['vlevelrateplanReservation'] = true;
                    $this->getAvailableTransfersProperty();
                }
            }else{

                if($this->stepTwoFields['vlevelrateplanReservation'] == true){
                    $this->stepTwoFields['vlevelrateplanReservation'] = false;
                    $this->getAvailableTransfersProperty();
                }

            }
        }
    }

    public function resetAdresses()
    {
        $this->stepOneFields['pickupAddress'] = $this->stepOneFields['dropoffAddress'] = '';
    }


    public function setOtherTravellers()
    {


        $this->stepTwoFields['otherTravellers'] = [];
        for ($i = 0; $i < $this->getTotalPassengersProperty() - 1; $i++) {
            $this->stepTwoFields['otherTravellers'][] = [

                'firstName' => null,
                'lastName' => null,
                'comment' => null,
            ];
        }
    }

    public function openStatusModal($id)
    {
        $this->reservationStatusModal = true;

        $this->reservationStatus = Reservation::findOrFail($id);

    }

    public function pullData()
    {

        $this->validate($this->pullDataRules(), [], $this->pullDataFieldNames);

        $api = new \App\Services\Api\ValamarClientApi();

        $api->setReservationCodeFilter($this->pullDataFields['resId'])
            ->setFirstNameFilter($this->pullDataFields['fName'])
            ->setLastNameFilter($this->pullDataFields['lName'])
            ->setPropertyPMSCodeFilter($this->pullDataFields['property']);


        if ($this->pullDataFields['dFrom']) {
            $api->setCheckInFilter(Carbon::create($this->pullDataFields['dFrom']));
        }
        if ($this->pullDataFields['dTo']) {
            $api->setCheckOutFilter(Carbon::create($this->pullDataFields['dTo']));
        }

        $this->apiData = $api->getReservationList();
    }

    public function openPullModal()
    {
        $this->pullModal = true;
    }

    public function openWarningModal(){
        $this->reservationWarningModal = true;
    }

    public function closeWarningModal(){
        $this->reservationWarningModal = true;
    }

    public function closePullModal()
    {
        $this->pullModal = false;
        $this->emptyPullData();
    }

    public function emptyPullData()
    {
        $this->apiData = [];
    }

    public function pullRes($i)
    {

        $data = Arr::get($this->apiData, $i);

        $this->stepOneFields['adults'] = Arr::get($data, 'adults');
        $this->stepOneFields['children'] = Arr::get($data, 'children');
        $this->stepOneFields['luggage'] = Arr::get($data, 'adults');

        $checkInDate = Carbon::make(Arr::get($data, 'checkIn') ?? now());
        $checkOutDate = Carbon::make(Arr::get($data, 'checkOut') ?? now());

        $this->stepOneFields['returnDateTime'] = Carbon::make(Arr::get($data, 'checkOut'))?->format('d.m.Y').' '.substr($this->stepOneFields['returnDateTime'],11,5);

        if ($checkInDate?->isPast()) {
            $this->stepOneFields['dateTime'] = $this->stepOneFields['dateTime'];
        } else {
            $this->roundTrip = true;
            $this->stepOneFields['dateTime'] = $checkInDate->format('d.m.Y').' '.substr($this->stepOneFields['dateTime'],11,5);
        }

        $this->stepOneFields['rate_plan'] = Arr::get($data, 'rateCode');

        if(preg_match('!VL!',$this->stepOneFields['rate_plan'])){
            $this->stepTwoFields['vlevelrateplanReservation'] = true;
        }

        $this->stepTwoFields['leadTraveller']['firstName'] = Str::title(Arr::get($data, 'reservationHolderData.firstName'));
        $this->stepTwoFields['leadTraveller']['lastName'] = Str::title(Arr::get($data, 'reservationHolderData.lastName'));
        $this->stepTwoFields['leadTraveller']['email'] = Arr::get($data, 'reservationHolderData.email');
        $this->stepTwoFields['leadTraveller']['phone'] = Arr::get($data, 'reservationHolderData.mobile');
        $this->stepTwoFields['leadTraveller']['reservationNumber'] = $i;
        $this->stepTwoFields['leadTraveller']['reservationOperaID'] = Arr::get($data, 'OPERA.RESV_NAME_ID');
        $this->stepTwoFields['leadTraveller']['reservationOperaConfirmation'] = Arr::get($data, 'OPERA.CONFIRMATION_NO');

        $this->stepTwoFields['leadTraveller']['check_in'] = $checkInDate->format('d.m.Y');
        $this->stepTwoFields['leadTraveller']['check_out'] = $checkOutDate->format('d.m.Y');

        $this->notification()->success('Data pulled');

        $this->setOtherTravellers();

        $this->closePullModal();

        $this->checkExistingBooking($i);

    }

    private function checkExistingBooking($reservation_code){

        $booking = \DB::table('travellers')->where('reservation_number',$reservation_code)->first();

        if(!empty($booking)){

            $traveller_id = $booking->id;

            $reservation_traveller = \DB::table('reservation_traveller')->where('traveller_id',$traveller_id)->first();

            if(!empty($reservation_traveller)){
                $res_id = $reservation_traveller->reservation_id;

                if((int)$res_id > 0){


                    $reservation = Reservation::find($res_id);

                    if(!empty($reservation)){
                        $this->duplicateBooking = $reservation;
                        $this->openWarningModal();
                    }
                }
            }

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
        $this->stepOneFields['endingPointId'] = '';
        $this->stepOneFields['pickupAddressId'] = $this->stepOneFields['endingAddressId'] = '';

        if (!$this->getEndingPointsProperty()->contains(function ($item) {
            return $item->id === (int)$this->stepOneFields['endingPointId'];
        }));
    }

    public function getDestinationsWithRoutesProperty()
    {
        return Destination::whereHas('routes')->get();
    }

    public function getConfirmationLanguagesArrayProperty()
    {
        return Reservation::CONFIRMATION_LANGUAGES;
    }

    public function getPointsAccomodationProperty()
    {
        return Point::query()
            ->where('type', Point::TYPE_ACCOMMODATION)
            ->whereNotNull('pms_code')
            ->get();
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
            ->notCity()
            ->where('parent_point_id', (int)$this->stepOneFields['startingPointId'])
            ->get();


    }

    public function getDropoffAddressPointsProperty()
    {

        if($this->stepOneFields['pickupAddressId'] > 0){

            $point = Point::findOrFail($this->stepOneFields['pickupAddressId']);

            if($point->type == \App\Models\Point::TYPE_ACCOMMODATION){
                return Point::query()
                    ->notAccommodation()
                    ->notCity()
                    ->where('parent_point_id', (int)$this->stepOneFields['endingPointId'])
                    ->get();
            }else{
                return Point::query()
                    ->justAccommodation()
                    ->where('parent_point_id', (int)$this->stepOneFields['endingPointId'])
                    ->get();
            }


        }

        return Point::query()
            ->notCity()
            ->where('parent_point_id', (int)$this->stepOneFields['endingPointId'])
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

        $extras = collect($this->stepTwoFields['extras'])->reject(function ($item) {
            return $item === false;
        })->keys()->toArray();

        $priceCalculator = new \App\Services\TransferPriceCalculator($this->selectedTransfer, $this->selectedPartner, $this->roundTrip, $route?->id, $extras);

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
        if (!$this->selectedRoute) {
            return collect([]);
        }

        try {
            return (new TransferAvailability(
                $this->selectedRoute,
                (int)$this->stepOneFields['adults'],
                (int)$this->stepOneFields['children'],
                (int)$this->stepOneFields['infants'],
                (int)$this->stepOneFields['luggage'],
                $this->roundTrip
            ))
                ->getAvailablePartnerTransfers();

        } catch (\Exception $e) {
            return collect([]);
        }
    }


    public function addSeat()
    {
        $this->stepTwoFields['seats'][] = false;
    }



    public function removeSeat()
    {
        array_pop($this->stepTwoFields['seats']);
    }


    public function selectTransfer($transferId, $partnerId)
    {

        $this->stepTwoFields['includedInAccommodationReservation'] = false;

        $transfer = Transfer::findOrFail($transferId);
        $partner = Partner::findOrFail($partnerId);
        $route = $this->getSelectedRouteProperty();

        $route_transfer = \DB::table('route_transfer')->where('transfer_id',$transferId)->where('partner_id',$partnerId)->where('route_id',$route->id)->first();

        if(!empty($route_transfer)){
            if($route_transfer->included_in_accommodation == 1){
                $this->stepTwoFields['includedInAccommodationReservation'] = true;
            }
        }

        $this->selectedTransfer = $transfer->id;
        $this->selectedPartner = $partner->id;
    }

    public function nextStep()
    {
        $this->validate($this->stepOneRules(), [], $this->fieldNames);

        if (!$this->selectedPartner || !$this->selectedTransfer) {
            $this->addError('transferNotSelected', 'Please select a transfer!');
            return false;
        }


        $this->step = 2;
    }

    public function isTransferPartnerPairSelected($pId, $tId)
    {
        return $this->selectedTransfer === $tId && $this->selectedPartner === $pId;
    }


    public function setPickupAddress($address): void
    {

        $this->stepOneFields['pickupAddressId'] = null;

        if (is_numeric($address)) {
            if ($addressPoint = Point::find($address)) {
                $this->stepOneFields['pickupAddress'] = $addressPoint->name . ' ' . $addressPoint->address;
                $this->stepOneFields['pickupAddressId'] = $addressPoint->id;



                return;
            }
        }
        $this->stepOneFields['pickupAddress'] = $address;


    }

    public function setDropoffAddress($address): void
    {
        $this->stepOneFields['dropoffAddressId'] = null;
        if (is_numeric($address)) {
            if ($addressPoint = Point::find($address)) {
                $this->stepOneFields['dropoffAddress'] = $addressPoint->name . ' ' . $addressPoint->address;
                $this->stepOneFields['dropoffAddressId'] = $addressPoint->id;
                return;
            }
        }
        $this->stepOneFields['pickupAddress'] = $address;


    }


    public function render()
    {
        return view('livewire.internal-reservation');
    }

    private function isTransferAvailableAfterDataChange()
    {
        if ($this->availableTransfers->where('partner_id', $this->selectedPartner)->where('transfer_id', $this->selectedTransfer)->isEmpty()) {
            $this->selectedTransfer = $this->selectedPartner = null;
        }
    }


}
