<?php

namespace App\Http\Livewire\CRUD;

use App\Models\Destination;
use App\Models\Point;
use App\View\Components\Form\EzSelect;
use App\View\Components\Form\EzTextInput;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use JetBrains\PhpStorm\ArrayShape;

class RoutesOverview extends EzComponent
{

    public $userDestinationId = '';

    public array $fieldRuleNames=[
        'model.name' => 'name',
        'model.destination_id' => 'destination',
        'model.ending_point_id' => 'ending point',
        'model.starting_point_id' => 'starting point',
        'model.pms_code'=>'his code',
    ];

    public function mount(){
        $this->userDestinationId = Auth::user()->destination_id;
    }

    public function setModelClass(): string
    {
        return \App\Models\Route::class;
    }

    public function tableColumns(): array
    {
        return [
            'id' => '#id',
            'name' => 'Name',
            'destination.name' => 'Destination',
            'startingPoint.name' => 'Starting point',
            'endingPoint.name' => 'Ending point',
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
            ->get()->pluck('name','id')->toArray();
    }

    public function getEndingPointsProperty(){
        return Point::whereDestinationId($this->userDestinationId )
            ->where('id','!=',$this->model->starting_point_id)
            ->get()->pluck('name','id')->toArray();
    }

    protected function beforeSave(){
        $this->model->owner_id = \Auth::user()->owner_id;
        $this->model->destination_id = $this->userDestinationId;
        return true;
    }

    public function formFields(): Collection
    {
        return collect([
            (new EzTextInput('Name','model.name'))->withAttributes(['placeholder'=>'ex. Hotel to airport']),
            new EzSelect('Starting point','model.starting_point_id',$this->startingPoints),
            new EzSelect('Ending point ','model.ending_point_id',$this->endingPoints),
            new EzTextInput('PMS code','model.pms_code'),
        ]);
    }

    protected function rules(): array
    {
       return [
           'model.name' => 'required|min:3',
           'model.ending_point_id' => 'required',
           'model.starting_point_id' => 'required',
           'model.pms_code'=>'nullable',
       ];
    }
}
