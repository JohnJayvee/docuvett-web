<?php

use App\Services\ReportGenerator\ConsultantsReportGenerator;
use App\Services\ReportGenerator\EventsReportGenerator;
use App\Services\ReportGenerator\CallCenterReportGenerator;
use App\Services\ReportGenerator\LeadListsReportGenerator;
use App\Services\ReportGenerator\SalesTrackerReportGenerator;
use App\Services\ReportGenerator\OverallReportGenerator;
use App\Services\ReportGenerator\SalesReportGenerator;
use App\Services\ReportGenerator\MarketerLeadsGenerator;
use App\Services\ReportGenerator\MarketerLeadsDetailedGenerator;

$exportArray = [];

// Properties and constants from this array will be omitted in result array.
// Specify the class name and list of constants or variables from this class
$exceptions = [
    // classname => Array of properties names and constants names
    // App\Models\Property\Property::class => ['addressSelect']
];

// custom classes not from app/Models directory
$customClasses = [
    // \App\Libraries\MailSystem\Mailers\Mailjet\Mailjet::class
    OverallReportGenerator::class,
    CallCenterReportGenerator::class,
    ConsultantsReportGenerator::class,
    EventsReportGenerator::class,
    SalesReportGenerator::class,
    LeadListsReportGenerator::class,
    SalesTrackerReportGenerator::class,
    MarketerLeadsGenerator::class,
    MarketerLeadsDetailedGenerator::class,
];

// add constants/variables from Models classes
foreach (scandir(app_path('Models')) as $modelDirectoryName) {
    // dirs with name . or .. will be omitted
    if (in_array($modelDirectoryName, ['.', '..'])) {
        continue;
    }

    $className = "App\Models\\$modelDirectoryName\\$modelDirectoryName";

    generateClassData($exportArray, $exceptions, $className);
}

// add constants/variables from custom classes
foreach ($customClasses as $className) {
    generateClassData($exportArray, $exceptions, $className);
}

// CUSTOM VARIABLES
$exportArray = array_merge($exportArray, [
    // 'customVariable' => 100
    'config' => [
        'google' => [
            'label_colors' => \App\Libraries\GSuite\Services\Label\Label::LABEL_COLORS,
            'firebase' => [
                'sender_id' => env('FIREBASE_SENDER_ID'),
                'vapid_key' => env('FIREBASE_VAPID_KEY'),
                'api_key' => env('FIREBASE_API_KEY'),
                'project_id' => env('FIREBASE_PROJECT_ID'),
                'app_id' => env('FIREBASE_APP_ID'),
            ],
            'common_inbox' =>  env('GOOGLE_COMMON_INBOX'),
            'map_api_key' => env('GOOGLE_MAPS_API_KEY', ''),
            'openweathermap_api_key' => env('OPENWEATHERMAP_API_KEY', ''),
            'drive' => [
                'url' => \App\Libraries\GSuite\Services\Drive\Drive::URL
            ]
        ],
    ]
]);

return $exportArray;
