<?php

namespace App\Modules\DeliveryMen\Resources;

use App\Modules\Orders\Resources\OrderDelivery;
use App\Modules\Nationality\Resources\Nationality;
use App\Modules\VehicleType\Resources\VehicleType;
use HZ\Illuminate\Mongez\Managers\Resources\JsonResourceManager;

class DeliveryMan extends JsonResourceManager
{
    /**
     * Data that must be returned
     *
     * @const array
     */
    const DATA = ['id', 'location', 'profileLocation', 'newProfileLocation', 'approved', 'dataState'];

    /**
     * String Data
     *
     * @const array
     */
    const STRING_DATA = ['firstName', 'lastName',  'email',  'birthDate', 'vehicleBrand', 'vehicleModel',  'accountCardName', 'accessToken', 'phoneNumber', 'name', 'bankAccountNumber', 'newAccountCardName', 'newBankAccountNumber', 'newVehicleBrand', 'newVehicleModel', 'newYearManufacture', 'newVehicleSerialNumber', 'newFirstName', 'newLastName', 'newEmail', 'newBirthDate', 'newPhoneNumberUpdate', 'VehicleSerialNumber', 'newIdNumber', 'idNumber'];

    /**
     * Boolean Data
     *
     * @const array
     */
    const BOOLEAN_DATA = ['published',  'isVerified', 'status', 'NewPublished', 'updateData'];

    /**
     * Data that should be returned if exists
     *
     * @const array
     */
    const WHEN_AVAILABLE = ['firstName', 'lastName', 'phoneNumber', 'codePhoneNumber', 'email', 'idNumber', 'bankAccountNumber', 'birthDate', 'vehicleBrand', 'vehicleModel', 'yearManufacture', 'VehicleSerialNumber', 'accountCardName', 'image', 'cardIdImage', 'driveryLicenseImage', 'VehicleFrontImage', 'VehicleBackImage', 'published', 'approved', 'location', 'nationality', 'vehicleType', 'newPhoneNumber', 'newVerificationCode', 'status', 'name', 'NewPublished', 'updateData', 'newAccountCardName', 'newBankAccountNumber', 'newVehicleType', 'newVehicleBrand', 'newVehicleModel', 'newYearManufacture', 'newVehicleSerialNumber', 'newCardIdImage', 'newDriveryLicenseImage', 'newVehicleFrontImage', 'newVehicleBackImage', 'newNationality', 'newFirstName', 'newLastName', 'newEmail', 'newBirthDate', 'newIdNumber', 'newImage', 'newPhoneNumberUpdate', 'profileLocation', 'newProfileLocation'];

    /**
     * Set columns list of INTEGER values.
     *
     * @const array
     */
    const INTEGER_DATA = [
        'verificationCode', 'totalNotifications',  'codePhoneNumber',  'yearManufacture', 'countResendCode', 'newPhoneNumber', 'newVerificationCode', 'requested',
    ];

    /**
     * Set columns list of float values.
     *
     * @cont array
     */
    const FLOAT_DATA = ['walletBalance', 'walletBalanceDeposit', 'walletBalanceWithdraw'];

    /**
     * Set that columns that will be formatted as dates
     * it could be numeric array or associated array to set the date format for certain columns
     *
     * @const array
     */
    const DATES = ['createdAt', 'loginUpdateDateAt', 'logoutUpdateDateAt'];

    /**
     * Data that has multiple values based on locale codes
     * Mostly this is used with mongodb driver
     *
     * @const array
     */
    const LOCALIZED = [];

    /**
     * List of assets that will have a full url if available
     */
    const ASSETS = ['image', 'cardIdImage', 'driveryLicenseImage', 'VehicleFrontImage', 'VehicleBackImage', 'newCardIdImage', 'newDriveryLicenseImage', 'newVehicleFrontImage', 'newVehicleBackImage'];

    /**
     * Object Data
     *
     * @const array
     */
    const OBJECT_DATA = [];

    /**
     * Data that will be returned as a resources
     *
     * i.e [city => CityResource::class],
     * @const array
     */
    const RESOURCES = [
        'nationality' => Nationality::class,
        'newNationality' => Nationality::class,
        'vehicleType' => VehicleType::class,
        'newVehicleType' => VehicleType::class,
    ];

    /**
     * Data that will be returned as a collection of resources
     *
     * i.e [cities => CityResource::class],
     * @const array
     */
    const COLLECTABLE = [
        // 'orders' => OrderDelivery::class,
    ];

    /**
     * List of keys that will be unset before sending
     *
     * @var array
     */
    protected static $disabledKeys = [];

    /**
     * List of keys that will be taken only
     *
     * @var array
     */
    protected static $allowedKeys = [];

    /**
     * {@inheritdoc}
     */
    protected function extend($request)
    {
        if ($request->restaurant) {
            $restaurant = repo('restaurants')->get((int) $request->restaurant);

            $restaurantLocation = [
                $restaurant['location']['coordinates'][0],
                $restaurant['location']['coordinates'][1],
            ];

            $deliveryMen = $this->location ?? $restaurant['location'];
            $locationDeliveryMen = [
                $deliveryMen['coordinates'][0],
                $deliveryMen['coordinates'][1],
            ];
            // dd($locationDeliveryMen);
            $distance = $this->distance($restaurantLocation[0], $restaurantLocation[1], $locationDeliveryMen[0], $locationDeliveryMen[1]);
            $this->set('distance', (int) $distance . ' Km');
        }
    }

   
    /**
     * It calculates the distance between two points.
     * 
     * @param lat1 Latitude of point 1 (in decimal degrees)
     * @param lon1 longitude of the first point
     * @param lat2 The latitude of the second point.
     * @param lon2 The longitude of the second point.
     * @param unit The unit you desire for results
     * 
     * @return The distance between two points.
     */
    public function distance($lat1, $lon1, $lat2, $lon2, $unit = 'k')
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } elseif ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }
}
