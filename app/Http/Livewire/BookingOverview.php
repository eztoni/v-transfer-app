<?php

namespace App\Http\Livewire;

use App\Models\Destination;
use App\Models\Partner;
use App\Models\Point;
use App\Models\Reservation;
use Livewire\Component;
use WireUi\Traits\Actions;
use Carbon\Carbon;

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
    public $page = 1; // Current page number
    public $perPage = 10; // Items per page
    public $totalReservations = 0;
    public $selectYear;
    public $yearOptions = array();
    protected $listeners = ['refreshReservations' => '$refresh'];


    public function mount(): void
    {
        $this->selectYear = now()->year;

        $this->from = now()->startOfMonth();
        $this->to = now()->endOfMonth();
    }


    public function nextPage()
    {
        $this->page++;
    }

    public function previousPage()
    {
        if ($this->page > 1) {
            $this->page--;
        }
    }

    public function getReservationsProperty()
    {
        return Reservation::with(['leadTraveller', 'pickupLocation'])
            ->when($this->partnerId, function ($q) {
                return $q->where('partner_id', $this->partnerId);
            })
            ->when($this->destinationId, function ($q) {
                return $q->where('destination_id', $this->destinationId);
            })
            ->when($this->pointId, function ($q) {
                return $q->where(function ($q) {
                    $q->where('dropoff_address_id', $this->pointId)
                        ->orWhere('pickup_address_id', $this->pointId);
                });
            })
            ->when($this->bookingId, function ($q) {
                return $q->where('id', '=', $this->bookingId);
            })
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('pickup_address', 'LIKE', '%' . $this->search . '%')
                        ->orWhere('dropoff_address', 'LIKE', '%' . $this->search . '%')
                        ->orWhereHas('leadTraveller', function ($q) {
                            $q->where('first_name', 'LIKE', '%' . $this->search . '%')
                                ->orWhere('last_name', 'LIKE', '%' . $this->search . '%')
                                ->orWhere('full_name', 'LIKE', '%' . $this->search . '%')
                                ->orWhere('email', 'LIKE', '%' . $this->search . '%')
                                ->orWhere('reservation_number', 'LIKE', '%' . $this->search . '%')
                                ->orWhere('reservation_opera_confirmation', 'LIKE', '%' . $this->search . '%')
                                ->orWhere('reservation_opera_id', 'LIKE', '%' . $this->search . '%');
                        })
                        ->orWhereHas('destination', function ($q) {
                            $q->where('name', 'LIKE', '%' . $this->search . '%');
                        });
                });
            })
            ->when(!$this->search && $this->selectYear, function ($q) {
                // Only apply the year filter if search is NOT set
                $startOfYear = Carbon::create($this->selectYear, 1, 1, 0, 0, 0);
                $endOfYear = Carbon::create($this->selectYear, 12, 31, 23, 59, 59);
                $q->whereBetween('date_time', [$startOfYear, $endOfYear]);
            })
            ->where('is_main', true)
            ->orderBy('created_at', 'desc')
            ->offset(($this->page - 1) * $this->perPage)
            ->limit($this->perPage)
            ->get();
    }



    public function search(){
        // Just rerender
    }

    public function updatedSelectYear($value)
    {
        $this->selectYear = $value;
        $this->emitSelf('refreshReservations');
    }


    public function render()
    {
        $partners = Partner::all();
        $destinations = Destination::all();
        $points = Point::notCity()->get();
        return view('livewire.booking-overview',compact('partners','destinations','points'));
    }
}
