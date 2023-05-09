<?php
define("APP_VERSION", "1.2");
define("APP_ALIAS", "app");
define("APP_NAME", "ip");
define("APP_ROOT", dirname(__DIR__, 1). "/");

//base configuration
require_once APP_ROOT . "inc/config.php"; //copy of config.sample.php
//base api controller
require_once APP_ROOT . "inc/controller/class_api_ctrl.php";
//use ip model
require_once APP_ROOT . "inc/model/class_ext_db_ip.php";
//extended ip controller
require APP_ROOT . "inc/controller/class_api_ext_ip.php";
?>