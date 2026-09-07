<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as OpenAI and more. This file provides the de facto location for this
    | type of information, allowing packages to have a conventional file
    | to locate the various service credentials.
    |
    */

    'openai' => [
        'api_url' => env('OPENAI_API', 'https://ai.rizuu.id/v1'),
        'api_key' => env('OPENAI_KEY'),
        'model' => env('OPENAI_MODEL', 'deepseek-v4-pro'),
        'models' => null,
        'timeout' => 120,
    ],

];
