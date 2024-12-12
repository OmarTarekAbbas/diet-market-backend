# Tax API DOCS

# Base URL
http://localhost

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/taxes            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/taxes | GET           |-|  [Response](#Response)         |
|/api/admin/taxes/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/taxes/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/taxes/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/taxes        |GET           |-| [Response](#Response)|
|/api/taxes/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Tax 

```json
{
"title" : "String"
"description" : "String"
"image" : "File"
} 
```

# <a name="Update"> </a> Update Tax

```json
{
"title" : "String"
"description" : "String"
"image" : "File"
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
