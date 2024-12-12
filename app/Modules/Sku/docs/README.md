# Sku API DOCS
It is the product model number added by the admin
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
| /api/admin/skus            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/skus | GET           |-|  [Response](#Response)         |
|/api/admin/skus/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/skus/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/skus/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/skus        |GET           |-| [Response](#Response)|
|/api/skus/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Sku 

```json
{
"name":"String"
"published":"bool"
} 
```

# <a name="Update"> </a> Update Sku

```json
{
"name":"String"
"published":"bool"
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
