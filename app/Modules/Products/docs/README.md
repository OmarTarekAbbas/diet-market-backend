# Product API DOCS
Add a product or add a meal, the difference between them is the type
He adds the admin, merchants, or restaurants, and each one has a repo other than the other
# Base URL
http://127.0.0.1:8000

# Other resources
[ProductReview](./productreview.md)
[ProductCollection](./productcollection.md) 

 
# Headers

Authorization: key your token

Accept : application/json

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/products            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/products | GET           |-|  [Response](#Response)         |
|/api/admin/products/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/products/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/products/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/products        |GET           |-| [Response](#Response)|
|/api/products/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new Product 

```json
{
"name":"product"
"description":"product product product"
"typeNutritionalValue":"grams"
"nutritionalValue"[protein]:"200"
"nutritionalValue"[fat]:"50"
"nutritionalValue"[carbs]:"160"
"quantity":"2"
"discount"[type]:"none"
"discount"[value]:"25"
"discount"[startDate]:"2021-09-19T13:53"
"discount"[endDate]:"2021-09-30T13:53"
"rewardPoints":"50"
"purchaseRewardPoints":"300"
"price":"120"
"finalPrice":"100"
"availableStock":"100"
"maxQuantity":"4"
"minQuantity":"1"
"published":"1"
"imported":"1"
"unit":"1"
"options"[0][optionId]:"14"
"options"[0][type]:"type" //  percentage or amount
"options"[0][required]:"true"
"options"[0][values][0][id]:"27"
"options"[0][values][0][price]:"12"
"options"[0][values][1][id]:"28"
"options"[0][values][1][price]:"13"
"inSubscription":"true"
"priceInSubscription":"1200"
"options"[0][values][0][quantity]:"5"
"options"[0][values][1][quantity]:"6"
"type":"products"
"brand":"2"
"storeManager":"9"
"category":"120"
"sku":"1"
"width":"12"
} 
```

# <a name="Update"> </a> Update Product

```json
{
"name":"product"
"description":"product product product"
"typeNutritionalValue":"grams"
"nutritionalValue"[protein]:"200"
"nutritionalValue"[fat]:"50"
"nutritionalValue"[carbs]:"160"
"quantity":"2"
"discount"[type]:"none" // percentage or amount
"discount"[value]:"25"
"discount"[startDate]:"2021-09-19T13:53"
"discount"[endDate]:"2021-09-30T13:53"
"rewardPoints":"50"
"purchaseRewardPoints":"300"
"price":"120"
"finalPrice":"100"
"availableStock":"100"
"maxQuantity":"4"
"minQuantity":"1"
"published":"1"
"imported":"1"
"unit":"1"
"options"[0][optionId]:"14"
"options"[0][type]:"type"
"options"[0][required]:"true"
"options"[0][values][0][id]:"27"
"options"[0][values][0][price]:"12"
"options"[0][values][1][id]:"28"
"options"[0][values][1][price]:"13"
"inSubscription":"true"
"priceInSubscription":"1200"
"options"[0][values][0][quantity]:"5"
"options"[0][values][1][quantity]:"6"
"type":"products"
"brand":"2"
"storeManager":"9"
"category":"120"
"sku":"1"
"width":"12"
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
