<?php

namespace App\View\Components\Form;

use Illuminate\View\Component;
use function view;

class EzTextInput extends Component
{
    public string $label;
    public $model;
    public $errorString;
    public $parentDivClasses;
    public $labelClasses;
    public $labelTextClasses;
    public $placeholder;
    public $value = '';
    public $sm;

    public function __construct(
        $label,
        $model = '',
        $errorString = null,
        $parentDivClasses = null,
        $labelClasses = null,
        $labelTextClasses = null,
        $value = '',
        $sm=false,
    )
    {
        $this->sm = $sm;
        $this->label = $label;
        $this->value = $value;
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
