<?php

namespace App\Http\Livewire;

use App\Models\Destination;
use App\Models\Partner;
use App\Models\Point;
use App\Models\Reservation;
use Livewire\Component;
use WireUi\Traits\Actions;

class BookingOverview extends Component
{
use Actions;

    public $destinationId = null;
    public $partnerId = null;
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

    public function getReservationsProperty(): array|\Illuminate\Database\Eloquent\Collection
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
            ->get();
    }


    public function search(){
        // Just rerender
    }


    public function render()
    {
        $partners = Partner::all();
        $destinations = Destination::all();
        $points = Point::notCity()->get();
        return view('livewire.booking-overview',compact('partners','destinations','points'));
    }
}
