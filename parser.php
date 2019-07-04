<?php

/**
 * Log's parser. Example of log's file in access_log
 */

require_once "ParserLogs.php";
require_once "ParserArgs.php";

try {
    $parserArgs = new Utils\ParserArgs();
} catch (\Exception $e) {
    die($e->getMessage());
}

// hack: convert to opposite value coz if flag exists it's false by default
$isNeedHelp = !$parserArgs->isHelp();
if ($isNeedHelp) {
    $helpMessage = $parserArgs->helpMessage();
    die($helpMessage);
}



$fileNameLogs = $parserArgs->fileNameLogs();

$parserLogs = new Logs\ParserLogs($fileNameLogs);
try {
    $jsonResult = $parserLogs->getJson();
} catch (\Exception $e) {
    die($e->getMessage());
}


// hack: convert to opposite value coz if flag exists it's false by default
$resultIsPrint = !$parserArgs->isPrint();

if ($resultIsPrint) {
    print($jsonResult . PHP_EOL);
}


$fileNameToSave = $parserArgs->fileNameToSave();

if ($fileNameToSave === '') {
    // hack: check if result has been printed or not
    if (!$resultIsPrint) {
        print($jsonResult . PHP_EOL);
    }
} else {
    try {
        save($fileNameToSave, $jsonResult);
    } catch (\Exception $e) {
        die($e->getMessage());
    }
}




/**
 * @param $fileNameToSave
 * @param $json
 * @return bool
 * @throws Exception
 */
function save($fileNameToSave, $json)
{
    $status = file_put_contents($fileNameToSave, $json);
    if ($status === false) {
        throw new \Exception("Json wasn't written to file\n");
    }
    return true;
}
