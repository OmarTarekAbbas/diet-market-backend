# Section API DOCS

# Base URL
http://localhost

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/sections            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/sections | GET           |-|  [Response](#Response)         |
|/api/admin/sections/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/sections/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/sections/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/sections        |GET           |-| [Response](#Response)|
|/api/sections/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Section 

```json
{
} 
```

# <a name="Update"> </a> Update Section

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
