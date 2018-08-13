<?PHP

return [
        'billing' => [
                'name' => 'Seat IRS',
                'icon' => 'fa-credit-card',
                'route_segment' => 'billing',
                'permission' => 'billing.view',
                'route' => 'billing.view',

        'entries'       => [
            'billing' => [
                'name' => 'Billing Statements',
                'icon' => 'fa-money',
                'route_segment' => 'billing',
                'route' => 'billing.view',
                'permission' => 'biling.view'
            ],
            'settings' => [
                'name' => 'Settings',
                'icon' => 'fa-gear',
                'route_segment' => 'billing',
                'route' => 'billing.settings',
                'permission' => 'billing.admin'
            ],

        ],
        ],
];

