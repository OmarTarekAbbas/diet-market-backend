# ReturningReason API DOCS
 Reasons for returning the product
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
| /api/admin/returning-reasons            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/returning-reasons | GET           |-|  [Response](#Response)         |
|/api/admin/returning-reasons/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/returning-reasons/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/returning-reasons/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/returning-reasons        |GET           |-| [Response](#Response)|
|/api/returning-reasons/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new ReturningReason 

```json
{
"reason":"String"
"type":"String" //products/food
} 
```

# <a name="Update"> </a> Update ReturningReason

```json
{
"reason":"String"
"type":"String" //products/food
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
