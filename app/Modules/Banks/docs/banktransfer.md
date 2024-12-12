# BankTransfer API DOCS

# Base URL
http://hamam-backend.local

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/banktransfers            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/banktransfers | GET           |-|  [Response](#Response)         |
|/api/admin/banktransfers/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/banktransfers/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/banktransfers/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/banktransfers        |GET           |-| [Response](#Response)|
|/api/banktransfers/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new BankTransfer 

```json
{
} 
```

# <a name="Update"> </a> Update BankTransfer

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
