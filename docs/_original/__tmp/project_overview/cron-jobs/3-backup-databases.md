# Duktig.Microservice
## Project Overview

Version 1.0.0

### Cron jobs

#### Backup Databases

**Duktig.Microservice** provides cron job for **CLI** to:

- Periodic backup of given databases.
- Keep an old copy of backup for given day.

To configure the backup process see Application configuration. 

##### Configure Databases access for backup purposes

```php
'Databases' => [
    # Authorization for Database backup user
    'BackupConn' => [
    	'driver' => 'MySQLi',
    	'host' => 'localhost',
    	'port' => 3306,
    	'username' => '{username}',
    	'password' => '{password}',
    	'database' => '2do',
    	'charset' => 'utf8'
    ]    
]
```

##### Configure mysqldump path in executables section

```php
# System binary executables
'Executables' => [
    'mysqldump' => '/usr/local/mysql/bin/mysqldump'
],
```

##### Configure databases to backup

The `'Databases'` section in `'Backups'` contains an array.

Each array is a database name to backup and list of tables to exclude from backup process.   

```php
# Backup Configuration
'Backups' => [
	# Databases to backup
	'Databases' => [
		[
			# Database name
			'database' => '2do',
			# Database Tables which will be excluded from backup process.
			'excluded_tables' => []
		],
		// Another database config here
	]	
```

##### Configure Backup Destination directory


By default you can leave empty the `DatabasesBackupDir` backup destination path in configuration.

In case if the destination path not specified, the system will backup into `/app/backups/db` 

If the destination directory configured and not exists, the system will try to create it. 

```php
# Backup Configuration
'Backups' => [
    # ! The last slash in path is important
    'DatabasesBackupDir' => '/var/backups/db',
]
```

##### Configure the days to keep backups

The `DatabasesBackupSteps` section allows to define, how many days a backup files will keep.

> NOTE: The 1st number file is newest. So, every time the backup tool will move oldest backups step back.

i.w. `1 (latest)`, `2 (second)`, `3 (3th backup)`, and so on until limited amount of copies.  

```php
# Backup Configuration
'Backups' => [
    'DatabasesBackupSteps' => 7,
]
```
 
The Cron job file located in: `install/cron/app.cron`

Recommended to run this job once a day in last minutes of day.

    $ php /duktig.microservice.1/cli/exec.php db-backup

End of document
