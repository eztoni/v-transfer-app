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
                'href' => route('dashboard'),
                'show' => \Auth::user()->hasAnyRole(User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN),
                'items' => [
                    [
                        'text' => 'Transfers',
                        'active' => request()->routeIs('transfer-overview'),
                        'href' => route('transfer-overview'),
                        'show' => \Auth::user()->hasAnyRole(User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN),
                    ],
                    [
                        'text' => 'Transfer Prices',
                        'active' => request()->routeIs('transfer-prices'),
                        'href' => route('transfer-prices'),
                        'show' => \Auth::user()->hasAnyRole(User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN),
                    ],
                    [
                        'text' => 'Routes',
                        'active' => request()->routeIs('routes-overview'),
                        'href' => route('routes-overview'),
                        'show' => \Auth::user()->hasAnyRole(User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN),
                    ],
                    [
                        'text' => 'Extras',
                        'active' => request()->routeIs('extras-overview'),
                        'href' => route('extras-overview'),
                        'show' => \Auth::user()->hasAnyRole(User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN),
                    ],
                    [
                        'text' => 'Vehicles',
                        'active' => request()->routeIs('vehicle-overview'),
                        'href' => route('vehicle-overview'),
                        'show' => \Auth::user()->hasAnyRole(User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN),
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
                'show' => \Auth::user()->hasAnyRole(User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN),
                'items' => [
                    [
                        'text' => 'Partner Report',
                        'active' => request()->routeIs('partner-report'),
                        'href' => route('partner-report'),
                        'show' => \Auth::user()->hasAnyRole(User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN,User::ROLE_USER)
                    ],
                    [
                        'text' => 'PPOM Report',
                        'active' => request()->routeIs('ppom-report'),
                        'href' => route('ppom-report'),
                        'show' => \Auth::user()->hasAnyRole(User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN,User::ROLE_USER)
                    ],
                    [
                        'text' => 'RPO Report',
                        'active' => request()->routeIs('rpo-report'),
                        'href' => route('rpo-report'),
                        'show' => \Auth::user()->hasAnyRole(User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN,User::ROLE_USER)
                    ],
                    [
                        'text' => 'Reception Daily Report',
                        'active' => request()->routeIs('partner-daily'),
                        'href' => route('partner-daily'),
                        'show' => \Auth::user()->hasAnyRole(User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN,User::ROLE_USER)
                    ],

                ]
            ],
            [
                'icon' => 'fas fa-tools',
                'text' => 'Administration',
                'show' => \Auth::user()->hasAnyRole(User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN),
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
                        'show' => \Auth::user()->hasAnyRole(User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN),
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
