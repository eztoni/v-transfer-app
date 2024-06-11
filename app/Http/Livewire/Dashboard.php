<?php

namespace App\Http\Livewire;

use App\Models\Destination;
use App\Models\Partner;
use App\Models\Point;
use App\Models\Reservation;
use Livewire\Component;
use WireUi\Traits\Actions;

class Dashboard extends Component
{
    use Actions;

    public $destinationId = null;
    public $partnerId = null;
    public $operaErrorBookings = false;
    public $fiscalizationErrorBookings = false;
    public $connectedDocumentErrorBookings = false;
    public $reservationResolveModal = false;
    public string $from;
    public string $to;
    public ?string $search = null;
    public ?string $bookingId = null;
    public ?string $pointId = null;

    public function mount(): void
    {
        $this->from = now()->startOfMonth();
        $this->to = now()->endOfMonth();
    }

    protected $listeners = [
        'resolveClosed' => 'closeResolveModal',
        'reservationResolved' => 'reservationResolved',
    ];

    public function get_bookings(): array|\Illuminate\Database\Eloquent\Collection
    {
        return Reservation::with(['leadTraveller','pickupLocation'])
            ->when($this->partnerId,function ($q){
                return $q->where('partner_id',$this->partnerId);
            })
            ->when($this->destinationId,function ($q){
                return $q->where('destination_id',$this->destinationId);
            })
            ->when($this->destinationId,function ($q){
                return $q->where('destination_id',$this->destinationId);
            })
            ->when($this->pointId,function ($q){
                return $q->where(function($q){
                    $q->where('dropoff_address_id',$this->pointId)
                        ->orWhere('pickup_address_id',$this->pointId);
                });
            })
            ->when($this->bookingId,function ($q){
                return $q->where('id', '=',  $this->bookingId );
            })
            ->when($this->search, function ($q){
                $q->where(function ($q){
                    $q->where('pickup_address', 'LIKE', '%' . $this->search . '%')
                        ->where('dropoff_address', 'LIKE', '%' . $this->search . '%')
                        ->orWhereHas('leadTraveller', function ($q){
                            $q->where('first_name', 'LIKE', '%' . $this->search . '%')
                                ->orWhere('last_name', 'LIKE', '%' . $this->search . '%')
                                ->orWhere('full_name', 'LIKE', '%' . $this->search . '%')
                                ->orWhere('email', 'LIKE', '%' . $this->search . '%')
                                ->orWhere('reservation_number', 'LIKE', '%' . $this->search . '%')
                                ->orWhere('reservation_opera_confirmation', 'LIKE', '%' . $this->search . '%')
                                ->orWhere('reservation_opera_id', 'LIKE', '%' . $this->search . '%');
                        })
                        ->orWhereHas('destination', function ($q){
                            $q->where('name', 'LIKE', '%' . $this->search . '%');
                        });
                });
            })
            ->where('is_main',true)
            ->orderBy('created_at','desc')
            ->get();
    }

    public function getErrorBookings() : void {

        $bookings = $this->get_bookings();

        if($this->operaErrorBookings === false) {
            $this->operaErrorBookings = array();
        }

        if($this->fiscalizationErrorBookings === false) {
            $this->fiscalizationErrorBookings = array();
        }

        if($this->connectedDocumentErrorBookings === false) {
            $this->connectedDocumentErrorBookings = array();
        }

        foreach($bookings as $booking){

            if($booking->resolved == 1){
                continue;
            }

            if($booking->included_in_accommodation_reservation == 1 || $booking->v_level_reservation == 1){
               continue;
            }

            if(!$booking->isSyncedWithOpera()){
                $this->operaErrorBookings[] = $booking;
            }else{
                if(!$booking->getInvoiceData('jir')){
                    $this->fiscalizationErrorBookings[] = $booking;
                }else{
                    if(!$booking->isDocumentConnectedSync()){
                        $this->connectedDocumentErrorBookings[] = $booking;
                    }
                }
            }
        }
    }

    public function openResolveModal($id){
        $this->reservationResolveModal = true;
        $this->resolveReservation = Reservation::findOrFail($id);
    }
    public function closeResolveModal(){
        $this->reservationResolveModal = false;
    }

    public function reservationResolved(){
        $this->closeResolveModal();
        $this->notification()->success('Reservation Marked as Resolved');
        sleep(1);
        $this->redirect(route('dashboard'));
    }

    public function has_errors(){

        $this->getErrorBookings();

        $return = false;

        if(!empty($this->operaErrorBookings)){
            return true;
        }

        if(!empty($this->fiscalizationErrorBookings)){
            return true;
        }

        if(!empty($this->connectedDocumentErrorBookings)){
            return true;
        }

        return $return;
    }

    public function get_opera_error_bookings(): array|\Illuminate\Database\Eloquent\Collection {
        return $this->operaErrorBookings;
    }

    public function get_fiscalization_error_bookings(): array|\Illuminate\Database\Eloquent\Collection {
        return $this->fiscalizationErrorBookings;
    }

    public function get_connected_document_error_bookings() : array|\Illuminate\Database\Eloquent\Collection {
        return $this->connectedDocumentErrorBookings;
    }
    public function search(){
        // Just rerender
    }

    public function isUser(){

        $return = false;

        if(!empty(auth()->user()->roles[0])){
            if(auth()->user()->roles[0]->name == 'user'){
                $return = true;
            }
        }

        return $return;

    }

    public function isAdmin(){

        $return = false;

        if(!empty(auth()->user()->roles[0])){
            if(auth()->user()->roles[0]->name == 'admin'){
                $return = true;
            }
        }

        return $return;
    }


    public function render()
    {
        $partners = Partner::all();
        $destinations = Destination::all();
        $points = Point::notCity()->get();
        return view('livewire.dashboard',compact('partners','destinations','points'));
    }
}
