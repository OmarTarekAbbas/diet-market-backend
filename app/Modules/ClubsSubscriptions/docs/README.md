# ClubsSubscription API DOCS

# Base URL
http://localhost:8000

# Other resources 

 
# Headers

Authorization: key your token

Accept : application/json

# API 

| Route                        | Request Method | Parameters | Response  |
| -----------                  | -----------    |----------- |---------- |
| /api/admin/clubssubscriptions            | POST           |  [Create Parmaters](#Create)|[Response](#Response)|
| /api/admin/clubssubscriptions | GET           |-|  [Response](#Response)         |
|/api/admin/clubssubscriptions/{id}         | GET           |  - |  [Response](#Response)         |
|/api/admin/clubssubscriptions/{id}        |PUT           |  [Update Parmaters](#Update)|[Response](#Response)     |
|/api/admin/clubssubscriptions/{id}        |DELETE           |  -|[Response](#Response)| 
|/api/clubssubscriptions        |GET           |-| [Response](#Response)|
|/api/clubssubscriptions/{id}        |GET           |-|[Response](#Response)|


# <a name="Create"> </a> Create new ClubsSubscription 

```json
{
} 
```

# <a name="Update"> </a> Update ClubsSubscription

```json
{
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

        },
    ]
}
```
