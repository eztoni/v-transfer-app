<?php

namespace App\Http\Livewire;

use App\Models\Destination;
use App\Models\Point;
use App\Models\Route;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\Actions;

class RoutesOverview extends Component
{
use Actions;

    use WithPagination;

    public $search = '';
    public $route;
    public $routeModal;
    public $softDeleteModal;
    public $deleteId = '';

    protected function rules()
    {
        return [
            'route.name'=>'required|max:255|min:2',
            'route.destination_id'=>'required|numeric',
            'route.starting_point_id'=>'required|numeric',
            'route.ending_point_id'=>'required|numeric',
            'route.pms_code'=>'nullable',
        ];
    }
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function openRouteModal(){
        $this->routeModal = true;
    }

    public function closeRouteModal(){
        $this->routeModal = false;
    }

    public function mount(){

        $this->route = new Route();
    }

    public function updateRoute($routeId){
        $this->openRouteModal();
        $this->route = Route::find($routeId);
    }

    public function updatedRouteDestinationId(){
        $this->route->starting_point_id = null;
        $this->route->ending_point_id = null;
    }


    //Computed
    public function getStartingPointsProperty(){
        $starting_points = Point::whereDestinationId($this->route->destination_id)->where('id','!=',$this->route->ending_point_id)->get()->map->only(['name', 'id'])->toArray();
        return  $starting_points;
    }

    public function getEndingPointsProperty(){
        $ending_points = Point::whereDestinationId($this->route->destination_id)->where('id','!=',$this->route->starting_point_id)->get()->map->only(['name', 'id'])->toArray();
        return  $ending_points;
    }

    public function addRoute(){
        $this->openRouteModal();
        $this->route = new Route();
    }

    public function saveRouteData(){

        if(!Auth::user()->hasRole([User::ROLE_SUPER_ADMIN,User::ROLE_ADMIN]))
            return;


        $this->validate();
        $this->route->owner_id = Auth::user()->owner_id;
        $this->route->save();
        $this->notification()->success('Saved','Route Saved');
        $this->closeRouteModal();
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
        Route::find($this->deleteId)->delete();
        $this->closeSoftDeleteModal();
        $this->notification()->success('Route deleted','',);
    }
    //------------- Soft Delete End ---------

    public function render()
    {
        $routes = Route::search('name',$this->search)->paginate(10);
        $destinations = Destination::all();
        return view('livewire.routes-overview',compact('routes','destinations'));
    }
}
