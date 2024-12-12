# Region API DOCS

# Base URL
http://localhost

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/regions            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/regions | GET           |-|  [Response](#Response)         |
|/api/admin/regions/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/regions/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/regions/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/regions        |GET           |-| [Response](#Response)|
|/api/regions/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Region 

```json
{
"name" : "String"
"shippingFees" : "Double"
} 
```

# <a name="Update"> </a> Update Region

```json
{
"name" : "String"
"shippingFees" : "Double"
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
