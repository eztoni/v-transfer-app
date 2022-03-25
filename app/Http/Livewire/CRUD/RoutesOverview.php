<?php

namespace App\Http\Livewire\CRUD;

use App\Models\Route;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class RoutesOverview extends EzComponent
{


    public function setModelClass(): string
    {
        return \App\Models\Route::class;
    }

    public function setTableColumns(): array
    {
       return  [
          'id'=>'#id',
            'name'=>'Name'
        ];
    }

    protected function rules(): array
    {
        return [];
    }

    protected function modelName(): string
    {
        return 'Route';
    }

    protected function fieldRuleNames(): array
    {
        return [];
    }
}
