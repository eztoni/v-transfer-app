<?php

namespace App\Http\Livewire\CRUD;


use App\Models\Destination;

class OwnerOverview extends EzComponent
{
    public $destination_name = '';

    public array $fieldRuleNames=[
        'model.name' => 'name',
    ];

    public function mount(){
        parent::mount();
    }

    public function setModelClass(): string
    {
        return \App\Models\Owner::class;
    }

    public function tableColumns(): array
    {
        return [
            '#id' => 'id',
            'Name' => 'name',

        ];
    }

    protected function modelName(): string
    {
        return 'Owner';
    }


    protected function beforeSave(){
        $this->model->company_id = \Auth::user()->company_id;

        return true;
    }

    protected function afterSave()
    {
        $this->model->destinations()->save(new Destination(['name'=>$this->destination_name]));

    }

    public function formBladeViewName(): string
    {
        return 'owner-overview';
    }

    protected function rules(): array
    {
       return [
           'model.name' => 'required|min:3',

       ];
    }
}
