<?php

use App\Modules\General\Events\ModifyResponse;
use App\Modules\Users\Events\WithUser;
use App\Modules\Users\Events\WithVisitorCart;

return [
    /*
    |--------------------------------------------------------------------------
    | Database options
    |--------------------------------------------------------------------------
    |
    | `prefix` value will be added to every model query,
    | however, if the model has a `TABLE_PREFIX` constant with a value rather than NULL
    | it will be used instead
    |
    | `updatesLogModel` if set a model class, any updates that occurs to every model will be stored
    | in the given model to be logged later.
    |
    | Please Note this will massively increase the updates log model size as every update is stored before the update happens.
    | Please read the documentation for the column names
    */
    // 'database' => [
    //     'mysql' => [
    //         'defaultStringLength' => 191,
    //     ],
    //     'prefix' => '',
    //     'updatesLogModel' => HZ\Illuminate\Mongez\Models\UpdateLog::class,
    // ],

    /*
    |--------------------------------------------------------------------------
    | Resources options
    |--------------------------------------------------------------------------
    |
    | These are the `resource` options that can be used with any `Resource` class
    | The `assets` option defines the generating `url` for any asset, by default is `url()`
    |
    | The date key provides the date options that can be used for any date column
    | `format`: the date format that will be returned.
    | `timestamp`: if set to true, the unix timestamp will be returned as integer.
    | `human`: if set to true, a human time will be returned i.e 12 minutes ago.
    |  Please note that if the timestamp and human time are set to true, the
    |  date format will be returned as string, otherwise it will be returned as array`object`.
    |
    */
    'resources' => [
        'assets' => 'url',
        'date' => [
            'format' => 'd-m-Y h:i:s a',
            'timestamp' => true,
            'humanTime' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | General Configurations
    |--------------------------------------------------------------------------
    |
    | The serialize_precision option if set to -1 will encode the float numbers properly
    |
    */
    'serialize_precision' => -1,

    /*
    |--------------------------------------------------------------------------
    | Localization Mode
    |--------------------------------------------------------------------------
    |
    | This will determine the type of handing data that has multiple values based on locale code
    | Mainly it will be used with resources when returning the data
    |
    | Available options: array|object
    */
    'localizationMode' => 'array',

    /*
    |--------------------------------------------------------------------------
    | Module builder
    |--------------------------------------------------------------------------
    |
    | Based on the settings that is provided here, the module builder will adjust its settings accordingly.
    | Put your configurations based on your application flow
    |
    | has-admin: if set to false, then Laravel Mongez will treat the application as a single application with no admin panel
    |
    | build: this will determine if the module will be created
    | to be served with the admin api controller + api controller or
    | to be served with the admin view controller + view controller
    | available values: view|api, defaults to api
    |
    */
    'module-builder' => [
        'has-admin' => true,
        'build' => 'api',
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin options
    |--------------------------------------------------------------------------
    |
    | The following options are applied on any request related to the AdminApiController or the /admin requests in general
    |
    | returnOn options: single-record | all-records | none
    |
    */
    'admin' => [
        'returnOn' => [
            'store' => 'single-record',
            'update' => 'single-record',
        ],
    ],


    /*
    |--------------------------------------------------------------------------
    | Repository Options
    |--------------------------------------------------------------------------
    |
    | List of repository options located here
    |
    |--------------------------------------------------------------------------
    | Pagination configurations
    |--------------------------------------------------------------------------
    | Uploads Directory
    |
    | Setting the uploads directory will be useful when dealing with git repositories to be ignored.
    | If sets to null, then it won't be used
    |
    | This directory will be created inside local directory path in the `config/filesystem.php`
    |
    | keepUploadsName
    |
    | If set to true, then all uploads names wil be kept as it is.
    | If set to false, a random generated hashed name will be used instead.
    |--------------------------------------------------------------------------
    | Pagination configurations
    |--------------------------------------------------------------------------
    | Pagination configurations work with `list` method in any repository.
    |
    | Any value listed below will be applied on all repositories unless repository/method-call override.
    |
    */
    'repository' => [
        'uploads' => [
            'uploadsDirectory' => 'data',
            'keepUploadsName' => true,
        ],
        'pagination' => [
            'paginate' => true,
            'itemsPerPage' => 15,
        ],
    ],


    /*
      |--------------------------------------------------------------------------
      | Response Options
      |--------------------------------------------------------------------------
      | badRequest Response Map strategy
      |
      | If the response map strategy is set as array, then it will be returned as array of objects
      | each object looks like [key => input, value => message]
      | However, key and value can be customized as well.
      |
      | Available Options: `array` | `object`, defaults to `array`
      |
      | The arrayKey will set the name of object key that will hold the input name, defaults to `key`
      | The arrayValue will set the name of object key that will hold the error message itself, defaults to `value`
      |
      */
    'response' => [
        'errors' => [
            'strategy' => 'array',
            'arrayKey' => 'key',
            'arrayValue' => 'value',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Application Repositories
    |--------------------------------------------------------------------------
    |
    | The repositories section will be mainly used for records retrieval... fetching records from database
    | It will also be responsible for inserting/updating and deleting from database
    |
    */
    'repositories' => [
        // add your repositories here
        // 'repo-short-name' => RepositoryClassPath::class,
        'users' => App\Modules\Users\Repositories\UsersRepository::class,
        'usersGroups' => App\Modules\Users\Repositories\UsersGroupsRepository::class,
        'permissions' => App\Modules\Users\Repositories\PermissionsRepository::class,
        'tests' => App\Modules\Test\Repositories\TestsRepository::class,
        'settings' => App\Modules\Settings\Repositories\SettingsRepository::class,
        'sellers' => App\Modules\Customers\Repositories\SellersRepository::class,
        'customers' => App\Modules\Customers\Repositories\CustomersRepository::class,
        'brands' => App\Modules\Brands\Repositories\BrandsRepository::class,
        'categories' => App\Modules\Categories\Repositories\CategoriesRepository::class,
        'coupons' => App\Modules\Coupons\Repositories\CouponsRepository::class,
        'addressBooks' => App\Modules\AddressBook\Repositories\AddressBooksRepository::class,
        'orders' => App\Modules\Orders\Repositories\OrdersRepository::class,
        'orderItems' => App\Modules\Orders\Repositories\OrderItemsRepository::class,
        'orderStatus' => App\Modules\Orders\Repositories\OrderStatusRepository::class,
        'orderDelivery' => App\Modules\Orders\Repositories\OrderDeliveryRepository::class,
        'deliveryReasonsRejecteds' => App\Modules\Orders\Repositories\DeliveryReasonsRejectedRepository::class,
        'deliveryReasonsNotCompletedOrders' => App\Modules\Orders\Repositories\DeliveryReasonsNotCompletedOrderRepository::class,
        'orderStatusDelivery' => App\Modules\Orders\Repositories\OrderStatusDeliveryRepository::class,
        'contactUs' => App\Modules\ContactUs\Repositories\ContactUsRepository::class,
        'countries' => App\Modules\Countries\Repositories\CountriesRepository::class,
        'cities' => App\Modules\Cities\Repositories\CitiesRepository::class,
        'regions' => App\Modules\Regions\Repositories\RegionsRepository::class,
        'cart' => App\Modules\Cart\Repositories\CartRepository::class,
        'pages' => App\Modules\Pages\Repositories\PagesRepository::class,
        'generals' => App\Modules\General\Repositories\GeneralsRepository::class,
        'products' => App\Modules\Products\Repositories\ProductsRepository::class,
        'productMeals' => App\Modules\Products\Repositories\ProductMealsRepository::class,
        'complaints' => App\Modules\Complaints\Repositories\ComplaintsRepository::class,
        'wallets' => App\Modules\Wallet\Repositories\WalletsRepository::class,
        'walletDelivery' => App\Modules\Wallet\Repositories\WalletDeliveryRepository::class,
        'walletProvider' => App\Modules\Wallet\Repositories\WalletProviderRepository::class,
        'banks' => App\Modules\Banks\Repositories\BanksRepository::class,
        'bankTransfers' => App\Modules\Banks\Repositories\BankTransfersRepository::class,
        'notifications' => App\Modules\Notifications\Repositories\NotificationsRepository::class,
        'banners' => App\Modules\Banners\Repositories\BannersRepository::class,
        'sliders' => App\Modules\Sliders\Repositories\SlidersRepository::class,
        'cancelingReasons' => App\Modules\Orders\Repositories\CancelingReasonsRepository::class,
        'rewards' => App\Modules\Rewards\Repositories\RewardsRepository::class,
        'productCollections' => App\Modules\Products\Repositories\ProductCollectionsRepository::class,
        'shippingMethods' => App\Modules\ShippingMethods\Repositories\ShippingMethodsRepository::class,
        'reviews' => App\Modules\Orders\Repositories\ReviewsRepository::class,
        'transactions' => App\Modules\Transactions\Repositories\TransactionsRepository::class,
        'deliveryMen' => App\Modules\DeliveryMen\Repositories\DeliveryMensRepository::class,
        'options' => App\Modules\Options\Repositories\OptionsRepository::class,
        'productReviews' => App\Modules\Products\Repositories\ProductReviewsRepository::class,
        'restaurantsReviews' => App\Modules\Restaurants\Repositories\RestaurantsReviewsRepository::class,
        'clubReviews' => App\Modules\Clubs\Repositories\ClubReviewsRepository::class,
        'nutritionSpecialistReviews' => App\Modules\NutritionSpecialist\Repositories\NutritionSpecialistReviewsRepository::class,
        'newsletters' => App\Modules\Newsletters\Repositories\NewslettersRepository::class,
        'subscriptions' => App\Modules\Newsletters\Repositories\SubscriptionsRepository::class,
        'campaigns' => App\Modules\Campaigns\Repositories\CampaignsRepository::class,
        'campaignDeliveries' => App\Modules\Campaigns\Repositories\CampaignDeliveriesRepository::class,
        'units' => App\Modules\Units\Repositories\UnitsRepository::class,
        'customerGroups' => App\Modules\CustomerGroups\Repositories\CustomerGroupsRepository::class,
        'returningReasons' => App\Modules\Orders\Repositories\ReturningReasonsRepository::class,
        'packagingStatus' => App\Modules\Orders\Repositories\PackagingStatusRepository::class,
        'favorites' => App\Modules\Favorites\Repositories\FavoritesRepository::class,
        'modules' => App\Modules\Modules\Repositories\ModulesRepository::class,
        'taxes' => App\Modules\Taxes\Repositories\TaxesRepository::class,
        'commissions' => App\Modules\Commissions\Repositories\CommissionsRepository::class,
        'homeModules' => App\Modules\HomeModules\Repositories\HomeModulesRepository::class,
        'auctions' => App\Modules\Auctions\Repositories\AuctionsRepository::class,
        'compromises' => App\Modules\Compromises\Repositories\CompromisesRepository::class,
        'shippingCosts' => App\Modules\ShippingCosts\Repositories\ShippingCostsRepository::class,
        'logs' => App\Modules\Logs\Repositories\LogsRepository::class,
        'dietTypes' => App\Modules\DietTypes\Repositories\DietTypesRepository::class,
        'storeManagers' => App\Modules\StoreManagers\Repositories\StoreManagersRepository::class,
        'clubs' => App\Modules\Clubs\Repositories\ClubsRepository::class,
        'restaurants' => App\Modules\Restaurants\Repositories\RestaurantsRepository::class,
        'deliveryRestaurant' => App\Modules\Restaurants\Repositories\DeliveryRestaurantRepository::class,
        'reasonRestaurant' => App\Modules\Restaurants\Repositories\ReasonRestaurantRepository::class,
        // 'clubs' => App\Modules\Clubs\Repositories\ClubsRepository::class,
        'restaurantManagers' => App\Modules\RestaurantManager\Repositories\RestaurantManagersRepository::class,
        'clubManagers' => App\Modules\ClubManagers\Repositories\ClubManagersRepository::class,
        'sections' => App\Modules\Sections\Repositories\SectionsRepository::class,
        'sizes' => App\Modules\Sizes\Repositories\SizesRepository::class,
        'items' => App\Modules\Items\Repositories\ItemsRepository::class,
        'meals' => App\Modules\Meals\Repositories\MealsRepository::class,
        'serviceProviders' => App\Modules\ServiceProvider\Repositories\ServiceProvidersRepository::class,
        //  'reviews' => App\Modules\Reviews\Repositories\ReviewsRepository::class,
        'stores' => App\Modules\Stores\Repositories\StoresRepository::class,
        'clubsSubscriptions' => App\Modules\ClubsSubscriptions\Repositories\ClubsSubscriptionsRepository::class,
        'receiptRequests' => App\Modules\ReceiptRequests\Repositories\ReceiptRequestsRepository::class,
        'subscriptionMeals' => App\Modules\SubscriptionMeals\Repositories\SubscriptionMealsRepository::class,
        'clubs' => App\Modules\Clubs\Repositories\ClubsRepository::class,
        'typeContactuses' => App\Modules\TypeContactUs\Repositories\TypeContactusesRepository::class,
        'healthyDatas' => App\Modules\HealthyData\Repositories\HealthyDatasRepository::class,
        'nutritionSpecialists' => App\Modules\NutritionSpecialist\Repositories\NutritionSpecialistsRepository::class,
        'NutritionSpecialistsNotes' => App\Modules\NutritionSpecialist\Repositories\NutritionSpecialistsNotesRepository::class,
        'nutritionSpecialistsCustomerNotes' => App\Modules\NutritionSpecialist\Repositories\NutritionSpecialistNotesCustomerRepository::class,
        'guests' => App\Modules\Guest\Repositories\GuestsRepository::class,
        'typeOfFoodRestaurants' => App\Modules\TypeOfFoodRestaurant\Repositories\TypeOfFoodRestaurantsRepository::class,
        'branchesClubs' => App\Modules\BranchesClubs\Repositories\BranchesClubsRepository::class,
        'packagesClubs' => App\Modules\PackagesClubs\Repositories\PackagesClubsRepository::class,
        'clubBookings' => App\Modules\ClubBookings\Repositories\ClubBookingsRepository::class,
        'nutritionSpecialistMangers' => App\Modules\NutritionSpecialistMangers\Repositories\NutritionSpecialistMangersRepository::class,
        'nationalities' => App\Modules\Nationality\Repositories\NationalitiesRepository::class,
        'vehicleTypes' => App\Modules\VehicleType\Repositories\VehicleTypesRepository::class,
        'deliveryTransactions' => App\Modules\DeliveryTransactions\Repositories\DeliveryTransactionsRepository::class,
        'productPackageSizes' => App\Modules\Products\Repositories\ProductPackageSizesRepository::class,
        'skus' => App\Modules\Sku\Repositories\SkusRepository::class,
        // Auto generated repositories here: DO NOT remove this line.
    ],

    /*
    |--------------------------------------------------------------------------
    | Macroable classes
    |--------------------------------------------------------------------------
    |
    | Here you can set your macros classes that will be used to be
    | The key will be the original class name that will be extends
    | The value will be the macro class that will be used to extend the original class
    |
    */
    'macros' => [
        Illuminate\Support\Str::class => HZ\Illuminate\Mongez\Macros\Support\Str::class,
        Illuminate\Support\Arr::class => HZ\Illuminate\Mongez\Macros\Support\Arr::class,
        Illuminate\Http\Request::class => HZ\Illuminate\Mongez\Macros\Http\Request::class,
        Illuminate\Support\Collection::class => HZ\Illuminate\Mongez\Macros\Support\Collection::class,
        Illuminate\Filesystem\Filesystem::class => HZ\Illuminate\Mongez\Macros\FileSystem\FileSystem::class,
        Illuminate\Database\Query\Builder::class => HZ\Illuminate\Mongez\Macros\Database\Query\Builder::class,
        Illuminate\Database\Schema\Blueprint::class => HZ\Illuminate\Mongez\Macros\Database\Schema\Blueprint::class,
        Illuminate\Console\Command::class => HZ\Illuminate\Mongez\Macros\Console\Command::class,
    ],
    /*
    |--------------------------------------------------------------------------
    | Events list
    |--------------------------------------------------------------------------
    |
    | Set list of events listeners that will be triggered later from its sources
    |
    */
    'events' => [
        'response.send' => [
            [WithUser::class, 'sendUser'],
            [WithVisitorCart::class, 'sendCart'],
            [ModifyResponse::class, 'modifyResponse']
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache driver
    |--------------------------------------------------------------------------
    |
    | Set your cache driver one of available drivers in laravel
    |
    */
    'cache' => [],

    /*
    |--------------------------------------------------------------------------
    | Base filters
    |--------------------------------------------------------------------------
    |
    */
    'filters' => [
        HZ\Illuminate\Mongez\Helpers\Filters\MYSQL\Filter::class,
        HZ\Illuminate\Mongez\Helpers\Filters\MongoDB\Filter::class,
    ]
];
