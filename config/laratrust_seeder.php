<?php

return [
    'role_structure' => [
        'participant' => [
            'accounts.index',

            'settings.get-by-options',

            'customer',
            'dashboard.index',
            'tags.autocomplete',

            'users.update',
            'users.current',
            'users.autocomplete',

            'history.index',
            'history.read',
            'history.export',

            'errors.index',
            'errors.export',
            'errors.status',
            'errors.delete',

            'rex-models.index',
            'rex-models.rex-software-models',
            'rex-models.attach-rex-model',
            'rex-models.clear-children-rex-model',
            'rex-models.check-active-parser',
            'rex-models.check-model-id',
            'rex-models.update-rex-model',

            'log-requests.index',
        ],

        'plan-manager' => [
            'accounts.index',

            'settings.get-by-options',

            'customer',
            'dashboard.index',
            'tags.autocomplete',

            'users.update',
            'users.current',
            'users.autocomplete',

            'history.index',
            'history.read',
            'history.export',

            'errors.index',
            'errors.export',
            'errors.status',
            'errors.delete',

            'rex-models.index',
            'rex-models.rex-software-models',
            'rex-models.attach-rex-model',
            'rex-models.clear-children-rex-model',
            'rex-models.check-active-parser',
            'rex-models.check-model-id',
            'rex-models.update-rex-model',

            'log-requests.index',
        ],

        'service-provider' => [
            'accounts.index',

            'settings.get-by-options',

            'customer',
            'dashboard.index',
            'tags.autocomplete',

            'users.update',
            'users.current',
            'users.autocomplete',

            'history.index',
            'history.read',
            'history.export',

            'errors.index',
            'errors.export',
            'errors.status',
            'errors.delete',

            'rex-models.index',
            'rex-models.rex-software-models',
            'rex-models.attach-rex-model',
            'rex-models.clear-children-rex-model',
            'rex-models.check-active-parser',
            'rex-models.check-model-id',
            'rex-models.update-rex-model',

            'log-requests.index',
        ],

        'admin' => [
            'admin',
            'accounts.index',

            'dashboard.index',

            'settings.index',
            'settings.store',
            'settings.get-by-options',

            'tags.autocomplete',

            'audits.index',

            'users.index',
            'users.store',
            'users.update',
            'users.current',
            'users.destroy',
            'users.customers',
            'users.get',
            'users.autocomplete',
            'users.login-as',

            'permissions.index',

            'roles.index',
            'roles.store',
            'roles.update',
            'roles.destroy',
            'roles.get',

            'history.index',
            'history.read',
            'history.export',

            'errors.index',
            'errors.export',
            'errors.status',
            'errors.delete',

            'rex-models.index',
            'rex-models.rex-software-models',
            'rex-models.attach-rex-model',
            'rex-models.clear-children-rex-model',
            'rex-models.check-active-parser',
            'rex-models.check-model-id',
            'rex-models.update-rex-model',

            'log-requests.index',
        ],
    ],

    'permission_structure' => [],

    'access_levels' => [
        'admin' => 0,
        'participant' => 2,
        'plan-manager' => 2,
        'service-provider' => 2,
    ],
];
