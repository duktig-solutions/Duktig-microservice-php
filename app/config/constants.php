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

# User Account Status codes
define('USER_STATUS_NOT_VERIFIED', 0);
define('USER_STATUS_ACTIVE', 1);
define('USER_STATUS_SUSPENDED', 2);
define('USER_STATUS_TERMINATED', 3);

# Special USER IDs
define('USER_ID_USER', -1);
define('USER_ID_GUEST', -2);
define('USER_ID_SYSTEM', -3);

# Roles 
define('ACCOUNTS_DEFAULT_ROLE', 'a45rzo01f3');

# Account providers
define('ACCOUNT_PROVIDER_BASIC_SIGN_UP', 'BasicSignUp');
define('ACCOUNT_PROVIDER_CREATED_BY_ADMIN', 'CreateByAdmin');
define('ACCOUNT_PROVIDER_GFB_FACEBOOK', 'FireBase_Facebook');
define('ACCOUNT_PROVIDER_GFB_APPLE', 'FireBase_Apple');
define('ACCOUNT_PROVIDER_GFB_GOOGLE', 'FireBase_Google');
define('ACCOUNT_PROVIDER_GFB_PHONE', 'FireBase_Phone');
define('ACCOUNT_PROVIDER_FACEBOOK', 'Facebook');
define('ACCOUNT_PROVIDER_APPLE', 'Apple');
define('ACCOUNT_PROVIDER_GOOGLE', 'Google');
define('ACCOUNT_PROVIDER_PHONE', 'Phone');
define('ACCOUNT_PROVIDER_GITHUB', 'Github');

# End of file
