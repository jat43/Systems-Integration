<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('loggerClient.php.inc');
require_once('requestClient.php.inc');

class stocksDB
{
	private $logindb;
	private $myloggerclient;
	private $requestClient;
	
	public function __construct()
	{
		$this->myLoggerClient = new loggerClient();
		$this->requestClient = new rabbitClient("testRabbitMQ.ini", 'DMZServer');
		$this->logindb = new mysqli("127.0.0.1","root","password","stocksdb");
		if ($this->logindb->connect_errno != 0)
		{
			echo "Sending error message to logger".PHP_EOL;
			$this->myLoggerClient->sendLog("database.log", 3,"Error connecting to database: ".$this->logindb->connect_error." in ".__FILE__." on line ".__LINE__);
			exit(1);
		}
		echo "correctly connected to database".PHP_EOL;
	}
	

	public function transactionsHistory($itemNumber,$transactionId,$paymentAmount,$paymentStat)
	{
		try{
			$itemNum = $this->logindb->real_escape_string($itemNumber);
			$transId = $this->logindb->real_escape_string($transactionId);
			$paymentAmt = $this->logindb->real_escape_string($paymentAmount);
			$payStat = $this->logindb->real_escape_string($paymentStat);
			$query = "insert into paymentHistory(itemNumber,transactionId,paymentAmount,paymentStatus) values('$itemNum','$transId','$paymentAmt','$payStat');
			$req = 





	}










}
?>
