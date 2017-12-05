<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('loggerClient.php.inc');

class rabbitClient
{
	private $client;
	public function __construct($ini, $server)
	{
		$this->client = new rabbitMQClient($ini, $server);
	}
	public function make_request($request)
	{
		try{
			$client = $this->client;
			return $client->send_request($request);
		}
		catch(Exception $e)
		{
			$mylogger = new loggerClient();
			$mylogger->sendLog("rabbitclient.log",2,"Error with making a request: ".$e." in ".__FILE__." on line ".__LINE__);
			return "Sorry, something went wrong.";
		}

	}
}
?>
