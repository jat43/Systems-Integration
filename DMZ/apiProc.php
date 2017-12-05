#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('alpha_api.php.inc');
require_once('loggerClient.php.inc');

function getStockData($symbols, $latestTime)
{
	var_dump($symbols);
	$response = getAPIdata($symbols, $latestTime);
	echo "Sending response: ".PHP_EOL;
	var_dump($response);
	return $response;
}

function requestProcessor($request)
{
	try
	{
		echo "received request".PHP_EOL;
		var_dump($request);
		if(!isset($request['type']))
		{
			return "ERROR: unsupported message type";
		}
		switch ($request['type'])
		{
			case "updateData":
				if(empty($request['symbols']))
				{
					echo "Stock symbol not given.".PHP_EOL;
					return "Stock symbol not given.";
				}
				return getStockData($request['symbols'],$request['latestTime']);
		}
	
		return array("returnCode" => '0', 'message'=>"Server received request and processed");
	}

	catch(Exception $e)
	{
		$mylogger = new loggerClient();
		$mylogger->sendLog("dmz.log",2,"Error with getting data from DMZ: ".$e." in ".__FILE__." on line ".__LINE__);
		exit(1);
	}
}

$server = new rabbitMQServer("testRabbitMQ.ini","DMZServer");

$server->process_requests('requestProcessor');
exit();
?>

