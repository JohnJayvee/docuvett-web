<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Gmail Configuration
    |--------------------------------------------------------------------------
    |
    |
    |
    |  Scopes Available:
    |
    |   * gmail - Read, send, delete, and manage your emails & labels
    |   * calendar - Read, delete, and manage your calendars & events
    |   * tasks - Read, delete, and manage your tasks
    |   * gdrive - Read, delete, and manage your files
    |   * contacts
    |
    |   Leaving the scopes empty fill use readonly
    |
    |
    */
    'project_id'    => env( 'GOOGLE_PROJECT_ID' ),
    'client_id'     => env( 'GOOGLE_CLIENT_ID' ),
    'client_secret' => env( 'GOOGLE_CLIENT_SECRET' ),
    'redirect_url'  => env( 'GOOGLE_REDIRECT_URI', '/' ),
    'scopes' => [
        'gmail',
        'calendar',
        'gdrive',
        'contacts',
        'tasks',
    ],
    'access_type'     => 'offline',
    'approval_prompt' => 'force',
    'cache_lifetime'  => env('GOOGLE_CACHE_LIFETIME', 1440),

    /*
    |--------------------------------------------------------------------------
    | Drive Configuration
    |--------------------------------------------------------------------------
    */
    'drive_root_directory' => env('GOOGLE_DRIVE_ROOT_DIRECTORY', 'root'),

    /*
    |--------------------------------------------------------------------------
    | Credentials File Name
    |--------------------------------------------------------------------------
    |
    |   :email to use, clients email on the file
    |
    |
    */
    'credentials_file_name' => env( 'GOOGLE_CREDENTIALS_NAME', 'gmail-json' ),

   /*
    *
    * Service account
    *
    */
    'service_account' => [
        'common_inbox' =>  env('GOOGLE_COMMON_INBOX'),
        'subject'      =>  env('GOOGLE_SERVICE_ACCOUNT_SUBJECT'),
        'credentials'  =>  env('GOOGLE_SERVICE_ACCOUNT_CREDENTIALS', '/keys/google_service_account.json'),
    ]
];