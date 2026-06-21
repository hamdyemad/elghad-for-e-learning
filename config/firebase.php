<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Firebase project
    |--------------------------------------------------------------------------
    */

    'default' => env('FIREBASE_PROJECT', 'app'),

    /*
    |--------------------------------------------------------------------------
    | Firebase Project Configurations
    |--------------------------------------------------------------------------
    */

    'projects' => [

        'app' => [

            'credentials' => [
                'file' => env('FIREBASE_CREDENTIALS', storage_path('app/firebase/elghad-6744b-firebase-adminsdk-fbsvc-1e19758782.json')),
            ],

            'database' => [
                'url' => env('FIREBASE_DATABASE_URL'),
            ],

            'storage' => [
                'default_bucket' => env('FIREBASE_STORAGE_DEFAULT_BUCKET'),
            ],

        ],

    ],

];
