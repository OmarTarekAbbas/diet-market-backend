# VehicleType API DOCS
Adding a vehicle type through the admin
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
| /api/admin/vehicletypes            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/vehicletypes | GET           |-|  [Response](#Response)         |
|/api/admin/vehicletypes/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/vehicletypes/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/vehicletypes/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/vehicletypes        |GET           |-| [Response](#Response)|
|/api/vehicletypes/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new VehicleType 

```json
{
    "name":"String"
    "published":"Bool"
} 
```

# <a name="Update"> </a> Update VehicleType

```json
{
    "name":"String"
    "published":"Bool"
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
            "success": true,
        },
    ]
}
```
