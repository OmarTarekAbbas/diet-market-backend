# Wallet API DOCS
Add balance to the wallet
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
| /api/admin/wallets            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/wallets | GET           |-|  [Response](#Response)         |
|/api/admin/wallets/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/wallets/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/wallets/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/wallets        |GET           |-| [Response](#Response)|
|/api/wallets/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Wallet 

```json
{
"notes" : "String"
"title" : "String"
"transactionType" : "String"
"reason" : "String"
"amount" : "Double"
"orderId" : "Int"
} 
```

# <a name="Update"> </a> Update Wallet

```json
{
"notes" : "String"
"title" : "String"
"transactionType" : "String"
"reason" : "String"
"amount" : "Double"
"orderId" : "Int"
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
