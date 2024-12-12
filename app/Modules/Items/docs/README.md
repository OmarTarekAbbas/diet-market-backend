# Item API DOCS

# Base URL
http://localhost

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/items            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/items | GET           |-|  [Response](#Response)         |
|/api/admin/items/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/items/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/items/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/items        |GET           |-| [Response](#Response)|
|/api/items/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Item 

```json
{
} 
```

# <a name="Update"> </a> Update Item

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
