# Restaurant API DOCS
Adding restaurants through the admin
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
| /api/admin/restaurants            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/restaurants | GET           |-|  [Response](#Response)         |
|/api/admin/restaurants/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/restaurants/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/restaurants/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/restaurants        |GET           |-| [Response](#Response)|
|/api/restaurants/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Restaurant 

```json
{
"name":"Misr Studio"
"logoText":"Misr Studio"
"commercialRegisterNumber":"05555526"
"minimumOrders":300
"city":1
"published":1
"delivery":1
"workTimes"[0][day]:"saturday"
"workTimes"[0][available]:"yes"
"workTimes"[0][open]:"8:00 am"
"workTimes"[0][close]:"10:00 pm"
"location"[lat]:30.0753738
"location"[lng]:31.2712153
"location"[address]:"15 شارع عباس ا"
"isBusy":0
"categories"[0]:116
"categories"[1]:117
"categories"[2]:118
"typeOfFoodRestaurant"[0]:2
"typeOfFoodRestaurant"[1]:3
"deliveryValue":29
} 
```

# <a name="Update"> </a> Update Restaurant

```json
{
"name":"Misr Studio"
"logoText":"Misr Studio"
"commercialRegisterNumber":"05555526"
"minimumOrders":300
"city":1
"published":1
"delivery":1
"workTimes"[0][day]:"saturday"
"workTimes"[0][available]:"yes"
"workTimes"[0][open]:"8:00 am"
"workTimes"[0][close]:"10:00 pm"
"location"[lat]:30.0753738
"location"[lng]:31.2712153
"location"[address]:"15 شارع عباس ا"
"isBusy":0
"categories"[0]:116
"categories"[1]:117
"categories"[2]:118
"typeOfFoodRestaurant"[0]:2
"typeOfFoodRestaurant"[1]:3
"deliveryValue":29
} 
```
# <a name="Response"> </a> Responses 

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
