<?php

namespace App\Http\Livewire;

use App\Models\Company;
use App\Models\Destination;
use App\Models\Point;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class PointsOverview extends Component
{

    public $search = '';
    public $point;
    public $pointModal;
    public $softDeleteModal;
    public $deleteId = '';


    protected function rules()
    {
        return [
            'point.name'=>'required|max:255|min:2',
            'point.destination_id'=>'required|numeric',
            'point.description'=>'nullable|min:3',
            'point.address'=>'required|min:3',
            'point.latitude'=>'required|min:3',
            'point.longitude'=>'required|min:3',
            'point.type'=>[
                'required',
                Rule::in(Point::TYPE_ARRAY),
            ],
            'point.his_code'=>'nullable',
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

    public function mount(){
        $this->point = new Point();
    }

    public function updatePoint($pointId){
        $this->openPointModal();
        $this->point = Point::find($pointId);
        $this->emit('updateMap',['lat'=>$this->point->latitude,'lng'=>$this->point->longitude]);
    }

    public function getRawAddressProperty(){
        if($this->point)
        return $this->point->address;
    }

    public function addPoint(){
        $this->openPointModal();
        $this->point = new Point();
        $this->emit('updateMap',['lat'=>$this->point->latitude,'lng'=>$this->point->longitude]);
    }

    public function savePointData(){

        if(!Auth::user()->hasRole([User::ROLE_SUPER_ADMIN,User::ROLE_ADMIN]))
            return;

        $this->validate();
        $this->point->company_id = Auth::user()->company_id;
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
        $points = Point::search('name',$this->search)->paginate(10);
        $destinations = Destination::all();
        return view('livewire.points-overview',compact('points','destinations'));
    }
}
