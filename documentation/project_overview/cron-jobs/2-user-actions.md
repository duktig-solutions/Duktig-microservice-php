# Duktig.Microservice
## Project Overview

Version 1.0.0

### Cron jobs

#### User actions

**Duktig.Microservice** provides cron job for **CLI** to:

- Generate User actions statistics
 
The Cron job file located in: `install/cron/app.cron`

##### Generate User actions statistics

This cron job will generate User actions statistics based on `userActions` database table 
and create a json content for future use.

Recommended to run this job once a day in last minutes of day.

    $ php /duktig.microservice.1/cli/exec.php generateUserActionsStats

End of document
