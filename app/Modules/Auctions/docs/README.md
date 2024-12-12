# Auction API DOCS

# Base URL
http://localhost

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/auctions            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/auctions | GET           |-|  [Response](#Response)         |
|/api/admin/auctions/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/auctions/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/auctions/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/auctions        |GET           |-| [Response](#Response)|
|/api/auctions/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Auction 

```json
{
} 
```

# <a name="Update"> </a> Update Auction

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
