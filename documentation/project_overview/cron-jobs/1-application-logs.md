# Duktig.Microservice
## Project Overview

Version 1.0.0

### Cron jobs

#### Application logs

**Duktig.Microservice** provides cron job for **CLI** to:

- Archive application log files
- Generate Application Logs statistics
 
The Cron job file located in: `install/cron/app.cron`

##### Archive log files 

This cron job will loop trough all application log files and check sizes. 
In case if file size is greater then 1MB, it will be renamed with date and time suffix as archived.    

Example:

File `app/log/cli-error.log` will be renamed to `app/log/cli-error_2019-08-10_18.05.31_archived.log`

Recommended to run this job 6 times in day (every 4 hours).

    $ php /duktig.microservice.1/cli/exec.php archiveLogFiles

##### Generate Application Logs statistics

This cron job generates Application log files statistics by log types and creates statistic json file for future access. 

The result can be seen in: `app/log/stats.json`

Recommended to run this job 6 times in day (every 4 hours).

    $ php /duktig.dev/cli/exec.php generateLogStats

To get access this statistics, see [System Statistics](../api/application_system_statistics/1-application-logs.md) Resource in API Section.

End of document
