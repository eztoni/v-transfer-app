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
                'icon' => 'fas fa-database',
                'text' => 'Master Data',
                'active' => request()->routeIs('age-groups',),
                'href' => route('master-data'),
                'show'=> true,
                'items' => [
                    [
                        'text' => 'Age Groups',
                        'active' => request()->routeIs('age-groups'),
                        'href' => route('age-groups'),
                        'show' => true,
                    ],

                ]
            ],
            [
                'icon' => 'fas fa-euro-sign',
                'text' => 'Selling',
                'active' => request()->routeIs('selling'),
                'href' => route('selling'),
                'show'=> true
            ],
            [
                'icon' => 'fas fa-book',
                'text' => 'Bookings',
                'active' => request()->routeIs('bookings'),
                'href' => route('bookings'),
                'show'=> true
            ],
            [
                'icon' => 'fas fa-chart-bar',
                'text' => 'Reports',
                'active' => request()->routeIs('reports'),
                'href' => route('reports'),
                'show'=> true
            ],
            [
                'icon' => 'fas fa-tools',
                'text' => 'Administration',
                'active' => request()->routeIs('admin.user-overview','admin.company-overview','admin.destinations','admin.points-overview'),
                'show'=> true,
                'items' => [
                    [
                        'text' => 'Company',
                        'active' => request()->routeIs('admin.company-overview'),
                        'href' => route('admin.company-overview'),
                        'show'=> \Auth::user()->hasRole(User::ROLE_SUPER_ADMIN)
                    ],
                    [
                        'text' => 'User Overview',
                        'active' => request()->routeIs('admin.user-overview'),
                        'href' => route('admin.user-overview'),
                        'show'=> \Auth::user()->hasAnyRole(User::ROLE_SUPER_ADMIN,User::ROLE_ADMIN)

                    ],
                    [
                        'text' => 'Destinations',
                        'active' => request()->routeIs('admin.destinations'),
                        'href' => route('admin.destinations'),
                        'show'=> \Auth::user()->hasAnyRole(User::ROLE_SUPER_ADMIN,User::ROLE_ADMIN)

                    ],
                    [
                        'text' => 'Points',
                        'active' => request()->routeIs('admin.points-overview'),
                        'href' => route('admin.points-overview'),
                        'show' =>\Auth::user()->hasAnyRole(User::ROLE_SUPER_ADMIN,User::ROLE_ADMIN),
                    ],
                ]
            ],


        ];
        return view('components.main-drawer',compact('menuItems'));
    }
}
