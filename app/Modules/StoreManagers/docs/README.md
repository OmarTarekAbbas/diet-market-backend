# StoreManager API DOCS
It's about the site's merchants being added by the admin
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
| /api/admin/store-managers            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/store-managers | GET           |-|  [Response](#Response)         |
|/api/admin/store-managers/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/store-managers/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/store-managers/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/store-managers        |GET           |-| [Response](#Response)|
|/api/store-managers/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new StoreManager 

```json
{
"firstName":"String"
"lastName":"String"
"email":"String"
"password":"String"
"password_confirmation":"String"
"phoneNumber":"String"
"store":"int"
"country":"int"
"city":"int"
} 
```

# <a name="Update"> </a> Update StoreManager

```json
{
"firstName":"String"
"lastName":"String"
"email":"String"
"password":"String"
"password_confirmation":"String"
"phoneNumber":"String"
"store":"int"
"country":"int"
"city":"int"
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
