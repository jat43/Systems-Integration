<?php
require_once('path.inc');
require_once('requestClient.php.inc');
require_once('loggerClient.php.inc');
session_start();

try{
	if (empty($_POST))
	{
		$msg = "NO POST MESSAGE SET";
		echo json_encode($msg);
		exit(0);
	}
	$response = "unsupported request type";

	//POST returns keys: login or register, uname, and pword.
	$request = $_POST;
	//Calls that functions to make the Client
	$myClient = new rabbitClient("testRabbitMQ.ini","stockServer");
	$response = $myClient->make_request($request);

}
catch(Error $e)
{
	$mylogger = new loggerClient();
	$mylogger->sendLog("userauth.log",2,"Error with user authentication: ".$e." in ".__FILE__." on line ".__LINE__);
	$response = "Sorry, something went wrong.";
}

echo json_encode($response);
exit(0);
?>
