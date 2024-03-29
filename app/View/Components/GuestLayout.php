<?php

namespace App\View\Components;

use Illuminate\View\Component;

class GuestLayout extends Component
{

    public $title;

    /**
     * Create a new component instance.
     *
     * @param $title
     */
    public function __construct($title = 'EZ booker')
    {
        $this->title = $title;
    }

    /**
     * Get the view / contents that represents the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('layouts.guest');
    }
}
