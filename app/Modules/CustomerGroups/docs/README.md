# CustomerGroup API DOCS
 It is a group, and I specify discounts for them when I add a customer or make an amendment to a customer, specifying that he follows. I end a group in order to make discounts for him
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
| /api/admin/customergroups            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/customergroups | GET           |-|  [Response](#Response)         |
|/api/admin/customergroups/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/customergroups/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/customergroups/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/customergroups        |GET           |-| [Response](#Response)|
|/api/customergroups/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new CustomerGroup 

```json
{
"name" ["array"]:"string"
"conditionType":"string"
"conditionValue":"int"
"specialDiscount":"int"
"freeShipping":"int"
"freeExpressShipping":"int"
"published":"Bool"
"nameGroup":"string"
} 
```

# <a name="Update"> </a> Update CustomerGroup

```json
{
"name" ["array"]:"string"
"conditionType":"string"
"conditionValue":"int"
"specialDiscount":"int"
"freeShipping":"int"
"freeExpressShipping":"int"
"published":"Bool"
"nameGroup":"string"
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
