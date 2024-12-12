# ShippingCost API DOCS

# Base URL
http://localhost

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/shippingcosts            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/shippingcosts | GET           |-|  [Response](#Response)         |
|/api/admin/shippingcosts/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/shippingcosts/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/shippingcosts/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/shippingcosts        |GET           |-| [Response](#Response)|
|/api/shippingcosts/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new ShippingCost 

```json
{
} 
```

# <a name="Update"> </a> Update ShippingCost

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
