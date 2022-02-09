<?php

namespace App\View\Components;

use Illuminate\View\Component;

class MainDrawer extends Component
{

    public function render()
    {
        $menuItems = [
            [
                'icon' => 'fas fa-tachometer-alt',
                'text' => 'Pregled',
                'active' => request()->routeIs('dashboard'),
                'href' => route('dashboard')
            ],
            [
                'icon' => 'fas fa-tools',
                'text' => 'Settings',
                'active' => request()->routeIs('admin.edit-user','admin.destinations'),
                'href' => route('dashboard'),
                'items' => [
                    [
                        'text' => 'Company users',
                        'active' => request()->routeIs('admin.edit-user'),
                        'href' => route('admin.edit-user')
                    ],
                    [
                        'text' => 'Destinations',
                        'active' => request()->routeIs('admin.destinations'),
                        'href' => route('admin.destinations')
                    ],
                ]
            ],


        ];
        return view('components.main-drawer',compact('menuItems'));
    }
}
