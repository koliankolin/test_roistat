<?php

/**
 * Log's parser. Example of log's file in access_log
 */

require_once "ParserLogs.php";

if (count($argv) > 1) {
    $files = array_slice($argv, 1);
} else {
    echo 'Usage: php parser FILE_PATH' . PHP_EOL;
    die();
}

$parser = new Logs\ParserLogs($files);
$parser->getJson($files[0]);
