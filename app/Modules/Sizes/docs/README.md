# Size API DOCS

# Base URL
http://localhost

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/sizes            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/sizes | GET           |-|  [Response](#Response)         |
|/api/admin/sizes/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/sizes/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/sizes/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/sizes        |GET           |-| [Response](#Response)|
|/api/sizes/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Size 

```json
{
} 
```

# <a name="Update"> </a> Update Size

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
