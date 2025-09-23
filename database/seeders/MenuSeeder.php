<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            [
                'name'   => 'Patients',
                'route'  => null,
                'status' => 1,
                'order'  => 1,
                'icon'   => 'fa-duotone fa-fw fa-user',
                'items'  => [
                    [
                        'name'   => 'All Patients',
                        'route'  => 'patients.index',
                        'status' => 1,
                        'order'  => 1,
                    ],
                    [
                        'name'   => 'New Patient',
                        'route'  => 'patients.create',
                        'status' => 1,
                        'order'  => 2,
                    ],
                ]
            ],
            [
                'name'   => 'Appointments',
                'route'  => null,
                'status' => 1,
                'order'  => 2,
                'icon'   => 'fa-duotone fa-fw fa-calendar',
                'items'  => [
                    [
                        'name'   => 'All Appointments',
                        'route'  => 'appointments.calendar',
                        'status' => 1,
                        'order'  => 1,
                    ],
                    [
                        'name'   => 'New Appointment',
                        'route'  => 'appointments.calendar',
                        'params' => ['action' => 'create'],
                        'status' => 1,
                        'order'  => 2,
                    ]
                ]
            ],
            [
                'name'   => 'Consultations',
                'route'  => null,
                'status' => 1,
                'order'  => 3,
                'icon'   => 'fa-duotone fa-fw fa-stethoscope',
                'items'  => [
                    [
                        'name'   => 'All Consultations',
                        'route'  => 'consultations.index',
                        'status' => 1,
                        'order'  => 1,
                    ],
                    [
                        'name'   => 'Add Consultation',
                        'route'  => 'consultations.create',
                        'status' => 1,
                        'order'  => 2,
                    ],
                ]
            ],
            [
                'name'   => 'Prescriptions',
                'route'  => null,
                'status' => 1,
                'order'  => 4,
                'icon'   => 'fa-duotone fa-fw fa-prescription',
                'items'  => [
                    [
                        'name'   => 'All Prescriptions',
                        'route'  => 'prescriptions.index',
                        'status' => 1,
                        'order'  => 1,
                    ],
                    [
                        'name'   => 'New Prescription',
                        'route'  => 'prescriptions.create',
                        'status' => 1,
                        'order'  => 2,
                    ],
                ]
            ],
            [
                'name'   => 'Drugs',
                'route'  => null,
                'status' => 1,
                'order'  => 5,
                'icon'   => 'fa-duotone fa-fw fa-pills',
                'items'  => [
                    [
                        'name'   => 'All Drugs',
                        'route'  => 'drugs.index',
                        'status' => 1,
                        'order'  => 1,
                    ],
                    [
                        'name'   => 'Add Drug',
                        'route'  => 'drugs.create',
                        'status' => 1,
                        'order'  => 2,
                    ],
                ]

            ],
            [
                'name'   => 'Reports',
                'route'  => null,
                'status' => 1,
                'order'  => 5,
                'icon'   => 'fa-duotone fa-fw fa-analytics',
                'items'  => [
                    [
                        'name'   => 'Geral Reports',
                        'route'  => 'reports.index',
                        'status' => 1,
                        'order'  => 0,
                    ],
                    [
                        'name'   => 'Finantial',
                        'route'  => 'reports.index',
                        'status' => 1,
                        'params' => ['tab' => 'finantial'],
                        'order'  => 1,
                    ],
                    [
                        'name'   => 'Appointment',
                        'route'  => 'reports.index',
                        'params' => ['tab' => 'appointments'],
                        'status' => 1,
                        'order'  => 2,
                    ],
                    [
                        'name'   => 'Patient',
                        'route'  => 'reports.index',
                        'params' => ['tab' => 'customers'],
                        'status' => 1,
                        'order'  => 3,
                    ],
                    [
                        'name'   => 'Enrollments',
                        'route'  => 'reports.index',
                        'params' => ['tab' => 'enrollments'],
                        'status' => 1,
                        'order'  => 4,
                    ]
                ]
            ],
            [

                'name'   => 'Diagnostics',
                'route'  => null,
                'status' => 1,
                'order'  => 6,
                'icon'   => 'fa-duotone fa-fw fa-flask',
                'items'  => [
                    [
                        'name'   => 'All Diagnostics',
                        'route'  => 'diagnostics.index',
                        'status' => 1,
                        'order'  => 1,
                    ],
                    [
                        'name'   => 'Add Diagnostic',
                        'route'  => 'diagnostics.create',
                        'status' => 1,
                        'order'  => 2,
                    ],
                ]
            ],
            [
                'name'   => 'Finance',
                'route'  => null,
                'status' => 1,
                'order'  => 7,
                'icon'   => 'fa-duotone fa-fw fa-money-bill',
                'items'  => [
                    [
                        'name'   => 'Extract',
                        'route'  => 'finance.index',
                        'status' => 1,
                        'order'  => 1,
                    ],
//                    [
//                        'name'   => 'Incomes',
//                        'route'  => 'finance.index',
//                        'params' => ['type' => 1],
//                        'status' => 1,
//                        'order'  => 2,
//                    ],
//                    [
//                        'name'   => 'Expenses',
//                        'route'  => 'finance.index',
//                        'params' => ['type' => 2],
//                        'status' => 1,
//                        'order'  => 3,
//                    ],
                    [
                        'name'   => 'Cash flow',
                        'route'  => 'finance.flow',
                        'status' => 1,
                        'order'  => 3,
                    ]
                ]
            ],
//            [
//                'name'   => 'Users',
//                'route'  => null,
//                'status' => 1,
//                'order'  => 8,
//                'icon'   => 'fa-duotone fa-fw fa-users',
//                'items'  => [
//                    [
//                        'name'   => 'All Users',
//                        'route'  => 'users.index',
//                        'status' => 1,
//                        'order'  => 1,
//                    ],
//                    [
//                        'name'   => 'New User',
//                        'route'  => 'users.create',
//                        'status' => 1,
//                        'order'  => 2,
//                    ],
//                ]
//            ],
            [
                'name'   => 'Enrollments',
                'route'  => null,
                'status' => 1,
                'order'  => 4,
                'icon'   => 'fa-duotone fa-fw fa-users',
                'items'  => [

                    [
                        'name'   => 'Enrollments',
                        'route'  => 'enrollments.index',
                        'status' => 1,
                        'order'  => 0,
                    ],
                    [
                        'name'   => 'Teams',
                        'route'  => 'enrollments.classes.index',
                        'status' => 1,
                        'order'  => 1,
                    ],
                    [
                        'name'   => 'Rooms',
                        'route'  => 'enrollments.rooms.index',
                        'status' => 1,
                        'order'  => 2,
                    ],
                ]
            ],
            [
                'name'   => 'Settings',
                'route'  => null,
                'status' => 1,
                'order'  => 9,
                'icon'   => 'fa-duotone fa-fw fa-cog',
                'items'  => [
                    [
                        'name'   => 'Account',
                        'route'  => 'settings.account.edit',
                        'status' => 1,
                        'order'  => 1,
                    ]
                ]
            ]
        ];

        foreach ($items as $item) {
            $menu = Menu::create([
                'name'   => $item['name'],
                'route'  => $item['route'],
                'status' => $item['status'],
                'order'  => $item['order'],
                'icon'   => $item['icon'],
            ]);

            if (isset($item['items'])) {
                foreach ($item['items'] as $subItem) {
                    $menu->items()->create([
                        'name'   => $subItem['name'],
                        'route'  => $subItem['route'],
                        'params' => $subItem['params'] ?? [],
                        'status' => $subItem['status'],
                        'order'  => $subItem['order'],
                    ]);
                }
            }
        }
    }
}
