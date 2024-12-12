# NutritionSpecialist API DOCS
 Add a nutritionist
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
| /api/admin/nutritionspecialists            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/nutritionspecialists | GET           |-|  [Response](#Response)         |
|/api/admin/nutritionspecialists/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/nutritionspecialists/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/nutritionspecialists/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/nutritionspecialists        |GET           |-| [Response](#Response)|
|/api/nutritionspecialists/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new NutritionSpecialist 

```json
{
"name":"DR.OmarTarek 4"
"commercialRegisterNumber":"05555526"
"city":"1"
"published":"1"
"location[lat]":"31.2712153"
"location[lng]":"31.2712153"
"location[address]":"15 شارع عباس العقاد"
"workTimes[0][day]":"saturday"
"workTimes[0][available]":"yes"
"workTimes[0][open]":"10:00"
"workTimes[0][close]":"23:00"
"finalPrice":"400"
"rewardPoints":"300"
"purchaseRewardPoints":"900"
"profitRatio":"1.5"
} 
```

# <a name="Update"> </a> Update NutritionSpecialist

```json
{
    "name":"DR.OmarTarek 4"
"commercialRegisterNumber":"05555526"
"city":"1"
"published":"1"
"location[lat]":"31.2712153"
"location[lng]":"31.2712153"
"location[address]":"15 شارع عباس العقاد"
"workTimes[0][day]":"saturday"
"workTimes[0][available]":"yes"
"workTimes[0][open]":"10:00"
"workTimes[0][close]":"23:00"
"finalPrice":"400"
"rewardPoints":"300"
"purchaseRewardPoints":"900"
"profitRatio":"1.5"
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
