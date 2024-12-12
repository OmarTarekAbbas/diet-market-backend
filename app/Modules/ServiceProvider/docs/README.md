# ServiceProvider API DOCS
It is about registering the service provider and the admin agrees or refuses, and in any case he sends a message to the service provider
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
| /api/admin/serviceproviders            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/serviceproviders | GET           |-|  [Response](#Response)         |
|/api/admin/serviceproviders/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/serviceproviders/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/serviceproviders/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/serviceproviders        |GET           |-| [Response](#Response)|
|/api/serviceproviders/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new ServiceProvider 

```json
{
"tradeName":"tradeName"
"country":1
"city":1
"serviceType":1
"address":"12 Boutros Ghaly Street, Heliopolis"
"commercialNumber":12345678
"commercialImage":"file"
"published":1
"firstName":"Omar"
"lastName":"Tarek"
"email":"o.tarek@rowaad.net"
"phoneNumber":"01144944808"
"type":"restaurant" //restaurant/club/products/
} 
```

# <a name="Update"> </a> Update ServiceProvider

```json
{
"tradeName":"tradeName"
"country":1
"city":1
"serviceType":1
"address":"12 Boutros Ghaly Street, Heliopolis"
"commercialNumber":12345678
"commercialImage":"file"
"published":1
"firstName":"Omar"
"lastName":"Tarek"
"email":"o.tarek@rowaad.net"
"phoneNumber":"01144944808"
"type":"restaurant" //restaurant/club/products/
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
