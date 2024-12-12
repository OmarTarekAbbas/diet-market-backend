# TypeContactU API DOCS

# Base URL
http://localhost/api

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/typecontactus            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/typecontactus | GET           |-|  [Response](#Response)         |
|/api/admin/typecontactus/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/typecontactus/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/typecontactus/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/typecontactus        |GET           |-| [Response](#Response)|
|/api/typecontactus/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new TypeContactU 

```json
{
} 
```

# <a name="Update"> </a> Update TypeContactU

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
