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

	public function validateLogin($username,$password)
	{
		try{
			$un = $this->logindb->real_escape_string($username);
			$pw = $this->logindb->real_escape_string($password);
			$statement = "select * from userlogin where username = '$un';";
			$response = $this->logindb->query($statement);
			while ($row = $response->fetch_assoc())
			{
				echo "checking password for $username".PHP_EOL;
				if (password_verify($pw,$row["password"]))
				{
					echo "passwords match for $username".PHP_EOL;
					
					$statement = "select balance from userInfo where username = '$un';";
					$balanceresponse = $this->logindb->query($statement);
					$response = $balanceresponse->fetch_assoc();

					$response['balance'] = 	$response['balance'];
					$response['login'] = "LoginSuccess";
					return $response;// password match
				}
				echo "passwords did not match for $username".PHP_EOL;
			}
			return "LoginFailed";//no users matched username
		}
		catch(Error $e)
		{
			$this->myLoggerClient->sendLog("database.log", 4,"Fatal Error connecting to database: ".$e." in ".__FILE__." on line ".__LINE__);
			exit(1);
		}
	}
	public function registerUser($username,$password)
	{
		try{
			$un = $this->logindb->real_escape_string($username);
			$pw = $this->logindb->real_escape_string($password);
			$pw = password_hash($pw, PASSWORD_BCRYPT);
			$statement = "select * from userlogin where username = '$un';";
			$response = $this->logindb->query($statement);
			$row = $response->fetch_assoc();
			if($row > 0)
			{
				return "User already in database.";
			}
			$statement = "insert into userlogin(username, password) Values('$un', '$pw');";
			$response = $this->logindb->query($statement);
			if($this->logindb->affected_rows == 1)
			{
				$statement = "insert into userInfo(username, balance) Values('$un', 10000);";
				$response = $this->logindb->query($statement);
				if($this->logindb->affected_rows == 1)
				{
					$statement = "select balance from userInfo where username = '$un';";
					$balanceresponse = $this->logindb->query($statement);
					$response = $balanceresponse->fetch_assoc();
					$response['balance'] = 	$response['balance'];
					$response['login'] = "User Registered";
					return $response;// password match
				}
			}
			return "Error registering user";
		}
		catch(Error $e)
		{
			$this->myLoggerClient->sendLog("database.log", 4,"Fatal Error connecting to database: ".$e." in ".__FILE__." on line ".__LINE__);
			exit(1);
		}
	}
}
?>
