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
                'href' => route('master-data'),
                'show'=> true,
                'items' => [
                    [
                        'text' => 'Routes',
                        'active' => request()->routeIs('routes-overview'),
                        'href' => route('routes-overview'),
                        'show' => true,
                    ],
                    [
                        'text' => 'Vehicles',
                        'active' => request()->routeIs('vehicle-overview'),
                        'href' => route('vehicle-overview'),
                        'show' => true,
                    ],
                    [
                        'text' => 'Partners',
                        'active' => request()->routeIs('partners-overview'),
                        'href' => route('partners-overview'),
                        'show' => true,
                    ],
                    [
                        'text' => 'Extras',
                        'active' => request()->routeIs('extras-overview'),
                        'href' => route('extras-overview'),
                        'show' => true,
                    ],
                    [
                        'text' => 'Transfers',
                        'active' => request()->routeIs('transfer-overview'),
                        'href' => route('transfer-overview'),
                        'show' => true,
                    ],
                ]
            ],
            [
                'icon' => 'fas fa-euro-sign',
                'text' => 'Selling',
                'href' => route('selling'),
                'show'=> true,
                'items'=>[
                    [
                        'text' => 'Transfer Reservation',
                        'active' => request()->routeIs('internal-reservation'),
                        'href' => route('internal-reservation'),
                        'show' => true,
                    ],
                    [
                        'text' => 'Transfer Prices',
                        'active' => request()->routeIs('transfer-prices'),
                        'href' => route('transfer-prices'),
                        'show' => true,
                    ],
                ]
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
                'show'=> true,
                'items' => [
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
