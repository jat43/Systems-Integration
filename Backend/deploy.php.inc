<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('loggerClient.php.inc');

class loginDB
{
	private $logindb;
	private $myloggerClient;
	public function __construct()
	{
		$this->myLoggerClient = new loggerClient();
		$this->logindb = new mysqli("127.0.0.1","root","password","stocksdb");
		if ($this->logindb->connect_errno != 0)
		{
			echo "Sending error message to logger".PHP_EOL;
			$this->myLoggerClient->sendLog("database.log", 3,"Error connecting to database: ".$this->logindb->connect_error." in ".__FILE__." on line ".__LINE__);
			exit(1);
		}
		echo "correctly connected to database".PHP_EOL;
	}

	public function deployNew($command)
	{
		try
		{
			$com = $this->logindb->real_escape_string($command);
			return "Deploy I-c conf.ini";
		}
		catch(Error $e)
		{
			$this->myLoggerClient->sendLog("database.log",4,"Fatal Error connecting to database: ".$e." in ".__FILE__." on line ".__LINE__);
		}


	}
	
	public function deployInstall($cluster,$machine)
	{
		try
		{
			$clu = $this->logindb->real_escape_string($cluster);
			$mac = $this->logindb->real_escape_string($machine);
			return "Deploy install $clu $machine";
		}
		catch(Error $e)
		{
			$this->myLoggerClient->sendLog("database.log",4,"Fatal Error connecting to database: ".$e." in ".__FILE__." on line ".__LINE__);
		}


	}

	public function deployRollback($cluster,$machine,$package)
	{
		try
		{
			$clu = $this->logindb->real_escape_string($cluster);
			$mac = $this->logindb->real_escape_string($machine);
			$pac = $this->logindb->real_escape_string($package);
			return "Deploy rollback $clu $machine $package";
		}
		catch(Error $e)
		{
			$this->myLoggerClient->sendLog("database.log",4,"Fatal Error connecting to database: ".$e." in ".__FILE__." on line ".__LINE__);
		}


	}
	public function deployDepreciate($package)
	{
		try
		{
			$pac = $this->logindb->real_escape_string($package);
			return "Deploy depreciate $pac";
		}
		catch(Error $e)
		{
			$this->myLoggerClient->sendLog("database.log",4,"Fatal Error connecting to database: ".$e." in ".__FILE__." on line ".__LINE__);
		}


	}
}?>
