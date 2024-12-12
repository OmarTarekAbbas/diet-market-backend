# Meal API DOCS
 Adding meals through the admin or the restaurant manager
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
| /api/admin/meals            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/meals | GET           |-|  [Response](#Response)         |
|/api/admin/meals/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/meals/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/meals/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/meals        |GET           |-| [Response](#Response)|
|/api/meals/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Meal 

```json
{
} 
```

# <a name="Update"> </a> Update Meal

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
