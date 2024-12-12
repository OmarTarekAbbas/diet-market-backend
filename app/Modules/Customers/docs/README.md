# Customer API DOCS
 add Customer
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
| /api/admin/customers            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/customers | GET           |-|  [Response](#Response)         |
|/api/admin/customers/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/customers/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/customers/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/customers        |GET           |-| [Response](#Response)|
|/api/customers/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Customer 

```json
{
"firstName":"12312311"
"email":"email@email.com"
"phoneNumber":"966507940308"
"lastName":"ttttt"
"healthInfo"["length"]:"152"
"healthInfo"["weight"]:"95"
"healthInfo"["age"]:"25"
"healthInfo"["gender"]:"meal"
"healthInfo"["fatRatio"]:"12"
"healthInfo"["targetWeight"]:"70"
"dietType":"0"
} 
```

# <a name="Update"> </a> Update Customer

```json
{
"firstName":"12312311"
"email":"email@email.com"
"phoneNumber":"966507940308"
"lastName":"ttttt"
"healthInfo"["length"]:"152"
"healthInfo"["weight"]:"95"
"healthInfo"["age"]:"25"
"healthInfo"["gender"]:"meal"
"healthInfo"["fatRatio"]:"12"
"healthInfo"["targetWeight"]:"70"
"dietType":"0"
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
