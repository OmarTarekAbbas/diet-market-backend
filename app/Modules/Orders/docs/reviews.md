# Review API DOCS
Add a comment on the order
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
|/api/admin/reviews            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
|/api/admin/reviews | GET           |-|  [Response](#Response)         |
|/api/admin/reviews/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/reviews/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/reviews/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/products/reviews        |GET           |-| [Response](#Response)|
|/api/products/reviews/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Review 

```json
{
"orderId":"int"
"productId":"int"
"rate":"int"
"review":"string"
//storeId:24
} 
```

# <a name="Update"> </a> Update Review

```json
{
    "orderId":"int"
"productId":"int"
"rate":"int"
"review":"string"
//storeId:24
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
