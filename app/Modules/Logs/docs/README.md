# Log API DOCS

# Base URL
http://localhost

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/logs            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/logs | GET           |-|  [Response](#Response)         |
|/api/admin/logs/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/logs/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/logs/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/logs        |GET           |-| [Response](#Response)|
|/api/logs/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Log 

```json
{
} 
```

# <a name="Update"> </a> Update Log

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
