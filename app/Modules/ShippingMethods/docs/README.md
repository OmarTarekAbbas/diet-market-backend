# ShippingMethod API DOCS
Add shipping methods, price, city to city and duration
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
| /api/admin/shippingmethods            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/shippingmethods | GET           |-|  [Response](#Response)         |
|/api/admin/shippingmethods/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/shippingmethods/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/shippingmethods/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/shippingmethods        |GET           |-| [Response](#Response)|
|/api/shippingmethods/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new ShippingMethod 

```json
{
"name":"String"
"cities"[0][city]:"int"
"cities"[0][shippingFees]:"int"
"cities"[0][expectedDeliveryIn]:"String" //2-3
"type":"String"
"published":"bool"
"deliveryOptionId":"int"
"skus"[0]:"int"
"skus"[1]:"int"
} 
```

# <a name="Update"> </a> Update ShippingMethod

```json
{
"name":"String"
"cities"[0][city]:"int"
"cities"[0][shippingFees]:"int"
"cities"[0][expectedDeliveryIn]:"String" //2-3
"type":"String"
"published":"bool"
"deliveryOptionId":"int"
"skus"[0]:"int"
"skus"[1]:"int"
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
