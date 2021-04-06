<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'twilio' => [
        'sid' => env('TWILIO_SID'),
        'token' => env('TWILIO_TOKEN'),
        'sender_number' => env('TWILIO_SENDER_NUMBER'),
        'incoming_auth_token' => env('TWILIO_INCOMING_AUTH_TOKEN')
    ],

    'sendgrid' => [
        'api_key' => env('SENDGRID_API_KEY'),
        'templates' => [
            'registration_email' => env('SENDGRID_TEMPLATE_REGISTRATION_EMAIL', 'd-08ead00308b54ab8a6d69c5d7e42e222'),
            'registration_confirmation' => env('SENDGRID_TEMPLATE_REGISTRATION_CONFIRMATION', 'd-0bbae2c707e448b5add42dcc4915fdd9'),
            'registration_referrer_notification' => env('SENDGRID_TEMPLATE_REGISTRATION_REFERRER_NOTIFICATION', 'd-dd30afcc56194d94aaba5277e0627182'),
            'appointment_confirmed' => env('SENDGRID_TEMPLATE_APPOINTMENT_CONFIRMED', 'd-19bb0916f9684cbba4cc33832b2de218'),
            'reminder_notification' => env('SENDGRID_TEMPLATE_REMINDER_NOTIFICATION', 'd-e369202db954401eafa97c8a1307efd2'),
            'access_data' => env('SENDGRID_TEMPLATE_ACCESS_DATA', 'd-682b2cd5f3d84c03b9bb92fd8a9e2c6f'),
            'appointment_consultant' => env('SENDGRID_TEMPLATE_CONSULTANT', 'd-809e19ade502492ba7ecd89521c8fbfe'),
        ]
    ],
    'abn_lookup' => [
        'guid' => env('ABN_LOOKUP_API_GUID'),
        'base_url' => 'https://abr.business.gov.au/json/',
        'abn' => [
            'base' => 'AbnDetails.aspx',
            'params' => [
                'callback' => 'abnCallback',
            ]
        ],
        'acn' => [
            'base' => 'AcnDetails.aspx',
            'params' => [
                'callback' => 'acnCallback',
            ]
        ],
        'name' => [
            'base' => 'MatchingNames.aspx',
            'params' => [
                'callback' => 'nameCallback',
            ]
        ],
    ],
    'xero' => [
        'auth2' => [
            'client_id' =>  env('XERO_CLIENT_ID'),
            'client_secret' =>  env('XERO_CLIENT_SECRET'),
            'callback_url' =>  env('XERO_CALLBACK_URL'),
        ]
    ]
];
