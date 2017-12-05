#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('loggerClient.php.inc');
require_once('requestClient.php.inc');

echo "Do you want to make, install, deprecate, or rollback package? ";
$handle = fopen ("php://stdin","r");
$request['type'] = fgets($handle);
$request['type'] = trim($request['type']);
$requestClient = new rabbitClient("testRabbitMQ.ini", 'deploymentServer');
switch ($request['type']) {
    case "make":
        echo "Creating new bundle".PHP_EOL;
        $serverInfo = getHostInfo();
        $server =  $serverInfo['server']['serverName'];
        $now = date('Y-m-d_H-i-s',time());
        $rootpath = "~/git/it490f17/";
        $servertypes = array("Backend", "Frontend", "DMZ");
        if(!in_array($server, $servertypes))
        {
                echo $server." is not a valid server type".PHP_EOL;
                exit();
        }
	$filename = $server."_".$now.".tar.gz"; 
	$localpath = $rootpath.$filename;	
	$destinationPath = $rootpath."Deployment/packages/".$server."/";
	$shellpath = $destinationPath;
	$destinationPath .= $filename;
	echo "Getting IP address for deployment Server".PHP_EOL;
	$request['type'] = "getIP";
	$response = $requestClient->make_request($request);
	echo $response.PHP_EOL;

	echo "Executing tar command for ".$filename.PHP_EOL;
        $now = escapeshellarg($now);
        //$filename = escapeshellarg($filename);
	shell_exec("tar -czf ../'$filename' -C ../ '$server' libraries");
	
	echo "Executing scp".PHP_EOL;
        $response = escapeshellarg($response);
	shell_exec("scp $localpath $response:$shellpath");
	unlink("../".$filename);
	$request['type'] = "make";
	$request['path'] = $destinationPath;
	$request['server'] = $server;
	echo "Sending request to track new package".PHP_EOL;
	$requestClient = new rabbitClient("testRabbitMQ.ini", 'deploymentServer');
	break;
    case "install":
        echo "Which cluster do you want to update? ";
	$request['cluster'] = fgets($handle);
	$request['cluster'] = trim($request['cluster']);
        echo "Which server do you want to update? ";
	$request['server'] = fgets($handle);
	$request['server'] = trim($request['server']);
        echo "What version do you want to install? ";
	$request['version'] = fgets($handle);
	$request['version'] = trim($request['version']);
        break;
    case "deprecate":
        echo "Which server type do you want to deprecate? ";
	$request['server'] = fgets($handle);
	$request['server'] = trim($request['server']);
        echo "What version do you want to deprecate? ";
	$request['version'] = fgets($handle);
	$request['version'] = trim($request['version']);
        break;
    case "rollback":
        echo "Which cluster do you want to rollback? ";
	$request['cluster'] = fgets($handle);
	$request['cluster'] = trim($request['cluster']);
        echo "Which server do you want to rollback? ";
	$request['server'] = fgets($handle);
	$request['server'] = trim($request['server']);
        echo "What version do you want to rollback to? ";
	$request['version'] = fgets($handle);
	$request['version'] = trim($request['version']);
        break;
    default:
        echo "Not a valid command".PHP_EOL;
        exit();
}

$response = $requestClient->make_request($request);
echo $response.PHP_EOL;
?>

