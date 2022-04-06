<?php

namespace App\Http\Livewire;

use App\Models\Company;
use App\Models\Destination;
use App\Models\Point;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;


class PointsOverview extends Component
{

    use WithPagination;

    public $search = '';
    public $destinationId;
    public $destination;
    public $point;
    public $pointModal;
    public $softDeleteModal;
    public $deleteId = '';

    public function mount()
    {
        $first = Destination::first();
        $this->destinationId = $first->id ?? null;
        $this->point = new Point();
        $this->setDestination();
    }

    public function updatedDestinationId()
    {
        $this->setDestination();
    }


    private function setDestination(){
        if($this->destinationId > 0){
            $this->destination = Destination::with('points')->find($this->destinationId);
        }
    }


    public function getPointsProperty(){
        if($this->destinationId > 0){
            $this->destination->refresh();
            return $this->destination->points;
        }
        return collect();
    }


    protected function rules()
    {
        return [
            'point.name'=>'required|max:255|min:2',
            'point.description'=>'nullable|min:3',
            'point.address'=>'required|min:3',
            'point.type'=>[
                'required',
                Rule::in(Point::TYPE_ARRAY),
            ],
            'point.pms_code'=>'nullable',
        ];
    }
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }


    public function openPointModal(){
        $this->pointModal = true;
    }

    public function closePointModal(){
        $this->pointModal = false;
    }

    public function updatePoint($pointId){
        $this->openPointModal();
        $this->point = Point::find($pointId);
    }

    public function addPoint(){
        $this->openPointModal();
        $this->point = new Point();
    }

    public function savePointData(){

        if(!Auth::user()->hasRole([User::ROLE_SUPER_ADMIN,User::ROLE_ADMIN]))
            return;

        $this->validate();
        $this->point->destination_id = $this->destinationId;
        $this->point->owner_id = Auth::user()->owner_id;
        $this->point->save();
        $this->showToast('Saved','Point Saved','success');
        $this->closePointModal();

    }

    //------------ Soft Delete ------------------
    public function openSoftDeleteModal($id){
        $this->deleteId = $id;
        $this->softDeleteModal = true;
    }

    public function closeSoftDeleteModal(){
        $this->deleteId = '';
        $this->softDeleteModal = false;
    }

    public function softDelete(){
        Point::find($this->deleteId)->delete();
        $this->closeSoftDeleteModal();
        $this->showToast('Point deleted','',);
    }
    //------------- Soft Delete End ---------

    public function render()
    {
        $destinations = Destination::all();
        return view('livewire.points-overview',compact('destinations'));
    }
}
