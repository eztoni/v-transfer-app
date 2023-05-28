<?php

namespace App\Http\Livewire;

use App\Models\Company;
use App\Models\Destination;
use App\Models\Language;
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
    public $point_options = array();

    public $companyLanguages = ['en'];
    public $pointName = [
        'en' => null
    ];

    public function mount()
    {
        $this->point = new Point();
        $this->destinationId = Auth::user()->destination_id;
        $this->destination = Auth::user()->destination;
        $this->companyLanguages = Language::all()->pluck('language_code')->toArray();
        $this->setPointInitialTranslations();
        $this->loadParentPointsArray();

    }

    protected function rules()
    {

        $ruleArray = [
            'pointName.en' => 'required|min:3',
            'point.name'=>'required|max:255|min:2',
            'point.internal_name'=>'max:255|min:2',
            'point.description'=>'nullable|min:3',
            'point.reception_email' => 'exclude_unless:point.type,'.\App\Models\Point::TYPE_ACCOMMODATION.'|required|email',
            'point.address'=>'nullable|min:3',
            'point.fiskal_invoice_no' => 'exclude_unless:point.type,'.\App\Models\Point::TYPE_ACCOMMODATION.'|required|min:1',
            'point.fiskal_establishment' => 'exclude_unless:point.type,'.\App\Models\Point::TYPE_ACCOMMODATION.'|required|min:1',
            'point.fiskal_device' => 'exclude_unless:point.type,'.\App\Models\Point::TYPE_ACCOMMODATION.'|required|min:1',
            'point.type'=>[
                'required',
                Rule::in(Point::TYPE_ARRAY),
            ],
            'point.pms_class' => 'nullable|required_if:point.type,'.\App\Models\Point::TYPE_ACCOMMODATION,
            'point.pms_code' => 'nullable|required_if:point.type,'.\App\Models\Point::TYPE_ACCOMMODATION,
            'point.parent_point_id' => 'required_if:point.type,!=,'.\App\Models\Point::TYPE_CITY.',|integer|min:0'
        ];
        foreach ($this->companyLanguages as $lang) {
            if ($lang !== 'en') {
                $ruleArray['pointName.' . $lang] = 'nullable|min:3';
            }
        }

        return $ruleArray;
    }

    private function loadParentPointsArray(){

        $this->point_options = array();

        $point_options  = Point::query()
            ->cityOnly()
            ->where('destination_id',$this->destinationId)
            ->get();

      if(!empty($point_options)){
          foreach($point_options as $option){
              $this->point_options[$option->id] = $option->internal_name;
          }
      }
    }

    public function updatedPointName()
    {
        $this->point->setTranslations('name', $this->pointName);
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
        $this->setPointInitialTranslations();
    }

    private function setPointInitialTranslations()
    {
        foreach ($this->companyLanguages as $lang) {
            $this->pointName[$lang] = $this->point->getTranslation('name', $lang, false);
        }
    }

    public function addPoint(){

        $this->openPointModal();
        $this->point = new Point();
        $this->setPointInitialTranslations();

    }

    public function savePointData(){

        if(!Auth::user()->hasRole([User::ROLE_SUPER_ADMIN,User::ROLE_ADMIN]))
            return;

        //$this->validate();

        $this->point->setTranslations('name', $this->pointName);

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

            foreach($this->companyLanguages as $lang){
                $this->pointName[$lang] = Arr::get($dataFromApi,'name')?:'';
            }

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
