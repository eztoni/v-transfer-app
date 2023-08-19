<?php

namespace App\Http\Livewire;

use App\Models\Company;
use App\Models\Destination;
use App\Models\Language;
use App\Models\Point;
use App\Models\Partner;
use App\Models\Transfer;
use App\Models\User;
use App\Services\Api\ValamarClientApi;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\Actions;


class DestinationSetupChecker extends Component
{
use Actions;

    use WithPagination;

    public $search = '';
    public $destinationId;
    public $destination;
    public $propertyMap = array();
    public $propertyMapTest = array();
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
        $this->destination = Auth::user()->destination;
    }


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

        $points = Point::whereDestinationId(Auth::user()->destination_id)->where('type',Point::TYPE_ACCOMMODATION)->get();

        $partners = \DB::table('partners')->where('owner_id',Auth::user()->owner_id)->get();

        $this->valamarPropertiesFromApi = collect();


        $this->valamarPropertiesFromApi = (new ValamarClientApi())->getPropertiesList();

        if(!empty($this->valamarPropertiesFromApi)){
            foreach($this->valamarPropertiesFromApi as $property){
                $propertyMap[$property['propertyOperaCode'].'|'.$property['class']] = $property['name'];
            }
        }

        if(!empty($points)){
            foreach($points as $point){

                $code = strtoupper($point->pms_code.'|'.$point->pms_class);
                $inverse = strtoupper($point->pms_class.'|'.$point->pms_code);

                #Not Able To Find the property
                $this->propertyMapTest[$code] = 0;

                #Property Mapped Property
                if(!empty($propertyMap[$code])){
                    $this->propertyMapTest[$code] = 1;
                }

                #Property Mapped Inverse
                if(!empty($propertyMap[$inverse])){
                    $this->propertyMapTest[$code] = 2;
                }
            }
        }


        return view('livewire.destination-setup-checker',compact('destinations','points','partners'));
    }
}
