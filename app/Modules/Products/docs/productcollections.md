# ProductCollection API DOCS

# Base URL
http://hamam-backend.local

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/productcollections            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/productcollections | GET           |-|  [Response](#Response)         |
|/api/admin/productcollections/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/productcollections/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/productcollections/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/productcollections        |GET           |-| [Response](#Response)|
|/api/productcollections/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new ProductCollection 

```json
{
} 
```

# <a name="Update"> </a> Update ProductCollection

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
