<?php

return [
    'email_pusher' => [
        'secret_key' => env('EMAIL_PUSHER_SECRET_KEY', 'tejspbYsy2290TzX'),
    ],

    'mailer' => [
        'title'                => 'MailJet',
        'class'                => \App\Libraries\Notification\MailJetNotification::class,
        'api_key'              => env('MAILJET_API_KEY', '424eb4e3181199fd42147c1421679807'),
        'api_secret'           => env('MAILJET_API_SECRET', '886a62b17b787ee14078b06cfb6a93cc'),
        'sender_name'          => env('APP_NAME'),
        'sender_email'         => env('MAILJET_SENDER_EMAIL', 'sender@toppako.aws.hicalibertest.com.au'),
        'listener_handler'     => '',
        'remote_templates_url' => 'https://app.mailjet.com/templates/transactional',
    ],

    'sms' => [
        'class'         => \App\Libraries\Notification\TwilioNotification::class,
        'sid'           => env('TWILIO_SID', 'AC692351e8ec084bab96a8748d6d7d674f'),
        'token'         => env('TWILIO_TOKEN', 'ec0373c66a2315a1ff352db67d631621'),
        'sender_number' => env('TWILIO_SENDER_NUMBER', '+61451562881'),

        'store_on_send'       => true,
        'store_model'         => App\Models\SmsIncomingEvents\SmsIncomingEvents::class,
        'listener_handler'    => '',

        /* Application specific config */

        //Special token to filter any queries from any non prohibited sources
        'incoming_auth_token' => env('TWILIO_INCOMING_AUTH_TOKEN', 'bbc50612-101f-4aee-b293-c58254f83e3f'),
    ],

];