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
    |  Credentials File Name
    |
    */
    'project_id'            => env('GOOGLE_PROJECT_ID'),
    'client_id'             => env('GOOGLE_CLIENT_ID'),
    'client_secret'         => env('GOOGLE_CLIENT_SECRET'),
    'redirect_url'          => env('GOOGLE_REDIRECT_URI', '/'),
    'scopes'                => [
        'gmail',
        'calendar',
        'gdrive',
        'contacts',
        'tasks',
    ],
    'access_type'           => 'offline',
    'approval_prompt'       => 'force',
    /*
    |--------------------------------------------------------------------------
    | Credentials File Name
    |--------------------------------------------------------------------------
    |
    |   :email to use, clients email on the file
    |
    |
    */
    'credentials_file_name' => env('GOOGLE_CREDENTIALS_NAME', 'gmail-json'),

    /*
     *
     * Service account
     *
     */
    'service_account' => [
        'subject'     =>  env('GOOGLE_SERVICE_ACCOUNT_SUBJECT', 'niagara-test@niagara-235912.iam.gserviceaccount.com'),
        'credentials' =>  env('GOOGLE_SERVICE_ACCOUNT_CREDENTIALS', 'keys/niagara-ac5eafb2e461.json'),
    ]

];
