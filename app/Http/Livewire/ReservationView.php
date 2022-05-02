<?php

namespace App\Http\Livewire;


use App\Models\Reservation;
use App\Models\Traveller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ReservationView extends Component
{

    public $reservationId;
    public $reservation;
    public $otherTravellerModal;
    public $otherTraveller;
    public $otherTravellerComment;

    protected function rules()
    {
        return [
            'otherTraveller.first_name'=>'required|max:50|min:2',
            'otherTraveller.last_name'=>'required|max:50|min:2',
            'otherTraveller.title'=>'required|max:50|min:2',
            'otherTravellerComment'=>'nullable|max:100',
        ];
    }

    public function mount($id)
    {
        $this->reservationId = $id;
        $this->reservation = Reservation::findOrFail($id);

        if($this->reservation){
            $this->reservation->load('travellers');
            $this->reservation->load('pickupLocation');
        }
    }

    //MODAL
    public function openOtherTravellerModal($travellerId){
        $this->otherTravellerModal = true;
        $this->otherTraveller = Traveller::findOrFail($travellerId);
        $this->otherTravellerComment = $this->otherTraveller->reservations->first()->pivot->comment;
    }

    public function closeOtherTravellerModal(){
        $this->otherTravellerModal = false;
    }

    public function saveOtherTravellerData(){
        if(!Auth::user()->hasRole([User::ROLE_SUPER_ADMIN,User::ROLE_ADMIN]))
            return;

        $this->validate();

        if($this->otherTravellerComment){
            \DB::table('reservation_traveller')->updateOrInsert(
                [
                    'traveller_id'=>$this->otherTraveller->id,
                    'reservation_id'=>$this->reservation->id,
                ],
                [
                    'lead' => 0,
                    'comment' => $this->otherTravellerComment,
                ]
            );
        }

        $this->otherTraveller->save();
        $this->showToast('Saved','Other traveller data saved','success');
        $this->reservation->refresh();
        $this->closeOtherTravellerModal();
    }

    //END OF MODAL

    public function getLeadTravellerProperty(){
        return $this->reservation->travellers->where('pivot.lead',true)->first();
    }

    public function getOtherTravellersProperty(){
        return $this->reservation->travellers->where('pivot.lead',false);
    }

    public function getPickupLocationStringProperty(){
        return "{$this->reservation->pickupLocation->name} {$this->reservation->date} @ {$this->reservation->time} - Address: {$this->reservation->pickup_address}";
    }

    public function render()
    {
        return view('livewire.reservation-view');
    }
}
