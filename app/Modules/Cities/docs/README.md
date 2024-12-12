# City API DOCS
 Add cities
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
| /api/admin/cities            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/cities | GET           |-|  [Response](#Response)         |
|/api/admin/cities/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/cities/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/cities/{id}        |DELETE           |  -|[Response](#Response)| 

|/api/cities        |GET           |-| [Response](#Response)|
|/api/cities/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new City 

```json
{
"name" ["array"]: "String"
"country" : "Int"
"published" : "Bool"
} 
```

# <a name="Update"> </a> Update City

```json
{
"name" ["array"]: "String"
"country" : "Int"
"published" : "Bool"
} 
```
# <a name="Response"> 
  {
                "id": 20,
                "name": [
                    {
                        "localeCode": "ar",
                        "text": "مدينة15"
                    },
                    {
                        "localeCode": "en",
                        "text": "مدينة"
                    }
                ],
                "published": true,
                "country": {
                    "id": 2,
                    "name": [
                        {
                            "localeCode": "ar",
                            "text": "test"
                        },
                        {
                            "localeCode": "en",
                            "text": "test"
                        }
                    ],
                    "published": null
                }
            }
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
