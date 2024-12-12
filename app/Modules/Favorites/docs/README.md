# Favorite API DOCS

# Base URL
http://127.0.0.1:8000

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/favorites            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/favorites | GET           |-|  [Response](#Response)         |
|/api/admin/favorites/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/favorites/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/favorites/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/favorites        |GET           |-| [Response](#Response)|
|/api/favorites/{id}        |GET           |-|[Response](#Response)|
|/api/addToFavorites      |GET           |-|[Response](#Response)|
|/api/removeFromFavorites      |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Favorite 

```json
{
    "productId":"int"
} 
```

# <a name="Update"> </a> Update Favorite

```json
{
        "productId":"int"
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
            "success": true,
        },
    ]
}
```