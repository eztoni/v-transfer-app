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
use App\Services\Api\ValamarFiskalizacija;
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
    public $destinationPoints = false;
    public $deleteId = '';
    public bool $importPoint = false;
    public $valamarPropertiesFromApi;
    public $point_options = array();

    public $companyLanguages = ['en'];
    public $pointName = [
        'en' => null
    ];

    public $mappedPackages = array();
    public $route_packages = array();

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

        $this->destinationPoints = $points;

        $partners = \DB::table('partners')->where('owner_id',Auth::user()->owner_id)->get();


        $routes = array();

        $this->getMappedPackageRelations($points);

        $transfers = Transfer::where('destination_id',Auth::user()->destination_id)->get();

        $transfer_ids = array();

        if(!empty($transfers)){
            foreach($transfers as $tr){
                $transfer_ids[] = $tr->id;
            }
        }

        $owner_partners = Partner::where('owner_id',Auth::user()->owner_id)->get();

        $partner_ids = array();

        $this->valamarPropertiesFromApi = collect();

        $this->valamarPropertiesFromApi = (new ValamarClientApi())->getPropertiesList();

        if(!empty($this->valamarPropertiesFromApi)){
            foreach($this->valamarPropertiesFromApi as $property){

                if(!empty($property['class'])){
                    $propertyMap[$property['propertyOperaCode'].'|'.$property['class']] = $property['name'];
                }

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





        return view('livewire.destination-setup-checker',compact('destinations','points','partners','routes'));
    }

    private function getMappedPackageRelations($accommodation_list){


        $owner_partners =  \DB::table('partners')->where('owner_id',Auth::user()->owner_id)->get();

        $package_ids = array();

        $transfers = Transfer::where('destination_id',Auth::user()->destination_id)->get();

        $transfer_ids = array();

        if(!empty($transfers)){
            foreach($transfers as $tr){
                $transfer_ids[] = $tr->id;
            }
        }

        if(!empty($owner_partners)){

            $partner_ids = array();

            foreach($owner_partners as $partner){

                $partner_ids[] = $partner->id;

                if($partner->cancellation_package_id > 0 && !in_array($partner->cancellation_package_id,$package_ids)){
                    $package_ids[] = $partner->cancellation_package_id;
                }

                if($partner->no_show_package_id > 0 && !in_array($partner->no_show_package_id,$package_ids)){
                    $package_ids[] = $partner->no_show_package_id;
                }
            }



            if(!empty($transfer_ids) && !empty($partner_ids)){

                $mapped_routes =  \DB::table('route_transfer')
                    ->whereIn('transfer_id', $transfer_ids)
                    ->whereIn('partner_id', $partner_ids)
                    ->where('opera_package_id', '>', 0)
                    ->get();

                if(!empty($mapped_routes)){
                    foreach($mapped_routes as $rt){
                        if(!in_array($rt->opera_package_id,$package_ids)){
                            $this->route_packages[] = (int)$rt->opera_package_id;
                            $package_ids[] = (int)$rt->opera_package_id;
                        }
                    }
                }
            }



            if(!empty($package_ids)){

                $valamarAPI = new ValamarFiskalizacija();
                $packageExistance = $valamarAPI->validatePackageIDMapping($package_ids);

                if($packageExistance['Status'] == 'OK' && !empty($packageExistance['PackageInfo'])){
                    foreach($packageExistance['PackageInfo'] as $packMap){

                        $packageID = $packMap['PackageID'];

                        if(!empty($packMap['HotelList'])){
                            foreach($packMap['HotelList'] as $hotelInfo){

                                $hotelMapping = explode('/',$hotelInfo);

                                if(count($hotelMapping) > 0){
                                    $this->mappedPackages[trim($packageID)][trim($hotelMapping[0])] = $hotelMapping[1];
                                }
                            }
                        }
                    }
                }
            }

        }
    }

    public function getPackagePropertyMapping($package_id){

        $return = array();

        if(!empty($this->destinationPoints)){
            foreach($this->destinationPoints as $point){

                $code = $point->pms_code;

                if(empty($this->mappedPackages[$package_id][$code])){
                    $return[] = '['.$code.'] '.$point->name;
                }
            }
        }

        return $return;

    }
}
