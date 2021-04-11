<?php

return [
    'path' => base_path() . '/app/Modules',
    'base_namespace' => 'App\Modules',

    /**
     * Modules
     */
    'modules' => [
        '_common_' => [
            'Account',
            'Auth',
            'History',
            'Errors',
            'Users',
            'LogRequest',
            'Dashboard',
        ],

        'Admin' => [
            'Audits',
            'Permissions',
            'Roles',
            'Users',
            'Settings',
            'RexModel',
        ],
    ]
];
