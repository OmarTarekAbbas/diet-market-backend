# Option API DOCS

# Base URL
http://localhost

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/options            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/options | GET           |-|  [Response](#Response)         |
|/api/admin/options/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/options/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/options/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/options        |GET           |-| [Response](#Response)|
|/api/options/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Option 

```json
{
} 
```

# <a name="Update"> </a> Update Option

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
