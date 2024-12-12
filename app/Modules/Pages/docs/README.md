# Page API DOCS

# Base URL
http://127.0.0.1:8000

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

LOCALE-CODE : {lang}


# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/pages            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/pages | GET           |-|  [Response](#Response)         |
|/api/admin/pages/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/pages/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/pages/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/pages        |GET           |-| [Response](#Response)|
|/api/pages/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Page 

```json
{
"title" : "String"
"content" : "String"
"name" : "String"
} 
```

# <a name="Update"> </a> Update Page

```json
{
"title" : "String"
"content" : "String"
"name" : "String"
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
