# ProductReview API DOCS

# Base URL
http://batee5-backend.local

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/productreviews            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/productreviews | GET           |-|  [Response](#Response)         |
|/api/admin/productreviews/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/productreviews/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/productreviews/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/productreviews        |GET           |-| [Response](#Response)|
|/api/productreviews/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new ProductReview 

```json
{
} 
```

# <a name="Update"> </a> Update ProductReview

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
