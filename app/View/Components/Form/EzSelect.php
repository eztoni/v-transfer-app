<?php

namespace App\View\Components\Form;

use Illuminate\Support\Collection;
use Illuminate\View\Component;
use function view;

class EzSelect extends Component
{
    public  $label;
    public   $model;
    public  $errorString ;
    public  $parentDivClasses ;
    public  $labelClasses ;
    public  $labelTextClasses;
    public array $items;
    public bool $showEmptyValue = true;
    public  $sm;

    public function __construct(
         $label,
         $model,
         array $items,
        $errorString=null,
        $parentDivClasses = null,
        $labelClasses = null,
        $labelTextClasses = null,
        $sm = false,
        $showEmptyValue = true,
    )
    {
        $this->label = $label;
        $this->sm = $sm;
        $this->model = $model;
        $this->items = $items;
        $this->errorString = empty($errorString) ? $this->model : $errorString;
        $this->parentDivClasses = $parentDivClasses;
        $this->labelClasses = $labelClasses;
        $this->labelTextClasses = $labelTextClasses;
        $this->showEmptyValue = $showEmptyValue;

    }

    public function render()
    {
        return view('components.form.ez-select');
    }
}
