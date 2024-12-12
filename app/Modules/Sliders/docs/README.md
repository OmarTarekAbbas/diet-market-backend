# Slider API DOCS

# Base URL
http://hamam-backend.local

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/sliders            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/sliders | GET           |-|  [Response](#Response)         |
|/api/admin/sliders/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/sliders/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/sliders/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/sliders        |GET           |-| [Response](#Response)|
|/api/sliders/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Slider 

```json
{
} 
```

# <a name="Update"> </a> Update Slider

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
