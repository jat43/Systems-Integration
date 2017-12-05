<?php
require_once('path.inc');
require_once('requestClient.php.inc');
require_once('loggerClient.php.inc');
session_start();

$response = "unsupported request type";
$request['type'] = "getUserStocks";
$request['username'] = $_SESSION['loginUser'];
$myClient = new rabbitClient("testRabbitMQ.ini","stockServer");
$response = $myClient->make_request($request);

echo json_encode($response);
exit(0);

?>
