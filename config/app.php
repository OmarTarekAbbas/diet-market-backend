<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),
    'api-key' => env('API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL', null), 

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'Asia/Riyadh',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => env('APP_LOCALE_CODE', 'ar'),

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [
        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,
        Maatwebsite\Excel\ExcelServiceProvider::class,

        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

        /**
         * Modules Service Providers...
         */
        App\Modules\Users\Providers\UserServiceProvider::class,
        App\Modules\Test\Providers\TestServiceProvider::class,
        App\Modules\Settings\Providers\SettingServiceProvider::class,
        App\Modules\Customers\Providers\CustomerServiceProvider::class,
        App\Modules\CustomerGroups\Providers\CustomerGroupServiceProvider::class,
        App\Modules\Categories\Providers\CategoryServiceProvider::class,
        App\Modules\Brands\Providers\BrandServiceProvider::class,
        App\Modules\Coupons\Providers\CouponServiceProvider::class,
        App\Modules\AddressBook\Providers\AddressBookServiceProvider::class,
        App\Modules\Orders\Providers\OrderServiceProvider::class,
        App\Modules\ContactUs\Providers\ContactUServiceProvider::class,
        App\Modules\Countries\Providers\CountryServiceProvider::class,
        App\Modules\Cities\Providers\CityServiceProvider::class,
        App\Modules\Regions\Providers\RegionServiceProvider::class,
        App\Modules\Cart\Providers\CartServiceProvider::class,
        App\Modules\Pages\Providers\PageServiceProvider::class,
        App\Modules\General\Providers\GeneralServiceProvider::class,
        App\Modules\Products\Providers\ProductServiceProvider::class,
        App\Modules\Complaints\Providers\ComplaintServiceProvider::class,
        App\Modules\Wallet\Providers\WalletServiceProvider::class,
        App\Modules\Banks\Providers\BankServiceProvider::class,
        App\Modules\Notifications\Providers\NotificationServiceProvider::class,
        App\Modules\Banners\Providers\BannerServiceProvider::class,
        App\Modules\Sliders\Providers\SliderServiceProvider::class,
        App\Modules\Rewards\Providers\RewardServiceProvider::class,
        App\Modules\ShippingMethods\Providers\ShippingMethodServiceProvider::class,
        App\Modules\Transactions\Providers\TransactionServiceProvider::class,
        App\Modules\DeliveryMen\Providers\DeliveryManServiceProvider::class,
        App\Modules\Options\Providers\OptionServiceProvider::class,
        App\Modules\Newsletters\Providers\NewsletterServiceProvider::class,
        App\Modules\Campaigns\Providers\CampaignServiceProvider::class,
        App\Modules\Reports\Providers\ReportServiceProvider::class,
        App\Modules\Units\Providers\UnitServiceProvider::class,
        App\Modules\CustomerGroups\Providers\CustomerGroupServiceProvider::class,
        App\Modules\Favorites\Providers\FavoriteServiceProvider::class,
        App\Modules\Modules\Providers\ModuleServiceProvider::class,
        App\Modules\Taxes\Providers\TaxServiceProvider::class,
        App\Modules\Commissions\Providers\CommissionServiceProvider::class,
        App\Modules\Auctions\Providers\AuctionServiceProvider::class,
        App\Modules\Compromises\Providers\CompromiseServiceProvider::class,
        App\Modules\ShippingCosts\Providers\ShippingCostServiceProvider::class,
        App\Modules\Logs\Providers\LogServiceProvider::class,
        App\Modules\DietTypes\Providers\DietTypeServiceProvider::class,
        App\Modules\StoreManagers\Providers\StoreManagerServiceProvider::class,
        // App\Modules\Club\Providers\ClubServiceProvider::class,
        App\Modules\Restaurants\Providers\RestaurantServiceProvider::class,
        App\Modules\Clubs\Providers\ClubServiceProvider::class,
        App\Modules\RestaurantManager\Providers\RestaurantManagerServiceProvider::class,
        App\Modules\ClubManagers\Providers\ClubManagerServiceProvider::class,
        App\Modules\Sections\Providers\SectionServiceProvider::class,
        App\Modules\Sizes\Providers\SizeServiceProvider::class,
        App\Modules\Items\Providers\ItemServiceProvider::class,
        App\Modules\Meals\Providers\MealServiceProvider::class,
        App\Modules\ServiceProvider\Providers\ServiceProviderServiceProvider::class,
        // App\Modules\Reviews\Providers\ReviewServiceProvider::class,
        App\Modules\Stores\Providers\StoreServiceProvider::class,
        App\Modules\ClubsSubscriptions\Providers\ClubsSubscriptionServiceProvider::class,
        App\Modules\ReceiptRequests\Providers\ReceiptRequestServiceProvider::class,
        App\Modules\SubscriptionMeals\Providers\SubscriptionMealServiceProvider::class,
        // App\Modules\Clubs\Providers\ClubServiceProvider::class,
        App\Modules\TypeContactUs\Providers\TypeContactUServiceProvider::class,
        App\Modules\HealthyData\Providers\HealthyDatumServiceProvider::class,
        App\Modules\NutritionSpecialist\Providers\NutritionSpecialistServiceProvider::class,
        App\Modules\Guest\Providers\GuestServiceProvider::class,
        App\Modules\TypeOfFoodRestaurant\Providers\TypeOfFoodRestaurantServiceProvider::class,
        App\Modules\BranchesClubs\Providers\BranchesClubServiceProvider::class,
        App\Modules\PackagesClubs\Providers\PackagesClubServiceProvider::class,
        App\Modules\ClubBookings\Providers\ClubBookingServiceProvider::class,
        App\Modules\NutritionSpecialistMangers\Providers\NutritionSpecialistMangerServiceProvider::class,
        App\Modules\Nationality\Providers\NationalityServiceProvider::class,
 		App\Modules\VehicleType\Providers\VehicleTypeServiceProvider::class,
 		App\Modules\DeliveryTransactions\Providers\DeliveryTransactionServiceProvider::class,
 		App\Modules\Sku\Providers\SkuServiceProvider::class,
 		// Auto generated providers here: DO NOT remove this line.
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [

        'App' => Illuminate\Support\Facades\App::class,
        'Arr' => Illuminate\Support\Arr::class,
        'Artisan' => Illuminate\Support\Facades\Artisan::class,
        'Auth' => Illuminate\Support\Facades\Auth::class,
        'Blade' => Illuminate\Support\Facades\Blade::class,
        'Broadcast' => Illuminate\Support\Facades\Broadcast::class,
        'Bus' => Illuminate\Support\Facades\Bus::class,
        'Cache' => Illuminate\Support\Facades\Cache::class,
        'Config' => Illuminate\Support\Facades\Config::class,
        'Cookie' => Illuminate\Support\Facades\Cookie::class,
        'Crypt' => Illuminate\Support\Facades\Crypt::class,
        'DB' => Illuminate\Support\Facades\DB::class,
        'Eloquent' => Illuminate\Database\Eloquent\Model::class,
        'Event' => Illuminate\Support\Facades\Event::class,
        'File' => Illuminate\Support\Facades\File::class,
        'Gate' => Illuminate\Support\Facades\Gate::class,
        'Hash' => Illuminate\Support\Facades\Hash::class,
        'Http' => Illuminate\Support\Facades\Http::class,
        'Lang' => Illuminate\Support\Facades\Lang::class,
        'Log' => Illuminate\Support\Facades\Log::class,
        'Mail' => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password' => Illuminate\Support\Facades\Password::class,
        'Queue' => Illuminate\Support\Facades\Queue::class,
        'Redirect' => Illuminate\Support\Facades\Redirect::class,
        // 'Redis' => Illuminate\Support\Facades\Redis::class,
        'Request' => Illuminate\Support\Facades\Request::class,
        'Response' => Illuminate\Support\Facades\Response::class,
        'Route' => Illuminate\Support\Facades\Route::class,
        'Schema' => Illuminate\Support\Facades\Schema::class,
        'Session' => Illuminate\Support\Facades\Session::class,
        'Storage' => Illuminate\Support\Facades\Storage::class,
        'Str' => Illuminate\Support\Str::class,
        'URL' => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View' => Illuminate\Support\Facades\View::class,
        'Excel' => Maatwebsite\Excel\Facades\Excel::class,
    ],

];
