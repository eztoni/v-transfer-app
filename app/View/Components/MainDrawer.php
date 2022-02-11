<?php

namespace App\View\Components;

use App\Models\User;
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
                'href' => route('dashboard'),
                'show'=> true
            ],
            [
                'icon' => 'fas fa-tools',
                'text' => 'Settings',
                'active' => request()->routeIs('admin.company-overview','admin.destinations'),
                'show'=> true,
                'items' => [
                    [
                        'text' => 'Company users',
                        'active' => request()->routeIs('admin.company-overview'),
                        'href' => route('admin.company-overview'),
                        'show'=> \Auth::user()->hasRole(User::ROLE_SUPER_ADMIN)
                    ],
                    [
                        'text' => 'Destinations',
                        'active' => request()->routeIs('admin.destinations'),
                        'href' => route('admin.destinations'),
                        'show'=> \Auth::user()->hasAnyRole(User::ROLE_SUPER_ADMIN,User::ROLE_ADMIN)

                    ],
                ]
            ],


        ];
        return view('components.main-drawer',compact('menuItems'));
    }
}
