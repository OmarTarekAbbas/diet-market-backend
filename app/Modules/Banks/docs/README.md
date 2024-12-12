# Bank API DOCS

# Base URL
http://hamam-backend.local

# Other resources
[BankTransfer](./banktransfer.md) 

 
# Headers

Authorization: key your token

Accept : application/json

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/banks            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/banks | GET           |-|  [Response](#Response)         |
|/api/admin/banks/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/banks/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/banks/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/banks        |GET           |-| [Response](#Response)|
|/api/banks/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Bank 

```json
{
} 
```

# <a name="Update"> </a> Update Bank

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
