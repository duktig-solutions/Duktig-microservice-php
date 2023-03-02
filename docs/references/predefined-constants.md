# Predefined constants

In **Duktig PHP Framework** there ara two types of predefined constants:

- System constants
- User defined constants in `app/config/constants.php`

## System Constants

Depends on application tun mode, the system constants can be different.

All system constants starting with prefix `DUKTIG_`.

**Run under HTTP mode with web server such as Apache/Nginx**

| Constant name      | Value                                  | Description                  |
| :----------------- | :------------------------------------- | :--------------------------- |
| DUKTIG_ENV         | http                                   | Application run environment  |
| DUKTIG_ROOT_PATH   | i.e. /Sites/duktig.microservice.1/     | Absolute path of project     |
| DUKTIG_APP_PATH    | i.e. /Sites/duktig.microservice.1/app/ | Absolute path of application |

**Run under CLI (Command line interface)**

| Constant name      | Value                                   | Description                  |
| :----------------- | :-------------------------------------- | :--------------------------- |
| DUKTIG_ENV         | cli                                     | Application run environment  |
| DUKTIG_ROOT_PATH   | i.e. /Sites/duktig.microservice.1/      | Absolute path of project     |
| DUKTIG_APP_PATH    | i.e. /Sites/duktig.microservice.1/app/  | Absolute path of application |

## User Defined constants

See all User defined constants in: `app/config/constants.php`

**General status codes**

| Constant name     | Value | Description               |
| :---------------- | :---- | :------------------------ |
| STATUS_INACTIVE   | 0     | Status inactive           |
| STATUS_ACTIVE     | 1     | Status active             | 
| STATUS_SUSPENDED  | 2     | Status suspended          |
| STATUS_TERMINATED | 3     | Status terminated/deleted |

