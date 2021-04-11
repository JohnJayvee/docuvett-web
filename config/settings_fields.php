<?php

use App\Models\Setting\Setting;

return [
    'app' => [
        Setting::CONFIG_KEY_COMPANY => [
            Setting::CONFIG_KEY_COMPANY_GENERAL => [
                Setting::FIELD_SETTING_TITLE => 'General',
                Setting::FIELD_SETTING_DESCRIPTION => 'General company settings.',
                Setting::FIELD_SETTING_ICON => 'glyphicon glyphicon-sunglasses',

                Setting::FIELD_SETTING_ELEMENTS => [

                    //Display Name
                    [
                        Setting::FIELD_TYPE => Setting::FIELD_TYPE_TEXT, // input fields type
                        Setting::FIELD_DATA => Setting::FIELD_TYPE_STRING, // data type, string, int, boolean
                        Setting::FIELD_NAME => 'display_name', // unique name for field
                        Setting::FIELD_LABEL => 'Display Name', // you know what label it is
                        Setting::FIELD_RULES => '', // validation rule of laravel
                        Setting::FIELD_CLASS => 'w-auto px-2', // any class for input
                        Setting::FIELD_VALUE => '', // default value if you want
                        Setting::FIELD_IS_HIDDEN => false // true if this field must be hidden in window.Laravel array
                    ],

                    //Trading Name
                    [
                        Setting::FIELD_TYPE => Setting::FIELD_TYPE_TEXT, // input fields type
                        Setting::FIELD_DATA => Setting::FIELD_TYPE_STRING, // data type, string, int, boolean
                        Setting::FIELD_NAME => 'trading_name', // unique name for field
                        Setting::FIELD_LABEL => 'Trading Name', // you know what label it is
                        Setting::FIELD_RULES => '', // validation rule of laravel
                        Setting::FIELD_CLASS => 'w-auto px-2', // any class for input
                        Setting::FIELD_VALUE => '', // default value if you want
                        Setting::FIELD_IS_HIDDEN => false // true if this field must be hidden in window.Laravel array
                    ],

                    //ABN
                    [
                        Setting::FIELD_TYPE => Setting::FIELD_TYPE_TEXT, // input fields type
                        Setting::FIELD_DATA => Setting::FIELD_TYPE_STRING, // data type, string, int, boolean
                        Setting::FIELD_NAME => 'abn', // unique name for field
                        Setting::FIELD_LABEL => 'ABN', // you know what label it is
                        Setting::FIELD_RULES => '', // validation rule of laravel
                        Setting::FIELD_CLASS => 'w-auto px-2', // any class for input
                        Setting::FIELD_VALUE => '', // default value if you want
                        Setting::FIELD_IS_HIDDEN => false // true if this field must be hidden in window.Laravel array
                    ],

                    //Email
                    [
                        Setting::FIELD_TYPE => Setting::FIELD_TYPE_TEXT, // input fields type
                        Setting::FIELD_DATA => Setting::FIELD_TYPE_STRING, // data type, string, int, boolean
                        Setting::FIELD_NAME => 'email', // unique name for field
                        Setting::FIELD_LABEL => 'Email', // you know what label it is
                        Setting::FIELD_RULES => 'required|email|unique:companies,email', // validation rule of laravel
                        Setting::FIELD_CLASS => 'w-auto px-2', // any class for input
                        Setting::FIELD_VALUE => '', // default value if you want
                        Setting::FIELD_IS_HIDDEN => false // true if this field must be hidden in window.Laravel array
                    ],

                    //Phone
                    [
                        Setting::FIELD_TYPE => Setting::FIELD_TYPE_TEXT, // input fields type
                        Setting::FIELD_DATA => Setting::FIELD_TYPE_STRING, // data type, string, int, boolean
                        Setting::FIELD_NAME => 'phone', // unique name for field
                        Setting::FIELD_LABEL => 'Phone', // you know what label it is
                        Setting::FIELD_RULES => 'phone:AUTO,AU', // validation rule of laravel
                        Setting::FIELD_CLASS => 'w-auto px-2', // any class for input
                        Setting::FIELD_VALUE => '', // default value if you want
                        Setting::FIELD_IS_HIDDEN => false // true if this field must be hidden in window.Laravel array
                    ],

                    //Address
                    [
                        Setting::FIELD_TYPE => Setting::FIELD_TYPE_TEXT, // input fields type
                        Setting::FIELD_DATA => Setting::FIELD_TYPE_STRING, // data type, string, int, boolean
                        Setting::FIELD_NAME => 'address', // unique name for field
                        Setting::FIELD_LABEL => 'Address', // you know what label it is
                        Setting::FIELD_RULES => '', // validation rule of laravel
                        Setting::FIELD_CLASS => 'w-auto px-2', // any class for input
                        Setting::FIELD_VALUE => '', // default value if you want
                        Setting::FIELD_IS_HIDDEN => false // true if this field must be hidden in window.Laravel array
                    ],
                ]
            ],
        ],
        Setting::CONFIG_KEY_APPOINTMENT => [
            Setting::CONFIG_KEY_APPOINTMENT_TIME => [
                Setting::FIELD_SETTING_TITLE => 'Time',
                Setting::FIELD_SETTING_DESCRIPTION => ' time settings.',
                Setting::FIELD_SETTING_ICON => 'glyphicon glyphicon-sunglasses',

                Setting::FIELD_SETTING_ELEMENTS => [

                    //Allowed Time In The Morning
                    [
                        Setting::FIELD_TYPE => Setting::FIELD_TYPE_TIME, // input fields type
                        Setting::FIELD_DATA => Setting::FIELD_TYPE_STRING, // data type, string, int, boolean
                        Setting::FIELD_NAME => Setting::VALUE_NAME_FIRST_APPOINTMENT_OF_DAY, // unique name for field
                        Setting::FIELD_LABEL => Setting::VALUE_LABEL_FIRST_APPOINTMENT_OF_DAY,
                        Setting::FIELD_RULES => 'required', // validation rule of laravel
                        Setting::FIELD_CLASS => 'w-auto px-2', // any class for input
                        Setting::FIELD_VALUE => '09:00', // default value if you want
                        Setting::FIELD_IS_HIDDEN => false // true if this field must be hidden in window.Laravel array
                    ],
                    //Allowed Time In The Noon
                    [
                        Setting::FIELD_TYPE => Setting::FIELD_TYPE_TIME, // input fields type
                        Setting::FIELD_DATA => Setting::FIELD_TYPE_STRING, // data type, string, int, boolean
                        Setting::FIELD_NAME => Setting::VALUE_NAME_SECOND_APPOINTMENT_OF_DAY, // unique name for field
                        Setting::FIELD_LABEL => Setting::VALUE_LABEL_SECOND_APPOINTMENT_OF_DAY,
                        Setting::FIELD_RULES => 'required', // validation rule of laravel
                        Setting::FIELD_CLASS => 'w-auto px-2', // any class for input
                        Setting::FIELD_VALUE => '12:00', // default value if you want
                        Setting::FIELD_IS_HIDDEN => false // true if this field must be hidden in window.Laravel array
                    ],
                    //Allowed Time After Noon
                    [
                        Setting::FIELD_TYPE => Setting::FIELD_TYPE_TIME, // input fields type
                        Setting::FIELD_DATA => Setting::FIELD_TYPE_STRING, // data type, string, int, boolean
                        Setting::FIELD_NAME => Setting::VALUE_NAME_THIRD_APPOINTMENT_OF_DAY, // unique name for field
                        Setting::FIELD_LABEL => Setting::VALUE_LABEL_THIRD_APPOINTMENT_OF_DAY,
                        Setting::FIELD_RULES => 'required', // validation rule of laravel
                        Setting::FIELD_CLASS => 'w-auto px-2', // any class for input
                        Setting::FIELD_VALUE => '15:00', // default value if you want
                        Setting::FIELD_IS_HIDDEN => false // true if this field must be hidden in window.Laravel array
                    ],
                    //Allowed Time In The Evening
                    [
                        Setting::FIELD_TYPE => Setting::FIELD_TYPE_TIME, // input fields type
                        Setting::FIELD_DATA => Setting::FIELD_TYPE_STRING, // data type, string, int, boolean
                        Setting::FIELD_NAME => Setting::VALUE_NAME_FOURTH_APPOINTMENT_OF_DAY, // unique name for field
                        Setting::FIELD_LABEL => Setting::VALUE_LABEL_FOURTH_APPOINTMENT_OF_DAY,
                        Setting::FIELD_RULES => 'required', // validation rule of laravel
                        Setting::FIELD_CLASS => 'w-auto px-2', // any class for input
                        Setting::FIELD_VALUE => '18:00', // default value if you want
                        Setting::FIELD_IS_HIDDEN => false // true if this field must be hidden in window.Laravel array
                    ],
                ]
            ],
        ],
        Setting::CONFIG_KEY_CONTACT => [
            Setting::CONFIG_KEY_CONTACT_GENERAL => [
                Setting::FIELD_SETTING_ELEMENTS => [
                    [
                        Setting::FIELD_TYPE         => Setting::FIELD_TYPE_TEXT,
                        Setting::FIELD_DATA         => Setting::FIELD_TYPE_STRING,
                        Setting::FIELD_NAME         => Setting::CONTACT_BIRTHDAY,
                        Setting::FIELD_LABEL        => 'Contact Birthday',
                        Setting::FIELD_RULES        => '',
                        Setting::FIELD_CLASS        => '',
                        Setting::FIELD_VALUE        => '',
                        Setting::FIELD_IS_HIDDEN    => false
                    ],
                    [
                        Setting::FIELD_TYPE         => Setting::FIELD_TYPE_TEXT,
                        Setting::FIELD_DATA         => Setting::FIELD_TYPE_STRING,
                        Setting::FIELD_NAME         => Setting::CONTACT_SITE_EVENT,
                        Setting::FIELD_LABEL        => 'Site Event',
                        Setting::FIELD_RULES        => '',
                        Setting::FIELD_CLASS        => '',
                        Setting::FIELD_VALUE        => '',
                        Setting::FIELD_IS_HIDDEN    => false
                    ],
                    [
                        Setting::FIELD_TYPE         => Setting::FIELD_TYPE_TEXT,
                        Setting::FIELD_DATA         => Setting::FIELD_TYPE_STRING,
                        Setting::FIELD_NAME         => Setting::CONTACT_SECOND_ADDRESS,
                        Setting::FIELD_LABEL        => 'Second Address',
                        Setting::FIELD_RULES        => '',
                        Setting::FIELD_CLASS        => '',
                        Setting::FIELD_VALUE        => '',
                        Setting::FIELD_IS_HIDDEN    => false
                    ],
                    [
                        Setting::FIELD_TYPE         => Setting::FIELD_TYPE_TEXT,
                        Setting::FIELD_DATA         => Setting::FIELD_TYPE_STRING,
                        Setting::FIELD_NAME         => Setting::CONTACT_ABN,
                        Setting::FIELD_LABEL        => 'ABN',
                        Setting::FIELD_RULES        => '',
                        Setting::FIELD_CLASS        => '',
                        Setting::FIELD_VALUE        => '',
                        Setting::FIELD_IS_HIDDEN    => false
                    ],
                ]
            ],
        ],
        Setting::CONFIG_KEY_CONTACTS => [
            Setting::CONFIG_KEY_CONTACTS_GENERAL => [
                Setting::FIELD_SETTING_ELEMENTS => [
                    [
                        Setting::FIELD_TYPE         => Setting::FIELD_TYPE_TEXT,
                        Setting::FIELD_DATA         => Setting::FIELD_TYPE_STRING,
                        Setting::FIELD_NAME         => Setting::WASH_LEADS,
                        Setting::FIELD_LABEL        => 'Wash Leads',
                        Setting::FIELD_RULES        => '',
                        Setting::FIELD_CLASS        => '',
                        Setting::FIELD_VALUE        => '',
                        Setting::FIELD_IS_HIDDEN    => false
                    ],
                    [
                        Setting::FIELD_TYPE         => Setting::FIELD_TYPE_TEXT,
                        Setting::FIELD_DATA         => Setting::FIELD_TYPE_STRING,
                        Setting::FIELD_NAME         => Setting::TRANSFER,
                        Setting::FIELD_LABEL        => 'Transfer',
                        Setting::FIELD_RULES        => '',
                        Setting::FIELD_CLASS        => '',
                        Setting::FIELD_VALUE        => '',
                        Setting::FIELD_IS_HIDDEN    => false
                    ],
                ]
            ],
        ],
    ],
];
