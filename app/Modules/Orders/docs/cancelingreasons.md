# CancelingReason API DOCS

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
| /api/admin/cancelingreasons            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/cancelingreasons | GET           |-|  [Response](#Response)         |
|/api/admin/cancelingreasons/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/cancelingreasons/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/cancelingreasons/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/cancelingreasons        |GET           |-| [Response](#Response)|
|/api/cancelingreasons/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new CancelingReason 

```json
{
"notes":"String"
"cancelingReason":"String" 
} 
```

# <a name="Update"> </a> Update CancelingReason

```json
{
"notes":"String"
"cancelingReason":"String" 
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
