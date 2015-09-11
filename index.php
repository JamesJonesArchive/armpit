<?php

use USF\IdM\UsfARMapi;

error_reporting(-1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('memory_limit', '-1');
date_default_timezone_set('UTC');
try {
    // Initialize Composer autoloader
    if (!file_exists($autoload = __DIR__ . '/vendor/autoload.php')) {
        throw new \Exception('Composer dependencies not installed. Run `make install --directory app/api`');
    }
    require_once $autoload;
    $usfARMapi = new UsfARMapi();
    $usfParser = new \USF\IdM\UsfParser();
    switch($argv[1]) {
        case "roles":
            $usfParser->parseRoles($argv[2]);
            break;
        case "accounts":
            $usfParser->parseAccounts($argv[2]);
            break;
        case "mapping":
            $usfParser->parseAccountRoles($argv[2]);
            break;
    }
} catch (Exception $ex) {

}



