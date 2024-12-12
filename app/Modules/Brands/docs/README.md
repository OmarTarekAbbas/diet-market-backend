# Brand API DOCS
It is the trademarks of the merchants, and we use them in the products, we determine whether he has the trademarks or not
# Base URL
http://localhost/

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

LOCALE-CODE : {lang}

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/brands            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/brands | GET           |-|  [Response](#Response)         |
|/api/admin/brands/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/brands/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/brands/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/brands        |GET           |-| [Response](#Response)|
|/api/brands/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Brand 

```json
{
"name" : "Array"
"logo" : "File"
"published" : "String"
} 
```

# <a name="Update"> </a> Update Brand

```json
{
"name" : "Array"
"logo" : "File"
"published" : "String"
} 
```
# <a name="Response">

 {
                "id": 21,
                "name": [
                    {
                        "localeCode": "ar",
                        "text": "ماركة 16"
                    },
                    {
                        "localeCode": "en",
                        "text": "ماركة 13"
                    }
                ],
                "published": true,
                "logo": "https://pub.rh.net.sa/diet-market-backend/master/public/data/brands/21/153-012458-storage-vegetables-fruits_700x400.jpeg"
},
# </a> Responses 

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
