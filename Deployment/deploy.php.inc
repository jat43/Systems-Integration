<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('loggerClient.php.inc');
require_once('requestClient.php.inc');

class deployDB
{
	private $logindb;
	private $myloggerClient;
	private $requestClient;
	public function __construct()
	{
		$this->myLoggerClient = new loggerClient();
		$this->requestClient = new rabbitClient("testRabbitMQ.ini", 'execServer');
		$this->logindb = new mysqli("127.0.0.1","root","password","stocksdb");
		if ($this->logindb->connect_errno != 0)
		{
			echo "Sending error message to logger".PHP_EOL;
			$this->myLoggerClient->sendLog("database.log", 3,"Error connecting to database: ".$this->logindb->connect_error." in ".__FILE__." on line ".__LINE__);
			exit(1);
		}
		echo "correctly connected to database".PHP_EOL;
	}

	public function newBundle($path, $serverType)
	{
		try{
			$username = exec('whoami');
			$path = substr($path, 1);
			$path = "/home/".$username.$path;
			echo $path.PHP_EOL;
			if(!file_exists($path))
			{
				return "New package was not copied to deploy server";
			}
			$serverType = $this->logindb->real_escape_string($serverType);
			$statement = "select max(version) from deployVersionControl where serverType = '$serverType';";
			$response = $this->logindb->query($statement);
			echo "checking version for ".$serverType.PHP_EOL;
			$row = $response->fetch_assoc();
			if ($row == 0)
			{
				$version = 1;
			}
			else
			{
				$version = $row['max(version)'] + 1;
			}
			$file = substr($path, 0, -26);
			$file .= "v".$version.".tar.gz";
			rename($path, $file);
			$statement = "insert into deployVersionControl(serverType, version) Values('$serverType', '$version');";
			$this->logindb->query($statement);
			if($this->logindb->affected_rows == 1)
			{
				return "Added new package ".$serverType."_v".$version;
			}
			return "Failed to add package to database for ".$serverType.", version ".$version;
		}
		catch(Error $e)
		{
			$this->myLoggerClient->sendLog("database.log", 4,"Fatal Error connecting to database: ".$e." in ".__FILE__." on line ".__LINE__);
			exit(1);
		}
	}
	
	public function getIP()
	{
		try{
			$localip = shell_exec("/sbin/ifconfig enp0s3 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}'");
			return $localip;	
		}
		catch(Error $e)
		{
			$this->myLoggerClient->sendLog("database.log", 4,"Fatal Error connecting to database: ".$e." in ".__FILE__." on line ".__LINE__);
			exit(1);
		}
	}
	public function installBundle($cluster, $server, $version)
	{
		try{
			$server = $this->logindb->real_escape_string($server);
			$statement = "select * from deployVersionControl where serverType = '$server' and version = '$version';";
			$response = $this->logindb->query($statement);
			$row = $response->fetch_assoc();
			if ($row == 0)
			{
				return "Cannot install because ".$version." is not found for ".$server;
			}
			if($row['deprecated'] == 1)
			{
				return "Cannot install because ".$version." is deprecated for ".$server;
			}
			
			$localip = shell_exec("/sbin/ifconfig enp0s3 | grep 'inet addr:' | cut -d: -f2 | awk '{ printf $1}'");

			$path = "~/git/it490f17/Deployment/packages/".$server."/".$server."_v".$version.".tar.gz";
			$username = exec('whoami');
			$localpath = substr($path, 1);
			$localpath = "/home/".$username.$localpath;
			echo "Filepath: ".$localpath.PHP_EOL;
			if(!file_exists($localpath))
			{
				return "Package was not found on deploy server";
			}
			$request['path'] = $localip.":".$path;
			$request['type'] = "install";
			$request['server'] = $server;
			$request['cluster'] = $cluster;
			
			$response = $this->requestClient->make_request($request);	
			return $response;	
		}
		catch(Error $e)
		{
			$this->myLoggerClient->sendLog("database.log", 4,"Fatal Error connecting to database: ".$e." in ".__FILE__." on line ".__LINE__);
			exit(1);
		}
	}
	
	public function deprecateVersion($server, $version)
	{
		try{
			$version = $this->logindb->real_escape_string($version);
			$server = $this->logindb->real_escape_string($server);
			$statement = "select * from deployVersionControl where serverType = '$server' and version = '$version';";
			$response = $this->logindb->query($statement);
			$row = $response->fetch_assoc();
			if ($row == 0)
			{
				return "Cannot deprecate because ".$version." is not found for ".$server;
			}
			if($row['deprecated'] == 1)
			{
				return "Cannot deprecate because ".$version." is already deprecated for ".$server;
			}
			$statement = "update deployVersionControl set deprecated = 1 where serverType = '$server' and version = '$version';";			
			$this->logindb->query($statement);
			if($this->logindb->affected_rows == 0)
			{
				return "Deprecate failed for Version: ".$version.", Server type: ".$server;
			}
			return "Successfully deprecated package ".$server."_v".$version;	
		}
		catch(Error $e)
		{
			$this->myLoggerClient->sendLog("database.log", 4,"Fatal Error connecting to database: ".$e." in ".__FILE__." on line ".__LINE__);
			exit(1);
		}
	}
	public function rollbackBundle($cluster, $server, $version)
	{
		try{
			$server = $this->logindb->real_escape_string($server);
			$statement = "select * from deployVersionControl where serverType = '$server' and version = '$version';";
			$response = $this->logindb->query($statement);
			$row = $response->fetch_assoc();
			if ($row == 0)
			{
				return "Cannot install because ".$version." is not found for ".$server;
			}
			if($row['deprecated'] == 1)
			{
				return "Cannot install because ".$version." is deprecated for ".$server;
			}
			
			$localip = shell_exec("/sbin/ifconfig enp0s3 | grep 'inet addr:' | cut -d: -f2 | awk '{ printf $1}'");

			$path = "~/git/it490f17/Deployment/packages/".$server."/".$server."_v".$version.".tar.gz";
			$username = exec('whoami');
			$localpath = substr($path, 1);
			$localpath = "/home/".$username.$localpath;
			echo "Filepath: ".$localpath.PHP_EOL;
			if(!file_exists($localpath))
			{
				return "Package was not found on deploy server";
			}
			$request['path'] = $localip.":".$path;
			$request['type'] = "rollback";
			$request['server'] = $server;
			$request['cluster'] = $cluster;
			
			$response = $this->requestClient->make_request($request);	
			return $response;	
		}
		catch(Error $e)
		{
			$this->myLoggerClient->sendLog("database.log", 4,"Fatal Error connecting to database: ".$e." in ".__FILE__." on line ".__LINE__);
			exit(1);
		}
	}
	
}
?>
