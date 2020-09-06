<?php

declare(strict_types=1);

// Error reporting for production
error_reporting(1);
ini_set('display_errors', '1');

date_default_timezone_set('Africa/Johannesburg');

$settings = [];

// Path settings
$settings['root'] = dirname(__DIR__);
$settings['temp'] = $settings['root'] . '/tmp';
$settings['public'] = $settings['root'] . '/public';
$settings['assets_path'] = $settings['public'] . '/assets/';
$settings['templates'] = $settings['root'] . '/resources/views/';

$db_creds = json_decode( file_get_contents( $settings['root'] . '/settings.json' ), true );
$db_creds = $db_creds['db'];

// Error Handling Middleware settings
$settings['error'] = [

    // Should be set to false in production
    'display_error_details' => true,

    // Parameter is passed to the default ErrorHandler
    // View in rendered output by enabling the "displayErrorDetails" setting.
    // For the console and unit tests we also disable it
    'log_errors' => true,

    // Display error details in error log
    'log_error_details' => true,
];

$settings['db'] = [
    'driver' => $db_creds['driver'],
    'host' => $db_creds['host'],
    'username' => $db_creds['username'],
    'database' => $db_creds['database'],
    'password' => $db_creds['password'],
    'charset' => $db_creds['charset'],
    'collation' => $db_creds['collation'],
    'flags' => [
        // Turn off persistent connections
        PDO::ATTR_PERSISTENT => false,
        // Enable exceptions
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        // Emulate prepared statements
        PDO::ATTR_EMULATE_PREPARES => true,
        // Set default fetch mode to array
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // Set character set
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$db_creds['charset']} COLLATE {$db_creds['collation']}"
    ],
];

$settings['user'] = $_SESSION['user'];


return $settings;