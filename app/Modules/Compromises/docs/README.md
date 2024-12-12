# Compromise API DOCS

# Base URL
http://localhost

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/compromises            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/compromises | GET           |-|  [Response](#Response)         |
|/api/admin/compromises/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/compromises/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/compromises/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/compromises        |GET           |-| [Response](#Response)|
|/api/compromises/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Compromise 

```json
{
} 
```

# <a name="Update"> </a> Update Compromise

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
