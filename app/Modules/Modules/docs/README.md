# Module API DOCS

# Base URL
http://batee5-backend.local

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/modules            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/modules | GET           |-|  [Response](#Response)         |
|/api/admin/modules/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/modules/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/modules/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/modules        |GET           |-| [Response](#Response)|
|/api/modules/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Module 

```json
{
} 
```

# <a name="Update"> </a> Update Module

```json
{
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

        },
    ]
}
```
