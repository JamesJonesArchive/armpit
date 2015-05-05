<?php

use USF\IdM\UsfARMapi;

error_reporting(-1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
date_default_timezone_set('UTC');
try {
    // Initialize Composer autoloader
    if (!file_exists($autoload = __DIR__ . '/vendor/autoload.php')) {
        throw new \Exception('Composer dependencies not installed. Run `make install --directory app/api`');
    }
    require_once $autoload;
    $usfARMapi = new UsfARMapi();
    $usfParser = new \USF\IdM\UsfParser();
    echo $usfParser->parseRoles('altGEMS_roles.json');
    echo $usfParser->parseAccounts('altGEMS_accounts.json');
    echo $usfParser->parseAccountRoles('altGEMS_accounts_roles.json');
} catch (Exception $ex) {

}



