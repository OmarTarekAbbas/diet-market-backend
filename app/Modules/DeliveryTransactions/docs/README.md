# DeliveryTransaction API DOCS
 Transactions of delegates# Base URL

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
| /api/admin/deliverytransactions            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/deliverytransactions | GET           |-|  [Response](#Response)         |
|/api/admin/deliverytransactions/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/deliverytransactions/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/deliverytransactions/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/deliverytransactions        |GET           |-| [Response](#Response)|
|/api/deliverytransactions/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new DeliveryTransaction 

```json
{
} 
```

# <a name="Update"> </a> Update DeliveryTransaction

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
             "success": true,
        },
    ]
}
```
