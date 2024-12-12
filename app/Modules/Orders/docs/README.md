# Order API DOCS
 It is about orders adding a product or a meal, booking a specialist, or booking in clubs
# Base URL
http://127.0.0.1:8000

# Other resources
[ReturningReason](./returningreason.md)
[Review](./review.md)
[CancelingReason](./cancelingreason.md) 

 
# Headers

Authorization: key your token

Accept : application/json

LOCALE-CODE : {lang}


# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/orders            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/orders | GET           |-|  [Response](#Response)         |
|/api/admin/orders/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/orders/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/orders/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/orders        |GET           |-| [Response](#Response)|
|/api/orders/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Order 

```json
{
"paymentMethod":"wallet" //cashOnDelivery,VISA,MADA,MASTER,APPLEPAY,wallet
"notes":"bla bla bla"
"type":"products" //food/products/clubs/nutritionSpecialist
"deliveryType":"inHome" //inStore,inHome / type == food
"fromTime":"10:00" //deliveryType=inStore
"toTime":"11:00" // deliveryType=inStore
"idpackagesClubs":"1" // show for type == clubs
"nutritionSpecialist":"5" //show for type == nutritionSpecialist
"time":"00:00" //show for type == nutritionSpecialist
"data":"2021-12-24" //show for type == nutritionSpecialist
"insideWhereType":"mobile"
} 
```

# <a name="Update"> </a> Update Order

```json
{
"paymentMethod":"wallet" //cashOnDelivery,VISA,MADA,MASTER,APPLEPAY,wallet
"notes":"bla bla bla"
"type":"products" //food/products/clubs/nutritionSpecialist
"deliveryType":"inHome" //inStore,inHome / type == food
"fromTime":"10:00" //deliveryType=inStore
"toTime":"11:00" // deliveryType=inStore
"idpackagesClubs":"1" // show for type == clubs
"nutritionSpecialist":"5" //show for type == nutritionSpecialist
"time":"00:00" //show for type == nutritionSpecialist
"data":"2021-12-24" //show for type == nutritionSpecialist
"insideWhereType":"mobile"
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
