# DietType API DOCS
 Types of diet
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
| /api/admin/diet-types            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/diet-types | GET           |-|  [Response](#Response)         |
|/api/admin/diet-types/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/diet-types/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/diet-types/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/diet-types        |GET           |-| [Response](#Response)|
|/api/diet-types/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new DietType 

```json
{
"name":"string"
"proteinRatio":"int"
"carbohydrateRatio":"int"
"fatRatio":"int"
"published":"bool"
"description":"string"

} 
```

# <a name="Update"> </a> Update DietType

```json
{
"name":"string"
"proteinRatio":"int"
"carbohydrateRatio":"int"
"fatRatio":"int"
"published":"bool"
"description":"string"
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
