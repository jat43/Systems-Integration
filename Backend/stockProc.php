#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('stock.php.inc');
require_once('loggerClient.php.inc');

function buyStock($symbol,$quantity,$username)
{
	$dbConn = new stocksDB();
	$response = $dbConn->buyStock($symbol,$quantity,$username);
	echo $response.PHP_EOL;
	return $response;
}
function sellStock($symbol,$quantity,$username)
{
	$dbConn = new stocksDB();
	$response = $dbConn->sellStock($symbol,$quantity,$username);
	echo $response.PHP_EOL;
	return $response;
}
function checkUserStock($username)
{
	$dbConn = new stocksDB();
	$response = $dbConn->checkUserStock($username);
	echo "Returning array of users stocks".PHP_EOL;
	return $response;
}
function listStocks()
{
	$dbConn = new stocksDB();
	$response = $dbConn->listStocks();
	var_dump($response);
	return $response;
}
function searchStock($stock)
{
	$dbConn = new stocksDB();
	$response = $dbConn->searchStock($stock);
	echo "Returning array of searched stocks".PHP_EOL;
	return $response;
}
function myStockStats($username)
{
	$dbConn = new stocksDB();
	$response = $dbConn->myStockStats($username);
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
			case "buy":
				if(empty($request['quantity']) || empty($request['symbol']))
				{
					echo "Stock symbol or Quantity not given.".PHP_EOL;
					return "Stock symbol or Quantity not given.";
				}
				return buyStock($request['symbol'],$request['quantity'],$request['username']);
			case "sell":
				if(empty($request['symbol']) || empty($request['quantity']))
				{
					echo "Stock symbol or Quantity not given.".PHP_EOL;
					return "Stock symbol or Quantity not given.";
				}
				return sellStock($request['symbol'],$request['quantity'],$request['username']);
			case "getUserStocks":
				if(empty($request['username']))
				{
					echo "Username not given.".PHP_EOL;
					return "Username not given.";
				}
				return checkUserStock($request['username']);
			case "list":
				return listStocks();
			case "search":
				if(empty($request['symbol']))
				{
					echo "Stock Name not given".PHP_EOL;
					return "Stock Name not given.";
				}
				return searchStock($request['symbol']);
			case "myStockStats":
				if(empty($request['username']))
				{
					echo "Username not given.".PHP_EOL;
					return "Username not given.";
				}
				return myStockStats($request['username']);
		}
	
		return array("returnCode" => '0', 'message'=>"Server received request and processed");
	}

	catch(Exception $e)
	{
		$mylogger = new loggerClient();
		$mylogger->sendLog("stocksProc.log",2,"Error with getting stocks: ".$e." in ".__FILE__." on line ".__LINE__);
		exit(1);
	}
}

$server = new rabbitMQServer("testRabbitMQ.ini","stockServer");

$server->process_requests('requestProcessor');
exit();
?>

