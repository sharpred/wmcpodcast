#!/usr/bin/env php
<?php

// NOTE: You should be using Composer's global autoloader.  But just so these examples
// work for people who don't have Composer, we'll use the library's "autoload.php".
require_once '/vendor/autoload.php';

use \Dropbox as dbx;

if ($argc == 1) {
    echoHelp($argv[0]);
    die;
}
if($argc != 3) {
    fwrite(STDERR, "Expecting exactly 2 arguments, got ".($argc - 1)."\n");
    fwrite(STDERR, "Run with no arguments for help\n");
    die;
}

$argAppInfoFile = $argv[1];
$argAuthFileOutput = $argv[2];

try {
    list($appInfoJson, $appInfo) = dbx\AppInfo::loadFromJsonFileWithRaw($argAppInfoFile);
}
catch (dbx\AppInfoLoadException $ex) {
    fwrite(STDERR, "Error loading <app-json>: ".$ex->getMessage()."\n");
    die;
}
$dbxConfig = new dbx\Config($appInfo, "examples-authorize");
$webAuth = new dbx\WebAuth($dbxConfig);

list($requestToken, $authorizeUrl) = $webAuth->start(null);

echo "1. Go to: $authorizeUrl\n";
echo "2. Click \"Allow\" (you might have to log in first).\n";
echo "3. Hit ENTER to continue.\n";
fgets(STDIN);

list($accessToken, $dropboxUserId) = $webAuth->finish($requestToken);
$serializedAccessToken = $accessToken->serialize();

echo "Authorization complete.\n";
echo "- User ID: $dropboxUserId\n";
echo "- Serialized Access Token: $serializedAccessToken\n";

$authArr = array(
    "app" => $appInfoJson,
    "access_token" => $serializedAccessToken,
);

$json_options = 0;
if (defined('JSON_PRETTY_PRINT')) {
    $json_options |= JSON_PRETTY_PRINT;  // Supported in PHP 5.4+
}
$json = json_encode($authArr, $json_options);

if (file_put_contents($argAuthFileOutput, $json) !== false) {
    echo "Saved authorization information to \"$argAuthFileOutput\".\n";
}
else {
    fwrite(STDERR, "Error saving to \"$argAuthFileOutput\".\n");
    fwrite(STDERR, "Dumping to stderr instead:\n");
    fwrite(STDERR, $json);
    fwrite(STDERR, "\n");
    die;
}

function echoHelp($command) {
    echo "\n";
    echo "Usage: $command <app-info-file> <auth-file-output>\n";
    echo "\n";
    echo "<app-info-file>: A JSON file with information about your API app.  Example:\n";
    echo "\n";
    echo "  {\n";
    echo "    \"key\": \"Your Dropbox API app key\",\n";
    echo "    \"secret\": \"Your Dropbox API app secret\",\n";
    echo "    \"access_type\": \"FullDropbox\" or \"AppFolder\"\n";
    echo "  }\n";
    echo "\n";
    echo "  Get an API app key by registering with Dropbox:\n";
    echo "    https://dropbox.com/developers/apps\n";
    echo "\n";
    echo "<auth-file-output>: If authorization is successful, the resulting API\n";
    echo "  access token will be saved to this file, which can then be used with\n";
    echo "  other example programs, such as \"examples/account-info.php\".\n";
    echo "\n";
}
