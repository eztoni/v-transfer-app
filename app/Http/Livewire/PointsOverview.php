<?php

namespace App\Http\Livewire;

use App\Models\Company;
use App\Models\Destination;
use App\Models\Point;
use App\Models\Transfer;
use App\Models\User;
use App\Services\Api\ValamarClientApi;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\Actions;


class PointsOverview extends Component
{
use Actions;

    use WithPagination;

    public $search = '';
    public $destinationId;
    public $destination;
    public ?Point $point;
    public $pointModal;
    public $softDeleteModal;
    public $deleteId = '';
    public bool $importPoint = false;
    public $valamarPropertiesFromApi;

    public function mount()
    {
        $this->point = new Point();
        $this->destinationId = Auth::user()->destination_id;
        $this->destination = Auth::user()->destination;
    }

    protected function rules()
    {

        $ruleArray = [
            'point.name'=>'required|max:255|min:2',
            'point.internal_name'=>'max:255|min:2',
            'point.description'=>'nullable|min:3',
            'point.reception_email' => 'exclude_unless:point.type,'.\App\Models\Point::TYPE_ACCOMMODATION.'|required|email',
            'point.address'=>'nullable|min:3',
            'point.type'=>[
                'required',
                Rule::in(Point::TYPE_ARRAY),
            ],
            'point.pms_class' => 'nullable|required_if:point.type,'.\App\Models\Point::TYPE_ACCOMMODATION,
            'point.pms_code' => 'nullable|required_if:point.type,'.\App\Models\Point::TYPE_ACCOMMODATION,

        ];

        return $ruleArray;
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


        if($this->point->type == Point::TYPE_ACCOMMODATION){
            if (Point::wherePmsClass($this->point->pms_class)->wherePmsCode($this->point->pms_code)->where('id', '!=', $this->point->id)->exists()){
                $this->addError('not_unique','Property with this Class and Code combination already exists');
                return;
            }
        }


        $this->point->destination_id = $this->destinationId;
        $this->point->owner_id = Auth::user()->owner_id;
        $this->point->save();
        $this->notification()->success('Saved','Point Saved');
        $this->closePointModal();

    }

    //------------ Soft Delete ------------------
    public function openSoftDeleteModal($id){
        $this->point = Point::find($id);
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
        $this->notification()->success('Point deleted','',);
    }
    //------------- Soft Delete End ---------

    public function setImportData($key){

        if ($dataFromApi = Arr::get($this->valamarPropertiesFromApi,$key)){

            $this->point->type = Point::TYPE_ACCOMMODATION;
            $this->point->pms_code = Arr::get($dataFromApi,'propertyOperaCode')?:'';
            $this->point->name = Arr::get($dataFromApi,'name')?:'';
            $this->point->pms_class = Arr::get($dataFromApi,'class')?:'';
            $this->point->address = Arr::get($dataFromApi,'address')?:'';
            $this->importPoint = false;
        }
    }


    public function render()
    {
        $destinations = Destination::all();

        $points = Point::whereDestinationId(Auth::user()->destination_id)->paginate(15);

        $this->valamarPropertiesFromApi = collect();

         if ($this->importPoint){
             $this->valamarPropertiesFromApi = (new ValamarClientApi())->getPropertiesList();
         }

        return view('livewire.points-overview',compact('destinations','points'));
    }
}
