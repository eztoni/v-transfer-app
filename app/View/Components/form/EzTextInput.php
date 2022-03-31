<?php

namespace App\View\Components\form;

use Illuminate\View\Component;
use function view;

class EzTextInput extends Component
{
    public string $label;
    public $model;
    public $errorString ;
    public $parentDivClasses ;
    public $labelClasses ;
    public $labelTextClasses;
    public $placeholder;


    public function __construct(
        $label,
        $model,
        $errorString=null,
        $parentDivClasses = null,
        $labelClasses = null,
        $labelTextClasses = null)
    {
        $this->label = $label;
        $this->model = $model;
        $this->errorString = empty($errorString) ? $this->model : $errorString;
        $this->parentDivClasses = $parentDivClasses;
        $this->labelClasses = $labelClasses;
        $this->labelTextClasses = $labelTextClasses;
    }


    public function render()
    {
        return view('components.form.ez-text-input');
    }
}
