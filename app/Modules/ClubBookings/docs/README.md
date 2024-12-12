# ClubBooking API DOCS
 It is about club reservations for each club manager who receives his reservations

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
|/api/admin/clubbookings            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
|/api/admin/clubbookings | GET           |-|  [Response](#Response)         |
|/api/admin/clubbookings/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/clubbookings/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/clubbookings/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/clubbookings        |GET           |-| [Response](#Response)|
|/api/clubbookings/{id}        |GET           |-|[Response](#Response)|
|/api/clubbookings/{id}/status       |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new ClubBooking 

```json
{
"customer":"Int"
"name":"String"
"phone":"String"
"clubBranch":"String"
"date":"2021-2-2"
"time":"10:00"
"status":"String" //pending|accepted|rejected|completed|canceled
} 
```

# <a name="Update"> </a> Update ClubBooking

```json
{
"customer":"Int"
"name":"String"
"phone":"String"
"clubBranch":"String"
"date":"2021-2-2"
"time":"10:00"
"status":"String" //pending|accepted|rejected|completed|canceled
} 
```
# <a name="Response"> 
 {
                "name": "Islam iOS",
                "status": "pending",
                "time": "00:00",
                "phone": "966550123123",
                "nextStatus": [
                    "accepted",
                    "completed",
                    "rejected",
                    "canceled"
                ],
                "id": 186,
                "customer": {
                    "location": null,
                    "subscribeClubs": null,
                    "firstName": "Islam",
                    "lastName": "iOS",
                    "email": "islam@gmail.com",
                    "phoneNumber": "966550123123",
                    "accessToken": "",
                    "id": 163,
                    "totalNotifications": 0,
                    "totalOrders": 0,
                    "rewardPoint": 0,
                    "rewardPointWithdraw": 0,
                    "rewardPointDeposit": 0,
                    "totalRefusedReceive": 0,
                    "walletBalance": 0,
                    "totalOrdersPurchases": 0,
                    "published": false,
                    "isVerified": false,
                    "cartMeal": null,
                    "birthDate": null
                },
                "clubBranch": {
                    "id": 195,
                    "location": {
                        "type": "Point",
                        "coordinates": [
                            30.0619739,
                            31.3449857
                        ],
                        "address": "مكرم عبيد، Al Manteqah as Sadesah, Nasr City, Egypt"
                    },
                    "workTimes": [
                        {
                            "open": "10:00 AM",
                            "close": "11:00 AM",
                            "day": "السبت",
                            "available": "yes"
                        },
                        {
                            "open": "12:00 AM",
                            "close": "11:00 PM",
                            "day": "الأحد",
                            "available": "yes"
                        },
                        {
                            "open": "00:00",
                            "close": "10:00 PM",
                            "day": "الأثنين",
                            "available": "yes"
                        },
                        {
                            "open": "12:00 AM",
                            "close": "11:00 PM",
                            "day": "الثلاثاء",
                            "available": "yes"
                        }
                    ],
                    "published": true,
                    "mainBranch": true,
                    "city": {
                        "id": 33,
                        "name": "Adena Gillespie"
                    }
                },
                "club": {
                    "id": 181,
                    "mainBranchClub": {
                        "id": 195,
                        "location": {
                            "type": "Point",
                            "coordinates": [
                                30.0619739,
                                31.3449857
                            ],
                            "address": "مكرم عبيد، Al Manteqah as Sadesah, Nasr City, Egypt"
                        },
                        "workTimes": [
                            {
                                "open": "10:00 AM",
                                "close": "11:00 AM",
                                "day": "السبت",
                                "available": "yes"
                            },
                            {
                                "open": "12:00 AM",
                                "close": "11:00 PM",
                                "day": "الأحد",
                                "available": "yes"
                            },
                            {
                                "open": "00:00",
                                "close": "10:00 PM",
                                "day": "الأثنين",
                                "available": "yes"
                            },
                            {
                                "open": "12:00 AM",
                                "close": "11:00 PM",
                                "day": "الثلاثاء",
                                "available": "yes"
                            }
                        ],
                        "published": true,
                        "mainBranch": true,
                        "city": {
                            "id": 33,
                            "name": "Adena Gillespie"
                        }
                    },
                    "rating": 0,
                    "totalRating": 0,
                    "profitRatio": 10,
                    "published": true,
                    "bookAheadOfTime": true,
                    "name": "نادي جديد للمراجعه",
                    "aboutClub": "<p>نادي جديد</p>",
                    "logo": "https://pub.rh.net.sa/diet-market-backend/master/public/data/clubs/181/طريقة-عمل-دجاج-محمر.jpg",
                    "images": [
                        "https://pub.rh.net.sa/diet-market-backend/master/public/data/clubs/181/steak0s.jpg"
                    ],
                    "cover": null,
                    "commercialRegisterImage": null,
                    "branches": [],
                    "package": [],
                    "openToday": {
                        "open": "12:00 AM",
                        "close": "11:00 PM",
                        "day": "sunday",
                        "available": "yes"
                    },
                    "isClosed": false
                },
                "date": {
                    "format": "2022-06-19",
                    "timestamp": 1655589600,
                    "text": "الأحد، ١٩ يونيو ٢٠٢٢ في ١٢:٠٠ ص",
                    "humanTime": "منذ 7 أشهر"
                },
                "dateTime": "الأحد 19 يونيو, 12 ص",
                "statusColor": "cfd4c8",
                "statusName": "في انتظار تأكيد النادى"
            },
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
