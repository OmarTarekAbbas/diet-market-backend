# Cart API DOCS
The shopping cart is about adding products or meals to it, and the customer adds addresses and shipping methods to the products
It is related to meal products

# Base URL
http://localhost/

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

LOCALE-CODE : {lang}


# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
|/api/carts        |GET           |-| [Response](#Response)|
|/api/carts/{id}        |put           |-|[Response](#Response)|
|/api/carts/{id}        |delete           |-|[Response](#Response)|
|/api/carts        |delete           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Cart 

```json
{
"item":"int"
"quantity":"int"
"type":"string" // products or food
"options[0][id]":"int"
"options[0][values][0]":"int"
"options[0][values][1]":"int"
} 




```

# <a name="Update"> </a> Update Cart
# api/carts get
```json
{
"shippingMethod":"int"
"couponCode":"string" //VokhIReC 
"deliveryType":"string" //inHome
"state":"string"
"address":"int"
"type":"string" // products or food
} 
```
# <a name="Response">
 "success": true,
        "cart": {
            "group": {
                "id": 22,
                "name": [
                    {
                        "localeCode": "ar",
                        "text": "Test"
                    },
                    {
                        "localeCode": "en",
                        "text": "تجربة"
                    }
                ],
                "conditionType": "totalPurchaseAmount",
                "conditionValue": null,
                "specialDiscount": 3,
                "freeShipping": false,
                "freeExpressShipping": false,
                "nameGroup": [
                    {
                        "name": "productsInStores"
                    },
                    {
                        "name": "typesInRestaurant"
                    },
                    {
                        "name": "reserveSpecialist"
                    },
                    {
                        "name": "clubSubscription"
                    }
                ],
                "published": true
            },
            "deviceId": "7A048D5F-5553-4DA6-AC5D-C9077D0850DE",
            "customer": {
                "id": 7,
                "firstName": "omar",
                "lastName": "tarek",
                "email": "omart8703@gmail.com",
                "phoneNumber": "966551234567"
            },
            "type": "products",
            "totalPrice": 0,
            "taxes": 0,
            "finalPrice": 0,
            "originalPrice": 0,
            "isActiveRewardPoints": false,
            "subscription": false,
            "rewardPoints": 0,
            "totalQuantity": 0,
            "purchaseRewardPoints": 0,
            "taxesValue": 15,
            "totalSubscription": 0,
            "totalItems": 0,
            "items": [],
            "canUseRewardPoints": false,
            "totals": [
                {
                    "text": "إجمالي الفاتورة",
                    "price": 0,
                    "priceText": "0 ر.س",
                    "type": "finalPriceText"
                }
            ]
        },
        "cartChanged": false,
        "seller": [],
        "customer": {
            "accessToken": "YKyqMH5UQnqniHFCGmwQ3h99IrTu3RrzcCTxn1otYw0lOt3GwuMJP7d7tsyfq1DkgHk1MTGNAAOwgxvOfFEDLkMV56IQt2Yk",
            "location": {
                "type": "Point",
                "coordinates": [
                    30.0663542,
                    31.2712785
                ],
                "address": null
            },
            "subscribeClubs": [
                {
                    "orderId": 287,
                    "product": {
                        "id": 1,
                        "name": [
                            {
                                "localeCode": "ar",
                                "text": "شهري"
                            },
                            {
                                "localeCode": "en",
                                "text": "Monthly"
                            }
                        ],
                        "rewardPoints": null,
                        "purchaseRewardPoints": null,
                        "finalPrice": 20,
                        "published": true,
                        "club": {
                            "id": 1,
                            "name": [
                                {
                                    "localeCode": "ar",
                                    "text": "جولدز جيم"
                                },
                                {
                                    "localeCode": "en",
                                    "text": "Golds Gym"
                                }
                            ],
                            "logo": "data/clubs/1/layer856@3x.jpg",
                            "aboutClub": [
                                {
                                    "localeCode": "ar",
                                    "text": "<p>جولدز جيم جولدز جيم جولدز جيم جولدز جيم</p>"
                                },
                                {
                                    "localeCode": "en",
                                    "text": "<p>Golds Gym &nbsp;Golds Gym&nbsp;</p>"
                                }
                            ],
                            "images": [
                                "data/clubs/1/maleAthleteWorkoutRunningExerciseMachineActiveSportTrainingGym@3x.png"
                            ],
                            "workHours": null,
                            "packagesClubs": null,
                            "rating": null,
                            "totalRating": null,
                            "published": true,
                            "city": null,
                            "bookAheadOfTime": true,
                            "mainBranchClub": {
                                "id": 1,
                                "location": {
                                    "type": "Point",
                                    "coordinates": [
                                        30.0619739,
                                        31.3449857
                                    ],
                                    "address": "مكرم عبيد، Al Manteqah as Sadesah, Nasr City, Egypt"
                                },
                                "published": true,
                                "mainBranch": true,
                                "workTimes": [
                                    {
                                        "day": "saturday",
                                        "open": null,
                                        "close": null,
                                        "available": "no"
                                    },
                                    {
                                        "day": "sunday",
                                        "open": "00:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    },
                                    {
                                        "day": "monday",
                                        "open": "00:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    },
                                    {
                                        "day": "tuesday",
                                        "open": "00:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    },
                                    {
                                        "day": "wednesday",
                                        "open": "00:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    },
                                    {
                                        "day": "thursday",
                                        "open": "00:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    },
                                    {
                                        "day": "friday",
                                        "open": "00:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    }
                                ],
                                "city": {
                                    "id": 1,
                                    "name": [
                                        {
                                            "localeCode": "ar",
                                            "text": "الرياض"
                                        },
                                        {
                                            "localeCode": "en",
                                            "text": "Riyadh"
                                        }
                                    ]
                                }
                            },
                            "profitRatio": 10
                        },
                        "monthsNumber": 1
                    },
                    "rewardPoints": null,
                    "price": 20,
                    "totalPrice": 20,
                    "type": "clubs",
                    "subscribeStartAt": "2022-05-12",
                    "subscribeEndAt": "2022-06-11",
                    "club": 1,
                    "createdBy": {
                        "id": 7,
                        "firstName": "omar",
                        "lastName": "tarek",
                        "email": "omart8703@gmail.com",
                        "phoneNumber": "966551234567"
                    },
                    "updatedBy": {
                        "id": 7,
                        "firstName": "omar",
                        "lastName": "tarek",
                        "email": "omart8703@gmail.com",
                        "phoneNumber": "966551234567"
                    },
                    "deletedBy": null,
                    "id": 388,
                    "updatedAt": {
                        "$date": {
                            "$numberLong": "1652370569323"
                        }
                    },
                    "createdAt": {
                        "$date": {
                            "$numberLong": "1652370569323"
                        }
                    }
                },
                {
                    "orderId": 485,
                    "product": {
                        "id": 18,
                        "name": [
                            {
                                "localeCode": "ar",
                                "text": "شهر"
                            },
                            {
                                "localeCode": "en",
                                "text": "شهر"
                            }
                        ],
                        "rewardPoints": null,
                        "purchaseRewardPoints": null,
                        "finalPrice": 250,
                        "published": true,
                        "club": {
                            "id": 18,
                            "name": [
                                {
                                    "localeCode": "ar",
                                    "text": "نادي عمر"
                                },
                                {
                                    "localeCode": "en",
                                    "text": "نادي عمر"
                                }
                            ],
                            "logo": "data/clubs/18/wallet.jpg",
                            "aboutClub": [
                                {
                                    "localeCode": "ar",
                                    "text": "<p>نادي عمرنادي عمر</p>"
                                },
                                {
                                    "localeCode": "en",
                                    "text": "<p>نادي عمرنادي عمرنادي عمر</p>"
                                }
                            ],
                            "images": [
                                "data/clubs/18/Image-from-iOS-4.png"
                            ],
                            "workHours": null,
                            "packagesClubs": null,
                            "rating": null,
                            "totalRating": null,
                            "published": true,
                            "city": null,
                            "bookAheadOfTime": false,
                            "mainBranchClub": {
                                "id": 19,
                                "location": {
                                    "type": "Point",
                                    "coordinates": [
                                        30.0701608,
                                        31.28157
                                    ],
                                    "address": "العباسية، Al Abbaseyah Al Gharbeyah, El Weili, Egypt"
                                },
                                "published": true,
                                "mainBranch": true,
                                "workTimes": [
                                    {
                                        "day": "saturday",
                                        "open": "10:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    },
                                    {
                                        "day": "sunday",
                                        "open": "10:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    },
                                    {
                                        "day": "monday",
                                        "open": "10:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    },
                                    {
                                        "day": "tuesday",
                                        "open": "10:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    },
                                    {
                                        "day": "wednesday",
                                        "open": "10:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    },
                                    {
                                        "day": "thursday",
                                        "open": "10:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    },
                                    {
                                        "day": "friday",
                                        "open": null,
                                        "close": null,
                                        "available": "no"
                                    }
                                ],
                                "city": {
                                    "id": 22,
                                    "name": [
                                        {
                                            "localeCode": "ar",
                                            "text": "جدة"
                                        },
                                        {
                                            "localeCode": "en",
                                            "text": "Jeddah"
                                        }
                                    ]
                                }
                            },
                            "profitRatio": 0
                        },
                        "monthsNumber": 1
                    },
                    "rewardPoints": null,
                    "price": 250,
                    "totalPrice": 250,
                    "type": "clubs",
                    "subscribeStartAt": "2022-05-31",
                    "subscribeEndAt": "2022-06-30",
                    "club": 18,
                    "createdBy": {
                        "id": 7,
                        "firstName": "omar",
                        "lastName": "tarek",
                        "email": "omart8703@gmail.com",
                        "phoneNumber": "966551234567"
                    },
                    "updatedBy": {
                        "id": 7,
                        "firstName": "omar",
                        "lastName": "tarek",
                        "email": "omart8703@gmail.com",
                        "phoneNumber": "966551234567"
                    },
                    "deletedBy": null,
                    "id": 690,
                    "updatedAt": {
                        "$date": {
                            "$numberLong": "1654000362687"
                        }
                    },
                    "createdAt": {
                        "$date": {
                            "$numberLong": "1654000362687"
                        }
                    }
                },
                {
                    "orderId": 524,
                    "product": {
                        "id": 16,
                        "name": [
                            {
                                "localeCode": "ar",
                                "text": "باقة جديدة"
                            },
                            {
                                "localeCode": "en",
                                "text": "new package 1"
                            }
                        ],
                        "rewardPoints": null,
                        "purchaseRewardPoints": null,
                        "finalPrice": 2,
                        "published": true,
                        "club": {
                            "id": 16,
                            "name": [
                                {
                                    "localeCode": "ar",
                                    "text": "Test16"
                                },
                                {
                                    "localeCode": "en",
                                    "text": "Test16"
                                }
                            ],
                            "logo": "data/clubs/16/1d83834ba8fa525bbff21a3f201cc93870cf12a7715bd8cf12a426fc71c15005.jpg",
                            "aboutClub": [
                                {
                                    "localeCode": "ar",
                                    "text": null
                                },
                                {
                                    "localeCode": "en",
                                    "text": null
                                }
                            ],
                            "images": [
                                "data/clubs/16/1d83834ba8fa525bbff21a3f201cc93870cf12a7715bd8cf12a426fc71c15005.jpg"
                            ],
                            "workHours": null,
                            "packagesClubs": null,
                            "rating": null,
                            "totalRating": null,
                            "published": true,
                            "city": null,
                            "bookAheadOfTime": false,
                            "mainBranchClub": {
                                "id": 17,
                                "location": {
                                    "type": "Point",
                                    "coordinates": [
                                        30.0619739,
                                        31.3449857
                                    ],
                                    "address": "مكرم عبيد، Al Manteqah as Sadesah, Nasr City, Egypt"
                                },
                                "published": true,
                                "mainBranch": true,
                                "workTimes": [
                                    {
                                        "day": "saturday",
                                        "open": null,
                                        "close": null,
                                        "available": "no"
                                    },
                                    {
                                        "day": "sunday",
                                        "open": null,
                                        "close": null,
                                        "available": "no"
                                    },
                                    {
                                        "day": "monday",
                                        "open": null,
                                        "close": null,
                                        "available": "no"
                                    },
                                    {
                                        "day": "tuesday",
                                        "open": null,
                                        "close": null,
                                        "available": "no"
                                    },
                                    {
                                        "day": "wednesday",
                                        "open": "00:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    },
                                    {
                                        "day": "thursday",
                                        "open": null,
                                        "close": null,
                                        "available": "no"
                                    },
                                    {
                                        "day": "friday",
                                        "open": null,
                                        "close": null,
                                        "available": "no"
                                    }
                                ],
                                "city": {
                                    "id": 1,
                                    "name": [
                                        {
                                            "localeCode": "ar",
                                            "text": "الرياض"
                                        },
                                        {
                                            "localeCode": "en",
                                            "text": "Riyadh"
                                        }
                                    ]
                                }
                            },
                            "profitRatio": 10
                        },
                        "monthsNumber": 2
                    },
                    "rewardPoints": null,
                    "price": 2,
                    "totalPrice": 2,
                    "type": "clubs",
                    "subscribeStartAt": "2022-06-15",
                    "subscribeEndAt": "2022-08-14",
                    "club": 16,
                    "createdBy": {
                        "id": 7,
                        "firstName": "omar",
                        "lastName": "tarek",
                        "email": "omart8703@gmail.com",
                        "phoneNumber": "966558234567"
                    },
                    "updatedBy": {
                        "id": 7,
                        "firstName": "omar",
                        "lastName": "tarek",
                        "email": "omart8703@gmail.com",
                        "phoneNumber": "966558234567"
                    },
                    "deletedBy": null,
                    "id": 768,
                    "updatedAt": {
                        "$date": {
                            "$numberLong": "1655288621067"
                        }
                    },
                    "createdAt": {
                        "$date": {
                            "$numberLong": "1655288621067"
                        }
                    }
                },
                {
                    "orderId": 541,
                    "product": {
                        "id": 14,
                        "name": [
                            {
                                "localeCode": "ar",
                                "text": "test2"
                            },
                            {
                                "localeCode": "en",
                                "text": "test2"
                            }
                        ],
                        "rewardPoints": null,
                        "purchaseRewardPoints": null,
                        "finalPrice": 2,
                        "published": true,
                        "club": {
                            "id": 14,
                            "name": [
                                {
                                    "localeCode": "ar",
                                    "text": "test14"
                                },
                                {
                                    "localeCode": "en",
                                    "text": "test2"
                                }
                            ],
                            "logo": "data/clubs/14/1d83834ba8fa525bbff21a3f201cc93870cf12a7715bd8cf12a426fc71c15005.jpg",
                            "aboutClub": [
                                {
                                    "localeCode": "ar",
                                    "text": null
                                },
                                {
                                    "localeCode": "en",
                                    "text": null
                                }
                            ],
                            "images": [
                                "data/clubs/14/1d83834ba8fa525bbff21a3f201cc93870cf12a7715bd8cf12a426fc71c15005.jpg"
                            ],
                            "workHours": null,
                            "packagesClubs": null,
                            "rating": null,
                            "totalRating": null,
                            "published": true,
                            "city": null,
                            "bookAheadOfTime": false,
                            "mainBranchClub": {
                                "id": 15,
                                "location": {
                                    "type": "Point",
                                    "coordinates": [
                                        30.0619739,
                                        31.3449857
                                    ],
                                    "address": "مكرم عبيد، Al Manteqah as Sadesah, Nasr City, Egypt"
                                },
                                "published": true,
                                "mainBranch": true,
                                "workTimes": [
                                    {
                                        "day": "saturday",
                                        "open": null,
                                        "close": null,
                                        "available": "no"
                                    },
                                    {
                                        "day": "sunday",
                                        "open": null,
                                        "close": null,
                                        "available": "no"
                                    },
                                    {
                                        "day": "monday",
                                        "open": null,
                                        "close": null,
                                        "available": "no"
                                    },
                                    {
                                        "day": "tuesday",
                                        "open": null,
                                        "close": null,
                                        "available": "no"
                                    },
                                    {
                                        "day": "wednesday",
                                        "open": "00:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    },
                                    {
                                        "day": "thursday",
                                        "open": null,
                                        "close": null,
                                        "available": "no"
                                    },
                                    {
                                        "day": "friday",
                                        "open": null,
                                        "close": null,
                                        "available": "no"
                                    }
                                ],
                                "city": {
                                    "id": 1,
                                    "name": [
                                        {
                                            "localeCode": "ar",
                                            "text": "الرياض"
                                        },
                                        {
                                            "localeCode": "en",
                                            "text": "Riyadh"
                                        }
                                    ]
                                }
                            },
                            "profitRatio": 10
                        },
                        "monthsNumber": 2
                    },
                    "rewardPoints": null,
                    "price": 2,
                    "totalPrice": 2,
                    "type": "clubs",
                    "subscribeStartAt": "2022-06-16",
                    "subscribeEndAt": "2022-08-15",
                    "club": 14,
                    "createdBy": {
                        "id": 7,
                        "firstName": "omar",
                        "lastName": "tarek",
                        "email": "omart8703@gmail.com",
                        "phoneNumber": "966551234567"
                    },
                    "updatedBy": {
                        "id": 7,
                        "firstName": "omar",
                        "lastName": "tarek",
                        "email": "omart8703@gmail.com",
                        "phoneNumber": "966551234567"
                    },
                    "deletedBy": null,
                    "id": 805,
                    "updatedAt": {
                        "$date": {
                            "$numberLong": "1655398354269"
                        }
                    },
                    "createdAt": {
                        "$date": {
                            "$numberLong": "1655398354269"
                        }
                    }
                },
                {
                    "orderId": 840,
                    "product": {
                        "id": 1,
                        "name": [
                            {
                                "localeCode": "ar",
                                "text": "شهري"
                            },
                            {
                                "localeCode": "en",
                                "text": "Monthly"
                            }
                        ],
                        "rewardPoints": null,
                        "purchaseRewardPoints": null,
                        "finalPrice": 20,
                        "published": true,
                        "club": {
                            "id": 1,
                            "name": [
                                {
                                    "localeCode": "ar",
                                    "text": "جولدز جيم"
                                },
                                {
                                    "localeCode": "en",
                                    "text": "Golds Gym"
                                }
                            ],
                            "logo": "data/clubs/1/layer856@3x.jpg",
                            "aboutClub": [
                                {
                                    "localeCode": "ar",
                                    "text": "<p>جولدز جيم جولدز جيم جولدز جيم جولدز جيم</p>"
                                },
                                {
                                    "localeCode": "en",
                                    "text": "<p>Golds Gym &nbsp;Golds Gym&nbsp;</p>"
                                }
                            ],
                            "images": [
                                "data/clubs/1/maleAthleteWorkoutRunningExerciseMachineActiveSportTrainingGym@3x.png"
                            ],
                            "workHours": null,
                            "packagesClubs": null,
                            "rating": null,
                            "totalRating": null,
                            "published": true,
                            "city": null,
                            "bookAheadOfTime": true,
                            "mainBranchClub": {
                                "id": 1,
                                "location": {
                                    "type": "Point",
                                    "coordinates": [
                                        30.0619739,
                                        31.3449857
                                    ],
                                    "address": "مكرم عبيد، Al Manteqah as Sadesah, Nasr City, Egypt"
                                },
                                "published": true,
                                "mainBranch": true,
                                "workTimes": [
                                    {
                                        "day": "saturday",
                                        "open": null,
                                        "close": null,
                                        "available": "no"
                                    },
                                    {
                                        "day": "sunday",
                                        "open": "00:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    },
                                    {
                                        "day": "monday",
                                        "open": "00:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    },
                                    {
                                        "day": "tuesday",
                                        "open": "00:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    },
                                    {
                                        "day": "wednesday",
                                        "open": "00:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    },
                                    {
                                        "day": "thursday",
                                        "open": "00:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    },
                                    {
                                        "day": "friday",
                                        "open": "00:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    }
                                ],
                                "city": {
                                    "id": 1,
                                    "name": [
                                        {
                                            "localeCode": "ar",
                                            "text": "الرياض"
                                        },
                                        {
                                            "localeCode": "en",
                                            "text": "Riyadh"
                                        }
                                    ]
                                }
                            },
                            "profitRatio": 10
                        },
                        "monthsNumber": 1
                    },
                    "rewardPoints": null,
                    "price": 20,
                    "totalPrice": 20,
                    "type": "clubs",
                    "subscribeStartAt": "2022-09-25",
                    "subscribeEndAt": "2022-10-24",
                    "club": 1,
                    "createdBy": {
                        "id": 7,
                        "firstName": "omar",
                        "lastName": "tarek",
                        "email": "omart8703@gmail.com",
                        "phoneNumber": "966551234567"
                    },
                    "updatedBy": {
                        "id": 7,
                        "firstName": "omar",
                        "lastName": "tarek",
                        "email": "omart8703@gmail.com",
                        "phoneNumber": "966551234567"
                    },
                    "deletedBy": null,
                    "id": 1226,
                    "updatedAt": {
                        "$date": {
                            "$numberLong": "1664101505144"
                        }
                    },
                    "createdAt": {
                        "$date": {
                            "$numberLong": "1664101505144"
                        }
                    }
                },
                {
                    "orderId": 948,
                    "product": {
                        "id": 1,
                        "name": [
                            {
                                "localeCode": "ar",
                                "text": "شهري"
                            },
                            {
                                "localeCode": "en",
                                "text": "Monthly"
                            }
                        ],
                        "rewardPoints": null,
                        "purchaseRewardPoints": null,
                        "finalPrice": 20,
                        "published": true,
                        "club": {
                            "id": 1,
                            "name": [
                                {
                                    "localeCode": "ar",
                                    "text": "جولدز جيم"
                                },
                                {
                                    "localeCode": "en",
                                    "text": "Golds Gym"
                                }
                            ],
                            "logo": "data/clubs/1/layer856@3x.jpg",
                            "aboutClub": [
                                {
                                    "localeCode": "ar",
                                    "text": "<p>جولدز جيم جولدز جيم جولدز جيم جولدز جيم</p>"
                                },
                                {
                                    "localeCode": "en",
                                    "text": "<p>Golds Gym &nbsp;Golds Gym&nbsp;</p>"
                                }
                            ],
                            "images": [
                                "data/clubs/1/maleAthleteWorkoutRunningExerciseMachineActiveSportTrainingGym@3x.png"
                            ],
                            "workHours": null,
                            "packagesClubs": null,
                            "rating": null,
                            "totalRating": null,
                            "published": true,
                            "city": null,
                            "bookAheadOfTime": true,
                            "mainBranchClub": {
                                "id": 1,
                                "location": {
                                    "type": "Point",
                                    "coordinates": [
                                        30.0619739,
                                        31.3449857
                                    ],
                                    "address": "مكرم عبيد، Al Manteqah as Sadesah, Nasr City, Egypt"
                                },
                                "published": true,
                                "mainBranch": true,
                                "workTimes": [
                                    {
                                        "day": "saturday",
                                        "open": null,
                                        "close": null,
                                        "available": "no"
                                    },
                                    {
                                        "day": "sunday",
                                        "open": "00:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    },
                                    {
                                        "day": "monday",
                                        "open": "00:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    },
                                    {
                                        "day": "tuesday",
                                        "open": "00:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    },
                                    {
                                        "day": "wednesday",
                                        "open": "00:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    },
                                    {
                                        "day": "thursday",
                                        "open": "00:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    },
                                    {
                                        "day": "friday",
                                        "open": "00:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    }
                                ],
                                "city": {
                                    "id": 1,
                                    "name": [
                                        {
                                            "localeCode": "ar",
                                            "text": "الرياض"
                                        },
                                        {
                                            "localeCode": "en",
                                            "text": "Riyadh"
                                        }
                                    ]
                                }
                            },
                            "profitRatio": 10
                        },
                        "monthsNumber": 1
                    },
                    "rewardPoints": null,
                    "price": 20,
                    "totalPrice": 20,
                    "type": "clubs",
                    "subscribeStartAt": "2022-10-24",
                    "subscribeEndAt": "2022-11-23",
                    "club": 1,
                    "createdBy": {
                        "id": 7,
                        "firstName": "omar",
                        "lastName": "tarek",
                        "email": "omart8703@gmail.com",
                        "phoneNumber": "966551234567"
                    },
                    "updatedBy": {
                        "id": 7,
                        "firstName": "omar",
                        "lastName": "tarek",
                        "email": "omart8703@gmail.com",
                        "phoneNumber": "966551234567"
                    },
                    "deletedBy": null,
                    "id": 1404,
                    "updatedAt": {
                        "$date": {
                            "$numberLong": "1666614877664"
                        }
                    },
                    "createdAt": {
                        "$date": {
                            "$numberLong": "1666614877664"
                        }
                    }
                },
                {
                    "orderId": 1033,
                    "product": {
                        "id": 1,
                        "name": [
                            {
                                "localeCode": "ar",
                                "text": "شهري"
                            },
                            {
                                "localeCode": "en",
                                "text": "Monthly"
                            }
                        ],
                        "rewardPoints": null,
                        "purchaseRewardPoints": null,
                        "finalPrice": 20,
                        "published": true,
                        "club": {
                            "id": 1,
                            "name": [
                                {
                                    "localeCode": "ar",
                                    "text": "جولدز جيم"
                                },
                                {
                                    "localeCode": "en",
                                    "text": "Golds Gym"
                                }
                            ],
                            "logo": "data/clubs/1/layer856@3x.jpg",
                            "aboutClub": [
                                {
                                    "localeCode": "ar",
                                    "text": "<p>جولدز جيم جولدز جيم جولدز جيم جولدز جيم</p>"
                                },
                                {
                                    "localeCode": "en",
                                    "text": "<p>Golds Gym &nbsp;Golds Gym&nbsp;</p>"
                                }
                            ],
                            "images": [
                                "data/clubs/1/maleAthleteWorkoutRunningExerciseMachineActiveSportTrainingGym@3x.png"
                            ],
                            "workHours": null,
                            "packagesClubs": null,
                            "rating": null,
                            "totalRating": null,
                            "published": true,
                            "city": null,
                            "bookAheadOfTime": true,
                            "mainBranchClub": {
                                "id": 1,
                                "location": {
                                    "type": "Point",
                                    "coordinates": [
                                        30.0619739,
                                        31.3449857
                                    ],
                                    "address": "مكرم عبيد، Al Manteqah as Sadesah, Nasr City, Egypt"
                                },
                                "published": true,
                                "mainBranch": true,
                                "workTimes": [
                                    {
                                        "day": "saturday",
                                        "open": null,
                                        "close": null,
                                        "available": "no"
                                    },
                                    {
                                        "day": "sunday",
                                        "open": "00:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    },
                                    {
                                        "day": "monday",
                                        "open": "00:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    },
                                    {
                                        "day": "tuesday",
                                        "open": "00:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    },
                                    {
                                        "day": "wednesday",
                                        "open": "00:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    },
                                    {
                                        "day": "thursday",
                                        "open": "00:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    },
                                    {
                                        "day": "friday",
                                        "open": "00:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    }
                                ],
                                "city": {
                                    "id": 1,
                                    "name": [
                                        {
                                            "localeCode": "ar",
                                            "text": "الرياض"
                                        },
                                        {
                                            "localeCode": "en",
                                            "text": "Riyadh"
                                        }
                                    ]
                                }
                            },
                            "profitRatio": 10
                        },
                        "monthsNumber": 1
                    },
                    "rewardPoints": null,
                    "price": 20,
                    "totalPrice": 20,
                    "type": "clubs",
                    "subscribeStartAt": "2022-12-11",
                    "subscribeEndAt": "2023-01-10",
                    "club": 1,
                    "createdBy": {
                        "id": 7,
                        "firstName": "omar",
                        "lastName": "tarek",
                        "email": "omart8703@gmail.com",
                        "phoneNumber": "966551234567"
                    },
                    "updatedBy": {
                        "id": 7,
                        "firstName": "omar",
                        "lastName": "tarek",
                        "email": "omart8703@gmail.com",
                        "phoneNumber": "966551234567"
                    },
                    "deletedBy": null,
                    "id": 1612,
                    "updatedAt": {
                        "$date": {
                            "$numberLong": "1670756471765"
                        }
                    },
                    "createdAt": {
                        "$date": {
                            "$numberLong": "1670756471765"
                        }
                    }
                },
                {
                    "orderId": 1043,
                    "product": {
                        "id": 1,
                        "name": [
                            {
                                "localeCode": "ar",
                                "text": "شهري"
                            },
                            {
                                "localeCode": "en",
                                "text": "Monthly"
                            }
                        ],
                        "rewardPoints": null,
                        "purchaseRewardPoints": null,
                        "finalPrice": 20,
                        "published": true,
                        "club": {
                            "id": 1,
                            "name": [
                                {
                                    "localeCode": "ar",
                                    "text": "جولدز جيم"
                                },
                                {
                                    "localeCode": "en",
                                    "text": "Golds Gym"
                                }
                            ],
                            "logo": "data/clubs/1/layer856@3x.jpg",
                            "aboutClub": [
                                {
                                    "localeCode": "ar",
                                    "text": "<p>جولدز جيم جولدز جيم جولدز جيم جولدز جيم</p>"
                                },
                                {
                                    "localeCode": "en",
                                    "text": "<p>Golds Gym &nbsp;Golds Gym&nbsp;</p>"
                                }
                            ],
                            "images": [
                                "data/clubs/1/maleAthleteWorkoutRunningExerciseMachineActiveSportTrainingGym@3x.png"
                            ],
                            "workHours": null,
                            "packagesClubs": null,
                            "rating": null,
                            "totalRating": null,
                            "published": true,
                            "city": null,
                            "bookAheadOfTime": true,
                            "mainBranchClub": {
                                "id": 1,
                                "location": {
                                    "type": "Point",
                                    "coordinates": [
                                        30.0619739,
                                        31.3449857
                                    ],
                                    "address": "مكرم عبيد، Al Manteqah as Sadesah, Nasr City, Egypt"
                                },
                                "published": true,
                                "mainBranch": true,
                                "workTimes": [
                                    {
                                        "day": "saturday",
                                        "open": null,
                                        "close": null,
                                        "available": "no"
                                    },
                                    {
                                        "day": "sunday",
                                        "open": "00:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    },
                                    {
                                        "day": "monday",
                                        "open": "00:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    },
                                    {
                                        "day": "tuesday",
                                        "open": "00:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    },
                                    {
                                        "day": "wednesday",
                                        "open": "00:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    },
                                    {
                                        "day": "thursday",
                                        "open": "00:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    },
                                    {
                                        "day": "friday",
                                        "open": "00:00",
                                        "close": "23:00",
                                        "available": "yes"
                                    }
                                ],
                                "city": {
                                    "id": 1,
                                    "name": [
                                        {
                                            "localeCode": "ar",
                                            "text": "الرياض"
                                        },
                                        {
                                            "localeCode": "en",
                                            "text": "Riyadh"
                                        }
                                    ]
                                }
                            },
                            "profitRatio": 10
                        },
                        "monthsNumber": 1
                    },
                    "rewardPoints": null,
                    "price": 20,
                    "totalPrice": 20,
                    "type": "clubs",
                    "subscribeStartAt": "2023-02-08",
                    "subscribeEndAt": "2023-03-07",
                    "club": 1,
                    "createdBy": {
                        "id": 7,
                        "firstName": "omar",
                        "lastName": "tarek",
                        "email": "omart8703@gmail.com",
                        "phoneNumber": "966551234567"
                    },
                    "updatedBy": {
                        "id": 7,
                        "firstName": "omar",
                        "lastName": "tarek",
                        "email": "omart8703@gmail.com",
                        "phoneNumber": "966551234567"
                    },
                    "deletedBy": null,
                    "id": 1622,
                    "updatedAt": {
                        "$date": {
                            "$numberLong": "1675849532490"
                        }
                    },
                    "createdAt": {
                        "$date": {
                            "$numberLong": "1675849532490"
                        }
                    }
                }
            ],
            "deviceCart": "7A048D5F-5553-4DA6-AC5D-C9077D0850DE",
            "firstName": "omar",
            "lastName": "tarek",
            "email": "omart8703@gmail.com",
            "phoneNumber": "966551234567",
            "id": 7,
            "totalNotifications": 1,
            "totalOrders": 165,
            "rewardPoint": 1000,
            "rewardPointWithdraw": 2263,
            "rewardPointDeposit": 3263,
            "verificationCode": 6821,
            "favoritesCount": 3,
            "totalRefusedReceive": 0,
            "newVerificationCode": 0,
            "walletBalance": 3814.19,
            "totalOrdersPurchases": 33518,
            "published": true,
            "isVerified": true,
            "addresses": [
                {
                    "id": 11,
                    "firstName": "omar",
                    "lastName": "tarek",
                    "email": "omart8703@gmail.com",
                    "address": "حارة الشيشينى بين الجناين الوايلى مصر",
                    "phoneNumber": "966550123123",
                    "buildingNumber": null,
                    "flatNumber": null,
                    "verificationCode": null,
                    "floorNumber": null,
                    "type": null,
                    "specialMark": "حارة الشيشينى",
                    "isPrimary": false,
                    "verified": true,
                    "district": "بين الجناين",
                    "location": {
                        "type": "Point",
                        "coordinates": [
                            30.61486834855349,
                            32.29499816894531
                        ],
                        "address": "حارة الشيشينى بين الجناين الوايلى مصر"
                    },
                    "country": null,
                    "city": {
                        "id": 1,
                        "name": "الرياض"
                    }
                }
            ],
            "cart": {
                "group": {
                    "id": 22,
                    "name": [
                        {
                            "localeCode": "ar",
                            "text": "Test"
                        },
                        {
                            "localeCode": "en",
                            "text": "تجربة"
                        }
                    ],
                    "conditionType": "totalPurchaseAmount",
                    "conditionValue": null,
                    "specialDiscount": 3,
                    "freeShipping": false,
                    "freeExpressShipping": false,
                    "nameGroup": [
                        {
                            "name": "productsInStores"
                        },
                        {
                            "name": "typesInRestaurant"
                        },
                        {
                            "name": "reserveSpecialist"
                        },
                        {
                            "name": "clubSubscription"
                        }
                    ],
                    "published": true
                },
                "deviceId": "7A048D5F-5553-4DA6-AC5D-C9077D0850DE",
                "customer": {
                    "id": 7,
                    "firstName": "omar",
                    "lastName": "tarek",
                    "email": "omart8703@gmail.com",
                    "phoneNumber": "966551234567"
                },
                "type": "products",
                "totalPrice": 0,
                "taxes": 0,
                "finalPrice": 0,
                "originalPrice": 0,
                "isActiveRewardPoints": false,
                "subscription": false,
                "rewardPoints": 0,
                "totalQuantity": 0,
                "purchaseRewardPoints": 0,
                "taxesValue": 15,
                "totalSubscription": 0,
                "totalItems": 0,
                "items": [],
                "canUseRewardPoints": false,
                "totals": [
                    {
                        "text": "إجمالي الفاتورة",
                        "price": 0,
                        "priceText": "0 ر.س",
                        "type": "finalPriceText"
                    }
                ]
            },
            "group": {
                "id": 22,
                "conditionType": "totalPurchaseAmount",
                "conditionValue": null,
                "specialDiscount": 3,
                "freeShipping": false,
                "freeExpressShipping": false,
                "totalCustomers": null,
                "nameGroup": [
                    {
                        "name": "productsInStores"
                    },
                    {
                        "name": "typesInRestaurant"
                    },
                    {
                        "name": "reserveSpecialist"
                    },
                    {
                        "name": "clubSubscription"
                    }
                ],
                "published": true,
                "name": "Test",
                "groupName": "Test"
            },
            "cartSubscription": {
                "deviceId": null,
                "customer": null,
                "type": null,
                "totalPrice": 0,
                "taxes": 0,
                "finalPrice": 0,
                "originalPrice": 0,
                "isActiveRewardPoints": false,
                "subscription": true,
                "rewardPoints": 0,
                "totalQuantity": 0,
                "purchaseRewardPoints": 0,
                "taxesValue": 0,
                "totalSubscription": 0,
                "totalItems": 0,
                "items": [],
                "canUseRewardPoints": false,
                "totals": [
                    {
                        "text": "إجمالي الفاتورة",
                        "price": 0,
                        "priceText": "0 ر.س",
                        "type": "finalPriceText"
                    }
                ]
            },
            "cartMeal": {
                "group": {
                    "id": 22,
                    "name": [
                        {
                            "localeCode": "ar",
                            "text": "Test"
                        },
                        {
                            "localeCode": "en",
                            "text": "تجربة"
                        }
                    ],
                    "conditionType": "totalPurchaseAmount",
                    "conditionValue": null,
                    "specialDiscount": 3,
                    "freeShipping": false,
                    "freeExpressShipping": false,
                    "nameGroup": [
                        {
                            "name": "productsInStores"
                        },
                        {
                            "name": "typesInRestaurant"
                        },
                        {
                            "name": "reserveSpecialist"
                        },
                        {
                            "name": "clubSubscription"
                        }
                    ],
                    "published": true
                },
                "deviceId": "7A048D5F-5553-4DA6-AC5D-C9077D0850DE",
                "customer": {
                    "id": 7,
                    "firstName": "omar",
                    "lastName": "tarek",
                    "email": "omart8703@gmail.com",
                    "phoneNumber": "966551234567"
                },
                "type": "food",
                "totalPrice": 0,
                "taxes": 0,
                "finalPrice": 0,
                "originalPrice": 0,
                "isActiveRewardPoints": false,
                "subscription": false,
                "rewardPoints": 0,
                "totalQuantity": 0,
                "purchaseRewardPoints": 0,
                "taxesValue": 15,
                "totalSubscription": 0,
                "totalItems": 0,
                "items": [],
                "canUseRewardPoints": false,
                "totals": [
                    {
                        "text": "المبلغ الإجمالي",
                        "price": 0,
                        "priceText": "0 ر.س",
                        "type": "finalPriceText"
                    }
                ]
            },
            "createdAt": {
                "format": "09-05-2022",
                "timestamp": 1652091254,
                "text": "الاثنين، ٩ مايو ٢٠٢٢ في ١٢:١٤ م",
                "humanTime": "منذ 9 أشهر"
            },
            "birthDate": null
        }

# </a> Responses 

## Unauthorized error

__*Response code : 401*__
```json 
{
    "message" : "Unauthenticated"
}
```

## Validation error 
__*Response code : 422*__

```json 
{
    "errors" {
        "Key" : "Error message"
    }
}
```
## Success  
__*Response code : 200*__
```json 
{
    "records" [
        {
            "success": true,
        },
    ]
}
```
