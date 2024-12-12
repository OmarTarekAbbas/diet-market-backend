# Subscription API DOCS

# Base URL
http://batee5-backend.local

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/subscriptions            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/subscriptions | GET           |-|  [Response](#Response)         |
|/api/admin/subscriptions/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/subscriptions/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/subscriptions/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/subscriptions        |GET           |-| [Response](#Response)|
|/api/subscriptions/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Subscription 

```json
{
} 
```

# <a name="Update"> </a> Update Subscription

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
