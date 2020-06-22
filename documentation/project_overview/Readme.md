# Duktig.Microservice
## Project Overview

### Table of content

Version 1.0.0

 - Application run modes
    - With Apache/Nginx HTTP Server    
    - As CLI Tool
 - Application workflow
 - Directory structure	
 - Cron jobs
    - [Application logs](cron-jobs/1-application-logs.md)
        - Archive log files
        - Generate Application Logs statistics
    - [User actions](cron-jobs/2-user-actions.md)
        - Generate User actions statistics
    - [Backup Databases](cron-jobs/3-backup-databases.md)
        - Backup Databases with given configuration    
 - Application configuration
     - Application
     - Routes for **Command Line Interface**
     - Routes for **HTTP** requests
     - Constants
 - Security
     - Auth by Key
     - Auth by JWT (Access Token)
 - FAQ
     
## Usage

### With regular HTTP Server such as Apache, Nginx
 - Create a virtual host
 - Documentroot: **/www**

### As PHP Command line interface
 - Run CLI php file with defined controller/module/route
 - Executable path: **/cli/exec.php**
  
End of document
