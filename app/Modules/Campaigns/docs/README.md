# Campaign API DOCS
It is about if I wanted to add something to customers about offers or any notification, and it is done for FearBase
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
| /api/admin/campaigns            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/campaigns | GET           |-|  [Response](#Response)         |
|/api/admin/campaigns/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/campaigns/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/campaigns/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/campaigns        |GET           |-| [Response](#Response)|
|/api/campaigns/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Campaign 

```json
{
"title" : "String"
"content" : "String"
"image" : "File"
} 
```

# <a name="Update"> </a> Update Campaign

```json
{
"title" : "String"
"content" : "String"
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
            "success": true,
        },
    ]
}
```
