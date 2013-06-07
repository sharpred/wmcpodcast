#!/usr/bin/php
<?php
require 'vendor/autoload.php';
use \Dropbox as dbx;
$file = "db.auth";
list($appInfo, $accessToken) = dbx\AuthInfo::loadFromJsonFile($file);
$dbxConfig = new dbx\Config($appInfo, "wmcpodcaster");

$dbxClient = new dbx\Client($dbxConfig, $accessToken);

$accountInfo = $dbxClient->getAccountInfo();
//print_r($accountInfo);

$f = fopen("wibble.txt", "rb");
$result = $dbxClient->uploadFile("/Public/wibble.txt", dbx\WriteMode::force(), $f);
//fclose($f);
print_r($result);

?>