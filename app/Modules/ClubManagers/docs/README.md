# ClubManager API DOCS
It is a club manager for every club that must have a manager
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
|/api/admin/clubmanagers            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
|/api/admin/clubmanagers | GET           |-|  [Response](#Response)         |
|/api/admin/clubmanagers/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/clubmanagers/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/clubmanagers/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/clubmanagers        |GET           |-| [Response](#Response)|
|/api/clubmanagers/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new ClubManager 

```json
{
"name":"string"
"email":"string"
"password":"string"
"club":"Int"
} 
```

# <a name="Update"> </a> Update ClubManager

```json
{
"name":"string"
"email":"string"
"password":"string"
"club":"Int"
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
