# Guest API DOCS

# Base URL
http://localhost/api

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/guests            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/guests | GET           |-|  [Response](#Response)         |
|/api/admin/guests/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/guests/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/guests/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/guests        |GET           |-| [Response](#Response)|
|/api/guests/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Guest 

```json
{
} 
```

# <a name="Update"> </a> Update Guest

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
