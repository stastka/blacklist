<?
define("DB_HOST", "localhost");
define("DB_USERNAME", "rest_username");
define("DB_PASSWORD", "rest_password");
define("DB_DATABASE_NAME", "databasename");
define("API_Key", array(
"Basic " . base64_encode('api-user-1' . ':' . 'api-password-1'),
"Basic " . base64_encode('api-user-2' . ':' . 'api-password-2'),
));
?>