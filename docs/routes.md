# Parameters
 *  **_date_** -  format _**YYYY-MM-DD**_

# Routes

## Get all logs

```
Method - GET
Endpoint - http(s)://{your-domain}/{prefix-from-config}
Params - NONE
```

## Get log Levels

```
Method - GET
Endpoint - http(s)://{your-domain}/{prefix-from-config}/levels
Params - NONE
```


## Show daily logs

```
Method - GET
Endpoint - http(s)://{your-domain}/{prefix-from-config}/{date}
Params:
 - query - serach logs
 - level - filter on level
```

## Download daily logs

```
Method - GET
Endpoint - http(s)://{your-domain}/{prefix-from-config}/{date}/download
Params - NONE
```


## Delete a log

```
Method - DELETE
Endpoint - http(s)://{your-domain}/{prefix-from-config}/delete
Params - date (string)

```
