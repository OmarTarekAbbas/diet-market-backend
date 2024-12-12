# Transaction API DOCS
It is a transaction for service providers
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
| /api/admin/transactions            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/transactions | GET           |-|  [Response](#Response)         |
|/api/admin/transactions/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/transactions/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/transactions/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/transactions        |GET           |-| [Response](#Response)|
|/api/transactions/{id}        |GET           |-|[Response](#Response)|


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
