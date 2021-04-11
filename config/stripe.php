<?php
/**
 * Created by PhpStorm.
 * User: denis
 * Email: gp.neutron@gmail.com
 */

return [
    'api_key' => env('STRIPE_SECRET_KEY', ''),
    'publish_key' => env('STRIPE_PUBLISH_KEY', ''),
    'client_name' => env('STRIPE_CLIENT_NAME', ''),
    'grace_period' => env('STRIPE_GRACE_PERIOD', 0),

    'plans' => [
        'one time' => 30000,
        'year' => 10000,
        'month' => 1500,
        'week' => 500,
        'day' => 100
    ],

    'webhooks' => [
        'modules.success-payment' => 'invoice.payment_succeeded',
        'modules.error-payment' => 'invoice.payment_failed',
        'modules.success-onetime-payment' => 'charge.succeeded',
        'modules.error-onetime-payment' => 'charge.failed'
    ],
];