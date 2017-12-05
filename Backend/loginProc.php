#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('login.php.inc');
require_once('loggerClient.php.inc');

function doLogin($username,$password)
{
	$dbConn = new loginDB();
	$response = $dbConn->validateLogin($username,$password);
	var_dump($response);
	return $response;
}
function doRegister($username,$password)
{
	$dbConn = new loginDB();
	$response = $dbConn->registerUser($username,$password);
	var_dump($response);
	return $response;
}


function requestProcessor($request)
{
	try{
		echo "received request".PHP_EOL;
		var_dump($request);
		if(!isset($request['type']))
		{
			return "ERROR: unsupported message type";
		}
		switch ($request['type'])
		{
			case "login":
				if(empty($request['uname']) || empty($request['pword']))
				{
					echo "Username or Password not given.".PHP_EOL;
					return "Username or Password not given.";
				}
				return doLogin($request['uname'],$request['pword']);
			case "register":
				if(empty($request['uname']) || empty($request['pword']))
				{
					echo "Username or Password not given.".PHP_EOL;
					return "Username or Password not given.";
				}
				if(strlen($request['uname']) < 5 || strlen($request['pword']) < 5)
				{	
					echo "Username or Password too short.";
					return "Username or Password too short.";
				}
				return doRegister($request['uname'],$request['pword']);
			case "validate_session":
				return doValidate($request['sessionId']);
		}
		return array("returnCode" => '0', 'message'=>"Server received request and processed");
	}
	catch(Exception $e)
	{
		$mylogger = new loggerClient();
		$mylogger->sendLog("userauth.log",2,"Error with user authentication: ".$e." in ".__FILE__." on line ".__LINE__);
		exit(1);
	}
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

$server->process_requests('requestProcessor');
exit();
?>

