<?php
class logger
{
	private $filestream;
	
	public function __construct($logfile)
	{
		$logdirectory = "../logging/";
		if(!file_exists($logdirectory))
		{
			mkdir($logdirectory, 0775, true);
		}	
		$this->filestream = fopen($logdirectory.$logfile,"a") or exit("Can't open log file ".$logfile.PHP_EOL);
	}
	
	public function writeLog($level,$machine,$ip,$message)
	{
		if($level <= 1)
		{
			$level = "LOW";
		}
		else if($level == 2)
		{
			$level = "MEDIUM";
		} 
		else if($level == 3)
		{
			$level = "HIGH";
		} 
		else if($level >= 4)
		{
			$level = "SEVERE";
		} 
		fwrite($this->filestream, "[".date('Y-m-d H:i:s')."] ==".$level."== ". $machine." - ".$ip.PHP_EOL.$message.PHP_EOL);
	}
	
	public function __destruct()
	{
		fclose($this->filestream);
	}
}

?>
