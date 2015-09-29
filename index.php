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
    $usfARMImportFileProcessor = new \USF\IdM\UsfARMImportFileProcessor();
    if(in_array(strtolower(trim($argv[1])), ['roles','accounts','mapping'])) {
        $usfARMImportFileProcessor->parseFileByType($argv[2], strtolower(trim($argv[1])));
    } else {
        exit("Invalid option for import type: {$argv[1]}");
    }
} catch (Exception $ex) {

}



