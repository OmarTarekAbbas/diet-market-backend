# Complaint API DOCS
 It is about if the customer wants to make a complaint

# Base URL
http://localhost

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/complaints            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/complaints | GET           |-|  [Response](#Response)         |
|/api/admin/complaints/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/complaints/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/complaints/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/complaints        |GET           |-| [Response](#Response)|
|/api/complaints/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Complaint 

```json
{
} 
```

# <a name="Update"> </a> Update Complaint

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
