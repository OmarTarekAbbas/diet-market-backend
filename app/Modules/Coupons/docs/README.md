# Coupon API DOCS
 It is about adding a discount coupon that you can use in the shopping cart
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
| /api/admin/coupons            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/coupons | GET           |-|  [Response](#Response)         |
|/api/admin/coupons/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/coupons/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/coupons/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/coupons        |GET           |-| [Response](#Response)|
|/api/coupons/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Coupon 

```json
{
"code":"testTest22"
"type":"percentage"
"value":"10.50"
"maxUsage":"100"
"minOrderValue":"10"
"startsAt":"2021-12-22"
"endsAt":"2022-12-30"
"published":"1"
"typeCoupon":"food" //products/food
} 
```

# <a name="Update"> </a> Update Coupon

```json
{
"code":"testTest22"
"type":"percentage"
"value":"10.50"
"maxUsage":"100"
"minOrderValue":"10"
"startsAt":"2021-12-22"
"endsAt":"2022-12-30"
"published":"1"
"typeCoupon":"food" //products/food
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
