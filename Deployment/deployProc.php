#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('deploy.php.inc');

function doNewBundle($path,$serverType)
{
	$deployConn = new deployDB();
	$response = $deployConn->newBundle($path,$serverType);
	echo $response.PHP_EOL;
	return $response;	
}

function dogetIP()
{
	$deployConn = new deployDB();
	$response = $deployConn->getIP();
	echo $response.PHP_EOL;
	return $response;	
}

function doInstallBundle($cluster,$server,$version)
{
	$deployConn = new deployDB();
	$response = $deployConn->installBundle($cluster,$server,$version);
	echo $response.PHP_EOL;
	return $response;	
}
function doRollbackBundle($cluster,$server,$version)
{
	$deployConn = new deployDB();
	$response = $deployConn->rollbackBundle($cluster,$server,$version);
	echo $response.PHP_EOL;
	return $response;	
}

function doDeprecateVersion($server,$version)
{
	$deployConn = new deployDB();
	$response = $deployConn->deprecateVersion($server,$version);
	echo $response.PHP_EOL;
	return $response;	
}

function requestProcessor($request)
{
  echo "received request".PHP_EOL;
  var_dump($request);
  if(!isset($request['type']))
  {
    return "ERROR: unsupported message type";
  }
   switch ($request['type'])
  {
    case "make":
	if(empty($request['path']) || empty($request['server']))
	{
		echo "Path or serverType not set for new package.".PHP_EOL;
		return "Path or serverType not set for new package.";
	}
	return doNewBundle($request['path'],$request['server']);
    case "getIP":
	return dogetIP();
    case "install":
	if(empty($request['cluster']) || empty($request['server']) || empty($request['version']))
	{
		echo "Cluster, server, or version not set for install.".PHP_EOL;
		return "Cluster, server, or version not set for install.";
	}
	return doInstallBundle($request['cluster'],$request['server'],$request['version']);
    case "rollback":
	if(empty($request['cluster']) || empty($request['server']) || empty($request['version']))
	{
		echo "Cluster, server, or version not set for install.".PHP_EOL;
		return "Cluster, server, or version not set for install.";
	}
	return doRollbackBundle($request['cluster'],$request['server'],$request['version']);
    case "deprecate":
	if(empty($request['server']) || empty($request['version']))
	{
		echo "Server or version not set for deprecate.".PHP_EOL;
		return "Server or version not set for deprecate.";
	}
	return doDeprecateVersion($request['server'],$request['version']);
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","deploymentServer");

$server->process_requests('requestProcessor');
exit();
?>

