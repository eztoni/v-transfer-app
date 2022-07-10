<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use WireUi\View\Components\FormComponent;

class Flatpickr extends FormComponent
{

    public function __construct(
        public ?string $label = null,
        public ?string $hint = null,
        public ?string $minDate = 'today',
        public ?string $dateFormat = 'd.m.Y H:i',
        public ?bool $enableTime = true,
        public ?bool $time24 = true,
        public ?string $defaultDate = '',
        public ?bool $noCalendar = false,
    )
    {

    }

    public function defaultClasses(): string
    {
        return 'form-select block w-full pl-3 pr-10 py-2 text-base sm:text-sm shadow-sm
                rounded-md border bg-white focus:ring-1 focus:outline-none
                dark:bg-secondary-800 dark:border-secondary-600 dark:text-secondary-400';
    }

    public function colorClasses(): string
    {
        return 'border-secondary-300 focus:ring-primary-500 focus:border-primary-500';
    }

    public function errorClasses(): string
    {
        return 'border-negative-400 focus:ring-negative-500 focus:border-negative-500 text-negative-500
                dark:border-negative-600 dark:text-negative-500';
    }
//    public function render(): View
//    {
//        return view('components.flatpickr');
//    }

    protected function getView(): string
    {
       return 'components.flatpickr';
    }
}
