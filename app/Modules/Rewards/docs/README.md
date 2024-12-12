# Reward API DOCS
It is about reward points that are added by the admin, and it means that if the customer buys a product, he will receive points
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
| /api/admin/rewards            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/rewards | GET           |-|  [Response](#Response)         |
|/api/admin/rewards/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/rewards/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/rewards/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/rewards        |GET           |-| [Response](#Response)|
|/api/rewards/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Reward 

```json
{
} 
```

# <a name="Update"> </a> Update Reward

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
