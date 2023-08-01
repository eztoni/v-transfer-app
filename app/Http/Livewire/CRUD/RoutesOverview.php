<?php

namespace App\Http\Livewire\CRUD;

use App\Models\Destination;
use App\Models\Language;
use App\Models\Point;
use App\Models\Route;
use App\View\Components\Form\EzSelect;
use App\View\Components\Form\EzTextInput;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use JetBrains\PhpStorm\ArrayShape;

class RoutesOverview extends EzComponent
{

    public $userDestinationId = '';
    public $companyLanguages = ['en'];
    public $routeName = [
        'en' => null
    ];


    public array $fieldRuleNames=[
        'model.destination_id' => 'destination',
        'model.ending_point_id' => 'ending point',
        'model.starting_point_id' => 'starting point',
        'model.pms_code'=>'his code',
        'routeName.*' => 'extra name',

    ];

    public function mount(){
        parent::mount();
        $this->userDestinationId = Auth::user()->destination_id;
        $this->instantiateComponentValues();
    }

    private function instantiateComponentValues(): void
    {
        $this->companyLanguages = Language::all()->pluck('language_code')->toArray();
        foreach ($this->companyLanguages as $lang) {
            $this->routeName[$lang] = $this->model->getTranslation('name', $lang, false);
        }

    }
    public function updateModel($modelId):void
    {
        parent::updateModel($modelId);
        $this->instantiateComponentValues();
    }

    public function addModel()
    {
        parent::addModel();
        #Restart Route Name
        $this->routeName = array_fill_keys(array_keys($this->routeName), null);
    }

    public function updatedRouteName()
    {
        $this->model->setTranslations('name', $this->routeName);
    }

    public function setModelClass(): string
    {
        return \App\Models\Route::class;
    }

    public function tableColumns(): array
    {
        return [
            '#id' => 'id',
            'Name' => 'name',
            'Starting point' => 'startingPoint.name',
            'Ending point' => 'endingPoint.name',
        ];
    }

    protected function modelName(): string
    {
        return 'Route';
    }

    protected function withArray():array
    {
        return ['destination'];
    }

    public function getDestinationSelectProperty()
    {
        return Destination::all()->pluck('name','id')->toArray();
    }

    //Computed
    public function getStartingPointsProperty(){
        return Point::whereDestinationId($this->userDestinationId )
            ->where('id','!=',$this->model->ending_point_id)
            ->cityOnly()
            ->get()->pluck('name','id')->toArray();
    }

    public function getEndingPointsProperty(){
        return Point::whereDestinationId($this->userDestinationId )
           ->cityOnly()
            ->where('id','!=',$this->model->starting_point_id)
            ->get()->pluck('name','id')->toArray();
    }

    protected function beforeSave(){
        $this->model->owner_id = \Auth::user()->owner_id;
        $this->model->destination_id = $this->userDestinationId;
        $this->model->setTranslations('name', $this->routeName);
        return true;
    }

    public function formBladeViewName(): string
    {
        return 'routes-overview';
    }

    protected function rules(): array
    {
        $ruleArray= [
           'model.ending_point_id' => 'required',
           'model.starting_point_id' => 'required',
           'model.pms_code'=>'nullable',
           'routeName.en' => 'required|min:3',

       ];
        foreach ($this->companyLanguages as $lang) {
            if ($lang !== 'en') {
                $ruleArray['routeName.' . $lang] = 'nullable|min:3';
            }
        }

        return $ruleArray;
    }
}
