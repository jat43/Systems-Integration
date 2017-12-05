<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

class loggerClient
{
        private $client;
        public function __construct()
        {
                $this->client = new rabbitMQClient("testRabbitMQ.ini","loggingServer");
        }
        public function sendLog($logfile, int $level, $message)
        {
		if (empty($logfile) || empty($level) || empty($message))
		{
			echo "Logfile, level, or message not set for log.";
			exit(1);
		}
		$machine = getHostInfo();
		$machine = $machine['server']['serverName'];	
		$localip = getHostByName(getHostName());
		$request = array("type" => "log", "logfile" => $logfile, "machine" => $machine, "ip" => $localip, "level" => $level, "message"=> $message);
		var_dump($request);
               	$this->client->publish($request);
        }
}
?>

