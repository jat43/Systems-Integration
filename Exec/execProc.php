#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('loggerClient.php.inc');

 
function execInstall($path)
{
	$response = exec("./connect $path");
	echo $response.PHP_EOL;
	return $response;	
}

function requestProcessor($request)
{
  	$home = getHostInfo();
  	$clus = $home['server']['cluster'];
  	$servName =  $home['server']['serverName'];
	echo "received request".PHP_EOL;
	var_dump($request);
	if(!isset($request['type']))
  	{
    		return "ERROR: unsupported message type";
  	} 

  	if($request['cluster'] != $clus && $request['serverType'] != $servName)
  	{
		echo "Passing By: Not the cluster and/or server for use";
	}
	else{
   	switch ($request['type'])
  	{
    		case "install":
			if(empty($request['path']))
			{
				echo "Path not set".PHP_EOL;
				return "Path not set";
			}
			return execInstall($request['path']);
   		case "rollback":
			if(empty($request['path']))
			{
				echo "Path not set".PHP_EOL;
				return "Path not set";
			}
			return execInstall($request['path']);
  	}
	}
  	return array("returnCode" => '0', 'message' => "Server recieved request and process");
}

$server = new rabbitMQServer("testRabbitMQ.ini","execServer");

$server->process_requests('requestProcessor');
exit();
?>
