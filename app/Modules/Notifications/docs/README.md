# Notification API DOCS
 Notifications to the customer
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
| /api/admin/notifications            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/notifications | GET           |-|  [Response](#Response)         |
|/api/admin/notifications/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/notifications/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/notifications/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/notifications        |GET           |-| [Response](#Response)|
|/api/notifications/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Notification 

```json
{
"title" : "String"
"content" : "String"
"type" : "String"
"extra" : "String"
"image" : "String"
} 
```

# <a name="Update"> </a> Update Notification

```json
{
"title" : "String"
"content" : "String"
"type" : "String"
"extra" : "String"
"image" : "String"
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
