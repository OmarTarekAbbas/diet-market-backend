# Test API DOCS

# Base URL
http://hamam-backend.test

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/tests            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/tests | GET           |-|  [Response](#Response)         |
|/api/admin/tests/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/tests/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/tests/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/tests        |GET           |-| [Response](#Response)|
|/api/tests/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Test 

```json
{
} 
```

# <a name="Update"> </a> Update Test

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
