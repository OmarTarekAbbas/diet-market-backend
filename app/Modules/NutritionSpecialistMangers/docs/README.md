# NutritionSpecialistManger API DOCS
 Add a nutritionist about admin

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
| /api/admin/nutritionspecialistmangers            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/nutritionspecialistmangers | GET           |-|  [Response](#Response)         |
|/api/admin/nutritionspecialistmangers/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/nutritionspecialistmangers/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/nutritionspecialistmangers/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/nutritionspecialistmangers        |GET           |-| [Response](#Response)|
|/api/nutritionspecialistmangers/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new NutritionSpecialistManger 

```json
{
"email":"nutritionspecialistmangers4@gmail.com"
"password":"123456789"
"password_confirmation":"123456789"
"nutritionSpecialist":"15"
"name[0][text]":"DR.OmarTarek4"
"name[0][localeCode]":"en"
"name[1][text]"":د.عمر طارق4"
"name[1][localeCode]":"ar"
"description[0][text]":"description"
"description[0][localeCode]":"en"
"description[1][text]":"description"
"description[1][localeCode]":"ar"
} 
```

# <a name="Update"> </a> Update NutritionSpecialistManger

```json
{
"email":"nutritionspecialistmangers4@gmail.com"
"password":"123456789"
"password_confirmation":"123456789"
"nutritionSpecialist":"15"
"name[0][text]":"DR.OmarTarek4"
"name[0][localeCode]":"en"
"name[1][text]"":د.عمر طارق4"
"name[1][localeCode]":"ar"
"description[0][text]":"description"
"description[0][localeCode]":"en"
"description[1][text]":"description"
"description[1][localeCode]":"ar"
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
