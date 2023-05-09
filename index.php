<?php
/*
Simple Rest-API for IP Blacklist
(c)2023 Daniel Stastka, stastka.ch
*/

require __DIR__ . "/inc/bootstrap.php";
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );
/*
    https://server.lan/folder/<alias>/<APP_NAME>/<command>
*/
if ((isset($uri[2]) && $uri[2] != APP_ALIAS) || (isset($uri[3]) && $uri[3] != APP_NAME) || !isset($uri[4]))
{
    header("HTTP/1.1 404 Not Found");
    exit();
}
//Build Ip Controller 
$ipBlacklistController = new ipCtrl();
//Get Authentication
$user_apikey=$ipBlacklistController->getAuthToken();

/* Special Uri as TXT Output */
if($uri[4] == "raw.txt"){	$uri[4]="raw";}
$strMethodName = 'cmd__' . $uri[4];
$ipBlacklistController->{$strMethodName}($user_apikey);
?>