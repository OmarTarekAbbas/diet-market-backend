# ContactU API DOCS
 It is about if the customer wants to make a complaint or make a suggestion or if he has a question
# Base URL
http://localhost

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

LOCALE-CODE : {lang}

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/contactus            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/contactus | GET           |-|  [Response](#Response)         |
|/api/admin/contactus/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/contactus/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/contactus/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/contactus        |GET           |-| [Response](#Response)|
|/api/contactus/{id}        |GET           |-|[Response](#Response)|
|/api/info/contact-us/submit       |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new ContactU 

```json
{
"firstName" : "String"
"lastName" : "String"
"email" : "String"
"subject" : "String"
"message" : "String"
"type" : "int" //1=complaint , 2=inquiry , 3=suggestion
"department" : "String" // restaurants , nutrition , products , clubs
} 
```

# <a name="Update"> </a> Update ContactU

```json
{
"firstName" : "String"
"lastName" : "String"
"email" : "String"
"subject" : "String"
"message" : "String"
"type" : "int" //1=complaint , 2=inquiry , 3=suggestion
"department" : "String" // restaurants , nutrition , products , clubs
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
