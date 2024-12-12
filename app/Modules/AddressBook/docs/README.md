# AddressBook API DOCS

It is about adding delivery addresses to the customer in products and restaurants

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
| /api/admin/addressbooks            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/addressbooks | GET           |-|  [Response](#Response)         |
|/api/admin/addressbooks/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/addressbooks/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/addressbooks/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/addressbooks        |GET           |-| [Response](#Response)|
|/api/addressbooks/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new AddressBook 

```json
{
"firstName" : "String"
"lastName" : "String"
"email" : "String"
"phoneNumber" : "String"
"location" : "array"
"address" : "String"
"buildingNumber" : "String"
"flatNumber" : "String"
"floorNumber" : "String"
"specialMark" : "String"
"district" : "String"
"type" : "String"
"isPrimary" : "Bool"
"city" : "int"
} 
```

# <a name="Update"> </a> Update AddressBook

```json
{
"firstName" : "String"
"lastName" : "String"
"email" : "String"
"phoneNumber" : "String"
"location" : "array"
"address" : "String"
"buildingNumber" : "String"
"flatNumber" : "String"
"floorNumber" : "String"
"specialMark" : "String"
"district" : "String"
"type" : "String"
"isPrimary" : "Bool"
"city" : "int"
} 
```
# <a name="Response"> </a> Responses 
{
       [
        "id": 11,
                "firstName": "omar",
                "lastName": "tarek",
                "email": "omart8703@gmail.com",
                "address": "حارة الشيشينى بين الجناين الوايلى مصر",
                "phoneNumber": "966550123123",
                "buildingNumber": null,
                "flatNumber": null,
                "verificationCode": null,
                "floorNumber": null,
                "type": null,
                "specialMark": "حارة الشيشينى",
                "isPrimary": true,
                "verified": true,
                "district": "بين الجناين",
                "location": {
                    "type": "Point",
                    "coordinates": [
                        30.61486834855349,
                        32.29499816894531
                    ],
                    "address": "حارة الشيشينى بين الجناين الوايلى مصر"
                },
                "country": null,
                "city": {
                    "id": 1,
                    "name": [
                        {
                            "localeCode": "ar",
                            "text": "الرياض"
                        },
                        {
                            "localeCode": "en",
                            "text": "Riyadh"
                        }
                    ]
                }
       ]         
            
}
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
