# Category API DOCS
    It is about sections that follow the products and meals, and a type of products or meals is added to them
# Base URL
http://localhost/

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

LOCALE-CODE : {lang}
# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/categories            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/categories | GET           |-|  [Response](#Response)         |
|/api/admin/categories/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/categories/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/categories/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/categories        |GET           |-| [Response](#Response)|
|/api/categories/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Category 

```json
{
"name" ["array"]: "String"
"description" ["array"]: "String"
"type" : "string" // products Or food
"image" : "File"
"published" : "Bool"
} 
```

# <a name="Update"> </a> Update Category

```json
{
"name" ["array"]: "String"
"description" ["array"]: "String"
"type" : "string" // products Or food
"image" : "File"
"published" : "Bool"
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
