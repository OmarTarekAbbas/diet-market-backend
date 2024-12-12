# Commission API DOCS

# Base URL
http://localhost

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/commissions            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/commissions | GET           |-|  [Response](#Response)         |
|/api/admin/commissions/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/commissions/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/commissions/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/commissions        |GET           |-| [Response](#Response)|
|/api/commissions/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Commission 

```json
{
} 
```

# <a name="Update"> </a> Update Commission

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
