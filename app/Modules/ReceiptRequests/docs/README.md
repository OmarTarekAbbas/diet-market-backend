# ReceiptRequest API DOCS

# Base URL
http://localhost

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/receiptrequests            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/receiptrequests | GET           |-|  [Response](#Response)         |
|/api/admin/receiptrequests/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/receiptrequests/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/receiptrequests/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/receiptrequests        |GET           |-| [Response](#Response)|
|/api/receiptrequests/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new ReceiptRequest 

```json
{
} 
```

# <a name="Update"> </a> Update ReceiptRequest

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
