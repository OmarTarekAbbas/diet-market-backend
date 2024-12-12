# TypeOfFoodRestaurant API DOCS
Add the type of restaurant, such as a fast food restaurant or a healthy food restaurant, and so on, and it will be added by the admin and the restaurant manager
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
| /api/admin/typeoffoodrestaurants            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/typeoffoodrestaurants | GET           |-|  [Response](#Response)         |
|/api/admin/typeoffoodrestaurants/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/typeoffoodrestaurants/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/typeoffoodrestaurants/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/typeoffoodrestaurants        |GET           |-| [Response](#Response)|
|/api/typeoffoodrestaurants/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new TypeOfFoodRestaurant 

```json
{
    "name":"String"
    "published":"Bool"
} 
```

# <a name="Update"> </a> Update TypeOfFoodRestaurant

```json
{
    "name":"String"
    "published":"Bool"
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
