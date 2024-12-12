# HealthyDatum API DOCS

# Base URL
http://127.0.0.1:8000

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/healthy{
}            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/healthy{
} | GET           |-|  [Response](#Response)         |
|/api/admin/healthy{
}/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/healthy{
}/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/healthy{
}/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/healthy{
}        |GET           |-| [Response](#Response)|
|/api/healthy{
}/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new HealthyDatum 

```json
{
"healthInfo[length]":"int"
"healthInfo[weight]":"int"
"healthInfo[age]":"int"
"healthInfo[gender]":"string"
"healthInfo[fatRatio]":"int"
"healthInfo[targetWeight]":"int"
"dietTypes":"int"
"type":"string" 
"specialDiet[fat]":"int"
"specialDiet[protein]":"int"
"specialDiet[carbohydrates]":"int"
"specialDiet[calories]":"int"
"customerId":"int"
} 
```

# <a name="Update"> </a> Update HealthyDatum

```json
{
    "healthInfo[length]":"int"
"healthInfo[weight]":"int"
"healthInfo[age]":"int"
"healthInfo[gender]":"string"
"healthInfo[fatRatio]":"int"
"healthInfo[targetWeight]":"int"
"dietTypes":"int"
"type":"string" 
"specialDiet[fat]":"int"
"specialDiet[protein]":"int"
"specialDiet[carbohydrates]":"int"
"specialDiet[calories]":"int"
"customerId":"int"
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
