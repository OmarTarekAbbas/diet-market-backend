# General API DOCS

# Base URL
http://hamam-backend.test

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/generals            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/generals | GET           |-|  [Response](#Response)         |
|/api/admin/generals/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/generals/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/generals/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/generals        |GET           |-| [Response](#Response)|
|/api/generals/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new General 

```json
{
} 
```

# <a name="Update"> </a> Update General

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
