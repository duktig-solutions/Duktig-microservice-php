<?php
/**
 * Application Constants
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */

# General Status codes
define('STATUS_INACTIVE', 0);
define('STATUS_ACTIVE', 1);
define('STATUS_SUSPENDED', 2);
define('STATUS_TERMINATED', 3);

# User Roles
define('USER_ROLE_ANY', '*');
define('USER_ROLE_SUPER_ADMIN', 1);
define('USER_ROLE_ADMIN', 2);
define('USER_ROLE_SERVICE_PROVIDER', 3);
define('USER_ROLE_CLIENT', 4);
define('USER_ROLE_DEVELOPER', 5);

# User Account Status codes
define('USER_STATUS_NOT_VERIFIED', 0);
define('USER_STATUS_ACTIVE', 1);
define('USER_STATUS_SUSPENDED', 2);
define('USER_STATUS_TERMINATED', 3);

# Special USER IDs
define('USER_ID_USER', -1);
define('USER_ID_GUEST', -2);
define('USER_ID_SYSTEM', -3);

# End of file
