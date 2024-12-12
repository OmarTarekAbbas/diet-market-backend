# Banner API DOCS

# Base URL
http://hamam-backend.local

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/banners            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/banners | GET           |-|  [Response](#Response)         |
|/api/admin/banners/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/banners/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/banners/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/banners        |GET           |-| [Response](#Response)|
|/api/banners/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Banner 

```json
{
} 
```

# <a name="Update"> </a> Update Banner

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
