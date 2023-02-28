# Predefined constants

In **Duktig PHP Framework** project there ara two types of predefined constants:

- System constants
- User defined constants in `app/config/constants.php`

#### System Constants

Depends on running mode of application, the system constants can be different.

All system constants starting with prefix `DUKTIG_`.

##### Run under HTTP mode with web server such as Apache/Nginx

| Constant name      | Value                                  | Description                  |
| :----------------- | :------------------------------------- | :--------------------------- |
| DUKTIG_ENV         | http                                   | Application run environment  |
| DUKTIG_ROOT_PATH   | i.e. /Sites/duktig.microservice.1/     | Absolute path of project     |
| DUKTIG_APP_PATH    | i.e. /Sites/duktig.microservice.1/app/ | Absolute path of application |

##### Run under CLI (Command line interface)

| Constant name      | Value                                   | Description                  |
| :----------------- | :-------------------------------------- | :--------------------------- |
| DUKTIG_ENV         | cli                                     | Application run environment  |
| DUKTIG_ROOT_PATH   | i.e. /Sites/duktig.microservice.1/      | Absolute path of project     |
| DUKTIG_APP_PATH    | i.e. /Sites/duktig.microservice.1/app/  | Absolute path of application |

#### User Defined constants

See all User defined constants in: `app/config/constants.php`

##### General status codes

| Constant name     | Value | Description               |
| :---------------- | :---- | :------------------------ |
| STATUS_INACTIVE   | 0     | Status inactive           |
| STATUS_ACTIVE     | 1     | Status active             | 
| STATUS_SUSPENDED  | 2     | Status suspended          |
| STATUS_TERMINATED | 3     | Status terminated/deleted |

##### User account Roles

| Constant name              | Value | Description              |
| :------------------------- | :---- | :----------------------- |
| USER_ROLE_ANY              | *     | Any type of role         |
| USER_ROLE_SUPER_ADMIN      | 1     | Super administrator role |
| USER_ROLE_ADMIN            | 2     | Administrator role       |
| USER_ROLE_SERVICE_PROVIDER | 3     | Service provider role    |
| USER_ROLE_CLIENT           | 4     | Client role              |
| USER_ROLE_DEVELOPER        | 5     | Developer role           |

##### User account status codes

| Constant name            | Value | Description                      |
| :----------------------- | :---- | :------------------------------- |
| USER_STATUS_NOT_VERIFIED | 0     | Not active, not verified account |
| USER_STATUS_ACTIVE       | 1     | Active account                   |
| USER_STATUS_SUSPENDED    | 2     | Suspended account                |
| USER_STATUS_TERMINATED   | 3     | Terminated account               |

##### Special User IDs

| Constant name  | Value | Description                                          |
| :------------- | :---- | :--------------------------------------------------- |
| USER_ID_USER   | -1    | Any Id of user excepts: system, guest                |
| USER_ID_GUEST  | -2    | Guest / Visitor                                      |
| USER_ID_SYSTEM | -3    | System processes User (not exists in users database) |

End of document