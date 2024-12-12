# Store API DOCS
Adding a store via admin
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
| /api/admin/stores            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/stores | GET           |-|  [Response](#Response)         |
|/api/admin/stores/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/stores/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/stores/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/stores        |GET           |-| [Response](#Response)|
|/api/stores/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Store 

```json
{
"name":"string"
"description":"string"
"commercialRecordId":"string"
"location"[lat]:"30.695"
"location"[lng]:"30.650"
"location"[address]:"اخر الشارع"
"commercialRecordImage":"file"
"commercialRecordImage":"file"
} 
```

# <a name="Update"> </a> Update Store

```json
{
"name":"string"
"description":"string"
"commercialRecordId":"string"
"location"[lat]:"30.695"
"location"[lng]:"30.650"
"location"[address]:"اخر الشارع"
"commercialRecordImage":"file"
"commercialRecordImage":"file"
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
