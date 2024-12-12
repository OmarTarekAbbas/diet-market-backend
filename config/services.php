<?php
return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'firebase' => [
        'baseUrl' => 'https://fcm.googleapis.com/fcm/send',
        'serverKey' => 'AAAAxgcG6Jk:APA91bF-kq-eZgyh54c9BElG9ocxIpTuX6a_GVgUrFyyLobx4YteR7P_Z-57bGIHqQvq9W2P6wElMi3K-flV9YixkETh_mkgdmf-_yIBQ-MkiLfo7ae8Bly1L4KWVd5asU_SO2D334hU',
    ],

    'payments' => [
        'hyperPay' => [
            'mode' => env('HYPER_PAY_MODE'),
            'currency' => env('HYPER_PAY_CURRENCY'),
            'saveCards' => env('HYPER_PAY_SAVE_CARDS'),
            'data' => [
                'live' => [
                    'url' => env('HYPER_PAY_LIVE_URL'),
                    'accessToken' => env('HYPER_PAY_LIVE_ACCESS_TOKEN'),
                    'entityId' => [
                        'VISA' => env('HYPER_PAY_LIVE_VISA_ENTITY_ID'),
                        'MADA' => env('HYPER_PAY_LIVE_MADA_ENTITY_ID'),
                    ],
                ],
                'sandbox' => [
                    'url' => env('HYPER_PAY_SANDBOX_URL'),
                    'accessToken' => env('HYPER_PAY_SANDBOX_ACCESS_TOKEN'),
                    'entityId' => [
                        'VISA' => env('HYPER_PAY_SANDBOX_VISA_ENTITY_ID'),
                        'MADA' => env('HYPER_PAY_SANDBOX_MADA_ENTITY_ID'),
                    ],
                ],
            ]
        ],
        'moyasar' => [
            'mode' => env('MOYASAR_MODE'),
            'data' => [
                'live' => [
                    'url' => env('MOYASAR_LIVE_URL'),
                    'secretKey' => env('MOYASAR_LIVE_SECRET_KEY'),
                    'publishableKey' => env('MOYASAR_LIVE_PUBLISHABLE_KEY'),
                    'version' => env('MOYASAR_LIVE_VERSION'),
                    'apiVersion' => env('MOYASAR_LIVE_API_VERSION'),
                ],
                'sandbox' => [
                    'url' => env('MOYASAR_SANDBOX_URL'),
                    'secretKey' => env('MOYASAR_SANDBOX_SECRET_KEY'),
                    'publishableKey' => env('MOYASAR_SANDBOX_PUBLISHABLE_KEY'),
                    'version' => env('MOYASAR_SANDBOX_VERSION'),
                    'apiVersion' => env('MOYASAR_SANDBOX_API_VERSION'),
                ],
            ]
        ],
        'noonPayments' => [
            'mode' => env('NOON_PAYMENTS_MODE'),
            'currency' => env('NOON_PAYMENTS_CURRENCY'),
            'data' => [
                'live' => [
                    'url' => env('NOON_PAYMENTS_LIVE_URL'),
                    'apiVersion' => env('NOON_PAYMENTS_LIVE_API_VERSION'),
                    'saveCards' => env('NOON_PAYMENTS_LIVE_SAVE_CARDS'),
                    'businessId' => env('NOON_PAYMENTS_LIVE_BUSINESS_ID'),
                    'appName' => env('NOON_PAYMENTS_LIVE_APP_NAME'),
                    'appKey' => env('NOON_PAYMENTS_LIVE_APP_KEY'),
                    "channel" => env('NOON_PAYMENTS_LIVE_CHANNEL'),
                    "category" => env('NOON_PAYMENTS_LIVE_CATEGORY'),
                    "locale" => env('NOON_PAYMENTS_LIVE_LOCALE'),
                    "styleProfile" => env('NOON_PAYMENTS_LIVE_STYLE_PROFILE'),
                ],
                'sandbox' => [
                    'url' => env('NOON_PAYMENTS_SANDBOX_URL'),
                    'apiVersion' => env('NOON_PAYMENTS_SANDBOX_API_VERSION'),
                    'saveCards' => env('NOON_PAYMENTS_SANDBOX_SAVE_CARDS'),
                    'businessId' => env('NOON_PAYMENTS_SANDBOX_BUSINESS_ID'),
                    'appName' => env('NOON_PAYMENTS_SANDBOX_APP_NAME'),
                    'appKey' => env('NOON_PAYMENTS_SANDBOX_APP_KEY'),
                    "channel" => env('NOON_PAYMENTS_SANDBOX_CHANNEL'),
                    "category" => env('NOON_PAYMENTS_SANDBOX_CATEGORY'),
                    "locale" => env('NOON_PAYMENTS_SANDBOX_LOCALE'),
                    "styleProfile" => env('NOON_PAYMENTS_SANDBOX_STYLE_PROFILE'),
                ],
            ]
        ],
    ],

    'newsletter' => [
        'sms' => [
            'mode' => env('NEWSLETTER_SMS_MODE'),
            'data' => [
                'live' => [
                    'url' => env('SMS_URL'),
                    'username' => 'DIETMARKET',
                    'password' => 'J2Q3JadeteeU74sA',
                    'sanderName' => 'DIETMARKET',
                ],
                'sandbox' => [
                    'url' => env('SMS_URL'),
                    'username' => 'DIETMARKET',
                    'password' => 'J2Q3JadeteeU74sA',
                    'sanderName' => 'DIETMARKET',
                ],
            ]
        ],

        'firebase' => [
            'mode' => env('NEWSLETTER_FIREBASE_MODE'),
            'data' => [
                'live' => [
                    'url' => env('NEWSLETTER_FIREBASE_LIVE_URL'),
                    'serverKey' => env('NEWSLETTER_FIREBASE_LIVE_SERVER_KEY'),
                ],
                'sandbox' => [
                    'url' => env('NEWSLETTER_FIREBASE_LIVE_URL'),
                    'serverKey' => env('NEWSLETTER_FIREBASE_LIVE_SERVER_KEY'),
                ],
            ],
        ],

        'email' => [
            'mode' => env('NEWSLETTER_EMAIL_MODE'),
            'data' => [
                'live' => [
                    'transport' => 'smtp',
                    'host' => env('MAIL_HOST', 'smtp.mailgun.org'),
                    'port' => env('MAIL_PORT', 587),
                    'encryption' => env('MAIL_ENCRYPTION', 'tls'),
                    'username' => env('MAIL_USERNAME'),
                    'password' => env('MAIL_PASSWORD'),
                    'timeout' => null,
                    'auth_mode' => null,
                ],
                'sandbox' => [
                    'transport' => 'smtp',
                    'host' => env('MAIL_HOST', 'smtp.mailgun.org'),
                    'port' => env('MAIL_PORT', 587),
                    'encryption' => env('MAIL_ENCRYPTION', 'tls'),
                    'username' => env('MAIL_USERNAME'),
                    'password' => env('MAIL_PASSWORD'),
                    'timeout' => null,
                    'auth_mode' => null,
                ],
            ],
        ]
    ],

    'oto' => [
        'realUrl' => 'https://api.tryoto.com/rest/v2/',
        'refreshToken' => 'AOEOulYKHbQNQhV-hs8lNqM_BXJsFxk3JAg8NVL8utuO6dr8_GiwskgVpM73vuJsN1saolBoS4UszXcn1Qtd5sujBncdaP6INH1RO5kZIQ_9z9yplfr8vg8MDXq6_Ph24YUPWf4Sia75jl8As_KhWMRRjqY_VYn3VMJVrEJdzD82XKAP0vXKaEO6TVRh-fsDbQxCcAX6U42OFDKT-kuO8VjJ9l8Lplz1-g',

    ],

];
