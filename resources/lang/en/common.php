<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Common Used Language Lines
    |--------------------------------------------------------------------------
    */

    'non_member' =>
        'You have attempted to connect with one of our ' . config('app.name') . ' members.' . PHP_EOL .
        'To connect with them via ' . config('app.name') . ', we require you to register an account. Please visit us at ' . config('app.wordpress_website_url'),

    'you_are_not_active_member' => "We noticed that you are trying to send a message to one of our members. ".config('app.name')." is a private and secure communication platform, where members invite people to connect with them. Please ask the person you are messaging to add you as a contact to their ".config('app.name')." account.".PHP_EOL."Alternatively, please visit ".config('app.url')." to create a member account and invite the person to connect with you.".PHP_EOL."Kind regards, The ".config('app.name')." Team.",

    'recipient_is_deleted_from_his_connections' => "Sorry, recipient has deleted you from his connection list",

    'recipient_is_marked_connection_inactive' => "Sorry, recipient has marked connection with you as not active",

    'recipient_is_not_active_member' => "Sorry recipient is not active " . config('app.name') . ' member',

    'recipient_dont_have_subscription' => "Sorry, recipient doesn't have active " . config('app.name') . ' membership subscription',

    'you_dont_have_subscription' => "Sorry, you don't have active " . config('app.name') . ' membership subscription.',

    'message_not_polite' => "Sorry, your message is blocked. It does not look polite enough." . PHP_EOL .
                            'Try to paraphrase, and try again.',

    'message_blocked' => 'Sorry, your message is blocked and now being moderating.',

    '1_message_blocked' => 'Your message was not sent.' . PHP_EOL . 'May include offensive/threatening language or images. Please change and resend.',

    '2_message_blocked' => 'Your message was not sent.' . PHP_EOL . 'May include offensive/threatening language or images. Please change and resend. If a 3rd message can not be sent, your account will go into “time out”.',

    '3_message_blocked' => 'You have tried to send 3 messages in ' . config('notification.message.period') . ' minutes. Your account is in “time out” for ' . config('notification.message.timeout') . ' minutes. Please change your message and try again later.',

    'message_not_sent' => 'Your message was not sent.' . PHP_EOL . 'May include offensive/threatening language or images. Please change and resend.',

    'auto_blocked' => 'Automatically filtered incoming message',

    'message_delayed' => 'Sorry, your message is delayed. Recipient will obtain it at :date',

    'message_approved_subject' => 'Your message was approved by Moderator',

    'message_approved' => 'Your message was approved by Moderator and successfully sent to recipient',

    'message_approved_with_delay' => 'Your message was approved by Moderator and will be sent to recipient at :date',

    'account_on_time_out' => 'Your account is in “time out” till :time',

    'sms_disabled' => 'SMS communication disabled',

    'email_disabled' => 'Email communication disabled',

    'recipient_is_suspended' => "Sorry, recipient's account is suspended",

    'you_are_suspended' => 'Sorry, your account is suspended',

    'photos_limit' => "Sorry, you've upload maximum number of photos (:limit)",

    'send_sms_message' => "Your " . config('app.name') . " connection has chosen not to receive messages via email. Please send your message via SMS using :phone",

    'send_email_message' => "Your " . config('app.name') . " connection has chosen not to receive messages via SMS. Please send your message via email using :email",

    'attempt_to_send_sms' => ":name sent a message via SMS. Please change your notification settings or advise :name to only use email.",

    'attempt_to_send_email' => ":name sent a message via email. Please change your notification settings or advise :name to only use SMS.",
    'successfully_created' => ':model successfully created',
    'successfully_updated' => ':model successfully updated',
    'successfully_deleted' => ':model successfully deleted',
    'cant_delete' => 'Can\'t delete this :model',
    'something_wrong' => 'Something went wrong',
    'user_has_no_email' => 'The given user has no email',
    'restrict_dynamic_properties' => 'Dynamic properties not allowed. Please extend class :class',
];
