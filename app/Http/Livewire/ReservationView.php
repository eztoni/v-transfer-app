<?php

namespace App\Http\Livewire;


use App\Mail\Guest\ReservationConfirmationMail;
use App\Models\Reservation;
use App\Models\Traveller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Livewire\Component;
use WireUi\Traits\Actions;

class ReservationView extends Component
{
use Actions;

    public $reservationId;
    public Reservation $reservation;
    public $traveller;
    public $leadTravellerEdit = false;
    public $otherTravellerComment;
    public $travellerModal;
    public $sentAgain = false;

    protected function rules()
    {

        $rules = [
            'traveller.first_name'=>'required|max:50|min:2',
            'traveller.last_name'=>'required|max:50|min:2',
            'traveller.title'=>'max:500',
            'traveller.reservation_opera_confirmation'=>'string|max:200',
            'traveller.reservation_opera_id'=>'string|max:200',
            'otherTravellerComment'=>'nullable|max:100',
        ];
        if ($this->leadTravellerEdit) {
            $rules['traveller.reservation_number'] = 'required';
            $rules['traveller.phone'] = 'required|numeric';
            $rules['traveller.email'] = 'required|email|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
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
    public function openTravellerModal($travellerId){

        $this->travellerModal = true;

        $this->traveller = Traveller::findOrFail($travellerId);
        if($this->reservation->leadTraveller->id === $this->traveller->id){
            $this->leadTravellerEdit = true;
        }else{
            $this->leadTravellerEdit = false;
        }
        $this->otherTravellerComment = $this->reservation?->otherTravellers->where('id',$travellerId)->first()?->pivot->comment;
    }

    public function closeTravellerModal(){
        $this->travellerModal = false;
    }

    public function saveTravellerData(){
        #if(!Auth::user()->hasRole([User::ROLE_SUPER_ADMIN,User::ROLE_ADMIN]))
            #return;

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
        $this->notification()->success('Saved','Traveller data saved');
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

    public function sendAgainConfirmationDialog(){
        $this->dialog()->confirm([
            'title'       => 'You are about to send a reservation confirmation email again',
            'description' => 'Proceed with this action?',
            'icon'        => 'question',
            'accept'      => [
                'label'  => 'Yes, send',
                'method' => 'sendAgain',
            ],
            'reject' => [
                'label'  => 'No, cancel',
            ],
        ]);
    }

    public function sendAgain(): void
    {

        if (!$this->reservation->is_main){
            return;
        }

        // Add email to email list
        if($travellerMail = $this->reservation->leadTraveller?->email){
            Mail::to($travellerMail)->locale($this->reservation->confirmation_language)->send(new ReservationConfirmationMail($this->reservation->id,$this->reservation->confirmation_language??'en'));
        }

        $this->notification()->success('Sent!','Reservation confirmation sent');

        $this->sentAgain = true;
    }

    public function render()
    {
        return view('livewire.reservation-view');
    }
}
