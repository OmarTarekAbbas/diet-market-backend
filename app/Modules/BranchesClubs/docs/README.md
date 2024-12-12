# BranchesClub API DOCS
 We have another model for the clubs, and I am the guest of the club by specifying its branches through array

# Base URL
http://127.0.0.1:8000

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

LOCALE-CODE : {lang}


# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/branchesclubs            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/branchesclubs | GET           |-|  [Response](#Response)         |
|/api/admin/branchesclubs/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/branchesclubs/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/branchesclubs/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/branchesclubs        |GET           |-| [Response](#Response)|
|/api/branchesclubs/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new BranchesClub 

```json
{
"name[0][text]":omarClub
"name[0][localeCode]":en
"name[1][text]:omarClub
name[1][localeCode]:ar
aboutClub[0][text]:Monester  Monester Monester Monester Monester Monester  Monester Monester Monester Monester Monester  Monester Monester Monester Monester Monester  Monester Monester Monester Monester
aboutClub[0][localeCode]:en
aboutClub[1][text]:مونستر مونستر مونستر مونستر مونستر مونستر مونستر مونستر مونستر مونستر مونستر مونستر مونستر مونستر مونستر مونستر مونستر مونستر مونستر مونستر مونستر مونستر مونستر مونستر
aboutClub[1][localeCode]:ar
published:1
commercialRegisterNumber:05555526
isBusy:0
bookAheadOfTime:1
branches[0][location][lat]:30.695545686
branches[0][location][lng]:30.657867878
branches[0][location][address]:bla bla bal bla
branches[0][mainBranch]:0
branches[0][published]:1
branches[0][workTimes][0][day]:saturday
branches[0][workTimes][0][available]:yes
branches[0][workTimes][0][open]:10:00 am
branches[0][workTimes][0][close]:11:00 pm
branches[0][workTimes][1][day]:sunday
branches[0][workTimes][1][available]:yes
branches[0][workTimes][1][open]:10:00
branches[0][workTimes][1][close]:23:00
branches[0][workTimes][2][day]:monday
branches[0][workTimes][2][available]:yes
branches[0][workTimes][2][open]:10:00
branches[0][workTimes][2][close]:23:00
branches[0][workTimes][3][day]:tuesday
branches[0][workTimes][3][available]:yes
branches[0][workTimes][3][open]:13:00
branches[0][workTimes][3][close]:23:00
branches[0][workTimes][4][day]:wednesday
branches[0][workTimes][4][available]:yes
branches[0][workTimes][4][open]:13:00
branches[0][workTimes][4][close]:23:00
branches[0][workTimes][5][day]:thursday
branches[0][workTimes][5][available]:yes
branches[0][workTimes][5][open]:11:00
branches[0][workTimes][5][close]:23:00
branches[0][workTimes][6][day]:friday
branches[0][workTimes][6][available]:no
branches[0][workTimes][6][open]:00:00
branches[0][workTimes][6][close]:00:00
branches[0][mainBranch]:1
package[0][name][0][text]:4ahry
package[0][name][0][localeCode]:en
package[0][name][1][text]:شهري
package[0][name][1][localeCode]:ar
package[0][published]:1
package[0][finalPrice]:320
package[0][rewardPoints]:400
package[0][purchaseRewardPoints]:500
package[0][monthsNumber]:1
branches[0][city]:1
} 
```

# <a name="Update"> </a> Update BranchesClub

```json
{
    "name[0][text]":omarClub
"name[0][localeCode]":en
"name[1][text]:omarClub
name[1][localeCode]:ar
aboutClub[0][text]:Monester  Monester Monester Monester Monester Monester  Monester Monester Monester Monester Monester  Monester Monester Monester Monester Monester  Monester Monester Monester Monester
aboutClub[0][localeCode]:en
aboutClub[1][text]:مونستر مونستر مونستر مونستر مونستر مونستر مونستر مونستر مونستر مونستر مونستر مونستر مونستر مونستر مونستر مونستر مونستر مونستر مونستر مونستر مونستر مونستر مونستر مونستر
aboutClub[1][localeCode]:ar
published:1
commercialRegisterNumber:05555526
isBusy:0
bookAheadOfTime:1
branches[0][location][lat]:30.695545686
branches[0][location][lng]:30.657867878
branches[0][location][address]:bla bla bal bla
branches[0][mainBranch]:0
branches[0][published]:1
branches[0][workTimes][0][day]:saturday
branches[0][workTimes][0][available]:yes
branches[0][workTimes][0][open]:10:00 am
branches[0][workTimes][0][close]:11:00 pm
branches[0][workTimes][1][day]:sunday
branches[0][workTimes][1][available]:yes
branches[0][workTimes][1][open]:10:00
branches[0][workTimes][1][close]:23:00
branches[0][workTimes][2][day]:monday
branches[0][workTimes][2][available]:yes
branches[0][workTimes][2][open]:10:00
branches[0][workTimes][2][close]:23:00
branches[0][workTimes][3][day]:tuesday
branches[0][workTimes][3][available]:yes
branches[0][workTimes][3][open]:13:00
branches[0][workTimes][3][close]:23:00
branches[0][workTimes][4][day]:wednesday
branches[0][workTimes][4][available]:yes
branches[0][workTimes][4][open]:13:00
branches[0][workTimes][4][close]:23:00
branches[0][workTimes][5][day]:thursday
branches[0][workTimes][5][available]:yes
branches[0][workTimes][5][open]:11:00
branches[0][workTimes][5][close]:23:00
branches[0][workTimes][6][day]:friday
branches[0][workTimes][6][available]:no
branches[0][workTimes][6][open]:00:00
branches[0][workTimes][6][close]:00:00
branches[0][mainBranch]:1
package[0][name][0][text]:4ahry
package[0][name][0][localeCode]:en
package[0][name][1][text]:شهري
package[0][name][1][localeCode]:ar
package[0][published]:1
package[0][finalPrice]:320
package[0][rewardPoints]:400
package[0][purchaseRewardPoints]:500
package[0][monthsNumber]:1
branches[0][city]:1
} 
```
# <a name="Response"> 
"record": {
            "id": 179,
            "mainBranchClub": {
                "id": 193,
                "location": {
                    "type": "Point",
                    "coordinates": [
                        30.1418839,
                        31.6285105
                    ],
                    "address": "مدينة الشروق، El Shorouk, Egypt"
                },
                "workTimes": [
                    {
                        "open": "02:00",
                        "close": "10:00",
                        "day": "الأحد",
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
            "commercialRegisterNumber": 123,
            "rating": 1,
            "totalRating": 1,
            "profitRatio": 10,
            "published": true,
            "bookAheadOfTime": true,
            "name": "نادي جديد 1",
            "aboutClub": "<p>نادي جديد 1</p>",
            "logo": "https://pub.rh.net.sa/diet-market-backend/master/public/data/clubs/179/layer-25.png",
            "images": [
                "https://pub.rh.net.sa/diet-market-backend/master/public/data/clubs/179/layer-22.png"
            ],
            "cover": "https://pub.rh.net.sa/diet-market-backend/master/public/data/clubs/179/layer-22.png",
            "commercialRegisterImage": "https://pub.rh.net.sa/diet-market-backend/master/public/data/clubs/179/153-012458-storage-vegetables-fruits_700x400.jpeg",
            "branches": [
                {
                    "id": 193,
                    "location": {
                        "type": "Point",
                        "coordinates": [
                            30.1418839,
                            31.6285105
                        ],
                        "address": "مدينة الشروق، El Shorouk, Egypt"
                    },
                    "workTimes": [
                        {
                            "open": "02:00",
                            "close": "10:00",
                            "day": "الأحد",
                            "available": "yes"
                        }
                    ],
                    "published": true,
                    "mainBranch": true,
                    "city": {
                        "id": 33,
                        "name": "Adena Gillespie"
                    }
                }
            ],
            "package": [
                {
                    "id": 171,
                    "rewardPoints": 10,
                    "purchaseRewardPoints": 10,
                    "monthsNumber": 1,
                    "finalPrice": 10,
                    "published": true,
                    "name": "باقة جديدة 1",
                    "finalPriceText": "10 ر.س"
                },
                {
                    "id": 174,
                    "rewardPoints": 10,
                    "purchaseRewardPoints": 10,
                    "monthsNumber": 1,
                    "finalPrice": 30,
                    "published": false,
                    "name": "شهري",
                    "finalPriceText": "30 ر.س"
                }
            ],
            "openToday": {
                "open": "02:00",
                "close": "10:00",
                "day": "sunday",
                "available": "yes"
            },
            "isClosed": true
        },
</a> Responses 

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
