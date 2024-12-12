# RestaurantManager API DOCS
Adding a manager to restaurants through the admin
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
| /api/admin/restaurantmanager            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/restaurantmanager | GET           |-|  [Response](#Response)         |
|/api/admin/restaurantmanager/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/restaurantmanager/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/restaurantmanager/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/restaurantmanager        |GET           |-| [Response](#Response)|
|/api/restaurantmanager/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new RestaurantManager 

```json
{
"name":"String"
"email":"String"
"password":"String"
"restaurants":"int" // list restaurants
} 
```

# <a name="Update"> </a> Update RestaurantManager

```json
{
"name":"String"
"email":"String"
"password":"String"
"restaurants":"int" // list restaurants
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
