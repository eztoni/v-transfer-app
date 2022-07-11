<?php

namespace App\Http\Livewire;


use App\Models\Reservation;
use App\Models\Traveller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ReservationView extends Component
{

    public $reservationId;
    public Reservation $reservation;
    public $traveller;
    public $leadTravellerEdit = false;
    public $otherTravellerComment;
    public $travellerModal;


    protected function rules()
    {

        $rules = [
            'traveller.first_name'=>'required|max:50|min:2',
            'traveller.last_name'=>'required|max:50|min:2',
            'traveller.title'=>'required|max:50|min:2',
            'otherTravellerComment'=>'nullable|max:100',
        ];

        if ($this->leadTravellerEdit) {
            $rules['traveller.reservation_number'] = 'required';
            $rules['traveller.phone'] = 'required|numeric';
            $rules['traveller.email'] = 'required|email';
        }

        return $rules;
    }

    protected $listeners = [
        'updateCompleted' => 'render'
    ];

    public function mount(Reservation $reservation)
    {
        $this->reservation = $reservation;
        $this->reservation->loadMissing(['pickupLocation','otherTravellers','leadTraveller','transfer','partner','extras']);
    }

    //MODAL
    public function openTravellerModal($travellerId,$travellerModel){

        if($travellerModel == 'leadTraveller'){
            $this->leadTravellerEdit = true;
        }else{
            $this->leadTravellerEdit = false;
        }

        $this->travellerModal = true;

        $this->traveller = Traveller::findOrFail($travellerId);
        $this->otherTravellerComment = $this->reservation?->otherTravellers->where('id',$travellerId)->first()?->pivot->comment;

    }

    public function closeTravellerModal(){
        $this->travellerModal = false;
    }

    public function saveTravellerData(){
        if(!Auth::user()->hasRole([User::ROLE_SUPER_ADMIN,User::ROLE_ADMIN]))
            return;

        $this->validate();

        if($this->otherTravellerComment){
            \DB::table('reservation_traveller')->updateOrInsert(
                [
                    'traveller_id'=>$this->traveller->id,
                    'reservation_id'=>$this->reservation->id,
                ],
                [
                    'lead' => 0,
                    'comment' => $this->otherTravellerComment,
                ]
            );
        }

        $this->traveller->save();
        $this->showToast('Saved','Traveller data saved','success');
        $this->reservation->refresh();
        $this->closeTravellerModal();
    }

    //END OF MODAL

    public function getLeadTravellerProperty(){
        return $this->reservation->travellers->where('pivot.lead',true)->first();
    }

    public function getOtherTravellersProperty(){
        return $this->reservation->otherTravellers;
    }

    public function getPickupLocationStringProperty(){
        return "{$this->reservation->pickupLocation->name} {$this->reservation->date} @ {$this->reservation->time} - Address: {$this->reservation->pickup_address}";
    }

    public function render()
    {
        return view('livewire.reservation-view');
    }
}
