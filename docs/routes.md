# Parameters
 *  **_date_** -  format _**YYYY-MM-DD**_

# Routes

* Get all logs

```
Method - GET
Endpoint - http(s)://{your-domain}/log-viewer/logs
Params - NONE
```


* Show daily logs

```
Method - GET
Endpoint - http(s)://{your-domain}/log-viewer/logs/{date}
Params - NONE
```

* Download daily logs

```
Method - GET
Endpoint - http(s)://{your-domain}/log-viewer/logs/{date}/download
Params - NONE
```

* Get log Levels

```
Method - GET
Endpoint - http(s)://{your-domain}/log-viewer/logs/levels
Params - NONE
```


* Delete a log

```
Method - DELETE
Endpoint - http(s)://{your-domain}/log-viewer/logs/delete
Params - date (string)

```
