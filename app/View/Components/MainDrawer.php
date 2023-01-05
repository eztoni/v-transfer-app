<?php

namespace App\View\Components;

use App\Models\User;
use Illuminate\Support\Arr;
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
                'show' => true,
                'items' => [
                    [
                        'text' => 'Transfers',
                        'active' => request()->routeIs('transfer-overview'),
                        'href' => route('transfer-overview'),
                        'show' => true,
                    ],
                    [
                        'text' => 'Transfer Prices',
                        'active' => request()->routeIs('transfer-prices'),
                        'href' => route('transfer-prices'),
                        'show' => true,
                    ],
                    [
                        'text' => 'Routes',
                        'active' => request()->routeIs('routes-overview'),
                        'href' => route('routes-overview'),
                        'show' => true,
                    ],
                    [
                        'text' => 'Extras',
                        'active' => request()->routeIs('extras-overview'),
                        'href' => route('extras-overview'),
                        'show' => true,
                    ],
                    [
                        'text' => 'Vehicles',
                        'active' => request()->routeIs('vehicle-overview'),
                        'href' => route('vehicle-overview'),
                        'show' => true,
                    ],


                ]
            ],
            [
                'icon' => 'fas fa-euro-sign',
                'text' => 'Transfer Reservation',
                'active' => request()->routeIs('internal-reservation'),
                'href' => route('internal-reservation'),
                'show' => true,
            ],
            [
                'icon' => 'fas fa-book',
                'text' => 'Bookings',
                'active' => request()->routeIs('bookings'),
                'href' => route('bookings'),
                'show' => true
            ],
            [
                'icon' => 'fas fa-chart-bar',
                'text' => 'Reports',
                'show' => true,
                'items' => [
                    [
                        'text' => 'Reservation Report',
                        'active' => request()->routeIs('reports'),
                        'href' => route('reports'),
                        'show' => \Auth::user()->hasAnyRole(User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN)
                    ],
                    [
                        'text' => 'Partner Reporting',
                        'active' => request()->routeIs('partner-reports'),
                        'href' => route('partner-reports'),
                        'show' => \Auth::user()->hasAnyRole(User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN)
                    ],

                ]
            ],
            [
                'icon' => 'fas fa-tools',
                'text' => 'Administration',
                'show' => true,
                'items' => [
                    [
                        'text' => 'User Overview',
                        'active' => request()->routeIs('admin.user-overview'),
                        'href' => route('admin.user-overview'),
                        'show' => \Auth::user()->hasAnyRole(User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN)

                    ],
                    [
                        'text' => 'Partners',
                        'active' => request()->routeIs('admin.partners-overview'),
                        'href' => route('admin.partners-overview'),
                        'show' => true,
                    ],
                    [
                        'text' => 'Destinations',
                        'active' => request()->routeIs('admin.destinations'),
                        'href' => route('admin.destinations'),
                        'show' => \Auth::user()->hasAnyRole(User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN)

                    ],
                    [
                        'text' => 'Pick-up and Drop-off Locations',
                        'active' => request()->routeIs('admin.points-overview'),
                        'href' => route('admin.points-overview'),
                        'show' => \Auth::user()->hasAnyRole(User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN),
                    ],

                ]
            ],


        ];
        return view('components.main-drawer', compact('menuItems'));
    }

    public function isSubActive(array $subItems)
    {
        return !empty(Arr::where($subItems, function ($value, $key) {
            return $value['active'];
        }));
    }
}
