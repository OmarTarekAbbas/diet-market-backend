# Country API DOCS
 Add Country
# Base URL
http://127.0.0.1:8000

# Other resources 

 
# Headers

Authorization: key your token

Authorization: key your token

Accept : application/json

LOCALE-CODE : {lang}

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/countries            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/countries | GET           |-|  [Response](#Response)         |
|/api/admin/countries/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/countries/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/countries/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/countries        |GET           |-| [Response](#Response)|
|/api/countries/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Country 

```json
{
"name" : "String"
} 
```

# <a name="Update"> </a> Update Country

```json
{
"name" : "String"
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
