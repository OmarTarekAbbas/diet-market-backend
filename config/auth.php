<?php

use App\Modules\Users\Models\User;
use App\Modules\Customers\Models\Customer;
use App\Modules\ClubManagers\Models\ClubManager;
use App\Modules\DeliveryMen\Models\DeliveryMan;
use App\Modules\NutritionSpecialistMangers\Models\NutritionSpecialistManger;
use App\Modules\RestaurantManager\Models\RestaurantManager;
use App\Modules\StoreManagers\Models\StoreManager;

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | here which uses session storage and the Eloquent user provider.
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | Supported: "session", "token"
    |
    */

    'guards' => [
        // .. at the bottom of the guards key
        // add the admin guard info
        'admin' => [
            'driver' => 'session',
            'provider' => 'users',
            'repository' => 'users',
            'type' => 'user',
            'prefix' => '/admin',
            'ignoredRoutes' => ['/login'],
        ],
        'clubManagers' => [
            'driver' => 'session',
            'provider' => 'clubManagers',
            'repository' => 'clubManagers',
            'type' => 'clubManagers',
            'prefix' => '/clubManagers',
            'ignoredRoutes' => ['/login'],
        ],
        'deliveryMen' => [
            'driver' => 'session',
            'provider' => 'deliveryMen',
            'repository' => 'deliveryMen',
            'type' => 'deliveryMan',
            'prefix' => '/deliveryMen',
            'ignoredRoutes' => ['/login'],
        ],
        // it maybe customers in the front site instead of users
        'site' => [
            'driver' => 'session',
            'provider' => 'customers',
            'repository' => 'customers',
            'type' => 'customer',
        ],

        'restaurantManager' => [
            'driver' => 'session',
            'provider' => 'restaurantManager',
            'repository' => 'restaurantManagers',
            'type' => 'restaurantManager',
            'prefix' => '/restaurantManagers',
        ],

        'storeManager' => [
            'driver' => 'session',
            'provider' => 'storeManager',
            'repository' => 'storeManagers',
            'type' => 'storeManager',
            'prefix' => '/storeManagers',
        ],

        'nutritionSpecialistMangers' => [
            'driver' => 'session',
            'provider' => 'nutritionSpecialistMangers',
            'repository' => 'nutritionSpecialistMangers',
            'type' => 'nutritionSpecialistMangers',
            'prefix' => '/nutritionSpecialistMangers',
        ],

        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'api' => [
            'driver' => 'token',
            'provider' => 'users',
            'hash' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | If you have multiple user tables or models you may configure multiple
    | sources which represent each model / table. These sources may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => User::class,
        ],
        'deliveryMen' => [
            'driver' => 'eloquent',
            'model' => DeliveryMan::class,
        ],
        'customers' => [
            'driver' => 'eloquent',
            'model' => Customer::class,
        ],
        'clubManagers' => [
            'driver' => 'eloquent',
            'model' => ClubManager::class,
        ],
        'restaurantManager' => [
            'driver' => 'eloquent',
            'model' => RestaurantManager::class,
        ],
        'storeManager' => [
            'driver' => 'eloquent',
            'model' => StoreManager::class,
        ],
        'nutritionSpecialistMangers' => [
            'driver' => 'eloquent',
            'model' => NutritionSpecialistManger::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | You may specify multiple password reset configurations if you have more
    | than one user table or model in the application and you want to have
    | separate password reset settings based on the specific user types.
    |
    | The expire time is the number of minutes that the reset token should be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Here you may define the amount of seconds before a password confirmation
    | times out and the user is prompted to re-enter their password via the
    | confirmation screen. By default, the timeout lasts for three hours.
    |
    */

    'password_timeout' => 10800,

];
