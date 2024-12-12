# DeliveryMan API DOCS

# Base URL
http://127.0.0.1:8000

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/deliverymen            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/deliverymen | GET           |-|  [Response](#Response)         |
|/api/admin/deliverymen/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/deliverymen/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/deliverymen/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/deliverymen        |GET           |-| [Response](#Response)|
|/api/deliverymen/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new DeliveryMan 

```json
{
"name" : "String"
"phone" : "String"
"email" : "String"
"password" : "String"
"image" : "File"
} 
```

# <a name="Update"> </a> Update DeliveryMan

```json
{
"name" : "String"
"phone" : "String"
"email" : "String"
"password" : "String"
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
