# SubscriptionMeal API DOCS

# Base URL
http://localhost

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/subscriptionmeals            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/subscriptionmeals | GET           |-|  [Response](#Response)         |
|/api/admin/subscriptionmeals/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/subscriptionmeals/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/subscriptionmeals/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/subscriptionmeals        |GET           |-| [Response](#Response)|
|/api/subscriptionmeals/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new SubscriptionMeal 

```json
{
} 
```

# <a name="Update"> </a> Update SubscriptionMeal

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
