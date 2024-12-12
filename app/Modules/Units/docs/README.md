# Unit API DOCS

# Base URL
http://batee5-backend.local

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/units            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/units | GET           |-|  [Response](#Response)         |
|/api/admin/units/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/units/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/units/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/units        |GET           |-| [Response](#Response)|
|/api/units/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Unit 

```json
{
} 
```

# <a name="Update"> </a> Update Unit

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
