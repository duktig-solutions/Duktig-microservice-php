<?php

error_reporting(E_ALL & ~E_WARNING & ~E_DEPRECATED);
ini_set('display_errors', '0');

// Composer autoload
require __DIR__ . '/../vendor/autoload.php';

// Prepare a temporary app path
$TMP_APP = sys_get_temp_dir() . '/duktig_app_' . uniqid();
@mkdir($TMP_APP . '/config', 0777, true);
@mkdir($TMP_APP . '/log', 0777, true);

// Define constants used by the kernel
if (!defined('DUKTIG_APP_PATH')) {
    define('DUKTIG_APP_PATH', $TMP_APP . '/');
}
if (!defined('DUKTIG_KERNEL_PATH')) {
    define('DUKTIG_KERNEL_PATH', realpath(__DIR__ . '/../src/kernel/') . DIRECTORY_SEPARATOR);
}

// Minimal config
file_put_contents($TMP_APP . '/config/app.php', "<?php return ['ProjectName'=>'Test','Mode'=>'testing','DateTimezone'=>'UTC'];");
file_put_contents($TMP_APP . '/config/http-routes.php', "<?php return ['/' => ['controller' => 'Home','action'=>'index']];");

// Stub Redis class for Response::enableCaching()
if (!class_exists('Redis')) {
    eval('
        namespace Lib\\Cache;
        class Redis {
            public static function set(string $key, string $value, int $ttl=60): bool { return true; }
            public static function get(string $key): ?string { return null; }
            public static function del(string $key): void {}
            public static function getArray(string $key): array { return []; }
        }
    ');
}

