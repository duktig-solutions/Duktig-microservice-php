# Cron jobs

#### Application logs

**Duktig.Microservice** provides cron job for **CLI** to:

- Archive application log files
- Generate Application Logs statistics Json file
 
The Cron job file located in: `install/cron/app.cron`

##### Archive log files 

This cron job will loop trough all application log files and check sizes. 
In case if file size is greater then 1MB (configured), it will be renamed with date and time suffix as archived.    

Example:

File `app/log/app.log` will be renamed to `app/log/app_2019-08-10_18.05.31_archived.log`

Recommended to run this job 6 times in day (every 4 hours).

    $ php ./cli/exec.php archiveLogFiles

##### Generate Application Logs statistics

This cron job generates Application log files statistics by log types and creates statistic json file for future access. 

The result can be seen in: `app/log/stats.json`

Recommended to run this job 6 times in day (every 4 hours).

    $ php /duktig.dev/cli/exec.php makeLogStats

The example will look like:

```json
{
    "lastUpdate": "2020-06-29 09:46:09",
    "filesCount": 2,
    "logsCount": 62956,
    "logs": {
        "EXCEPTION": 55697,
        "INFO": 6846,
        "ERROR": 59,
        "WARNING": 354
    }
}
```

End of document
