#!/usr/bin/php
<?php

function getAPIdata($symbols, $latestTime)
{
error_reporting(E_ALL ^ E_NOTICE);
$dataParsed = array();
$alpha_key = 'N7B9EMN3F2CHEW75';
$now = date('Y-m-d H:i:00',time());
$tempTime = $now;
$i = 0;
echo "Latest time in database: ".$latestTime.PHP_EOL;
if($latestTime == "FALSE")
{
	$i = 102;
}
while($i < 101){
	if(date('N',strtoTime($tempTime)) == '6')
	{	
		$tempTime = date('Y-m-d 16:00:00', strtotime($tempTime.'-1 day'));
	}
	elseif(date('N',strtoTime($tempTime)) == '7')
	{	
		$tempTime = date('Y-m-d 16:00:00', strtotime($tempTime.'-2 day'));
	}
	if(date('Hi',strtoTime($tempTime)) > '1600')
	{	
		$tempTime = date('Y-m-d 16:00:00', strtotime($tempTime));
	}
	elseif(date('Hi',strtoTime($tempTime)) < '0930')
	{
		$tempTime = date('Y-m-d 16:00:00', strtotime($tempTime.'-1 day'));
		if(date('N',strtoTime($tempTime)) == '7')
		{	
			$tempTime = date('Y-m-d 16:00:00', strtotime($tempTime.'-2 day'));
		}
	}
	if($latestTime == $tempTime)
	{
		break;
	}
	$tempTime = date('Y-m-d H:i:00', strtotime($tempTime.'-1 minute'));
	$i++;
}
if($i == 0)
{
	return "Latest data already in database";
}
if($i > 100)
{
	$apiPull = "full";
}
else
{
	$apiPull = "compact";
}
foreach($symbols as $mySymbol)
{
echo $mySymbol.PHP_EOL;
$tempTime = $now;

if($apiPull == "full")
{
	echo "Pulling full api data".PHP_EOL;
	$url = 'https://www.alphavantage.co/query?function=TIME_SERIES_INTRADAY&interval=1min&symbol='.$mySymbol.'&outputsize=full&apikey='.$alpha_key;
}
else
{
	echo "Pulling compact api data".PHP_EOL;
$url = 'https://www.alphavantage.co/query?function=TIME_SERIES_INTRADAY&interval=1min&symbol='.$mySymbol.'&outputsize=compact&apikey='.$alpha_key;
}

$json_obj = file_get_contents($url);
if(empty($json_obj))
{
	return "Error getting data from API";
}
$data = json_decode($json_obj, true);
$arrayLength = count($data['Time Series (1min)']);
$tempTime = $now;
if(date('N',strtoTime($tempTime)) == '6')
{	
	$tempTime = date('Y-m-d 16:00:00', strtotime($tempTime.'-1 day'));
}
elseif(date('N',strtoTime($tempTime)) == '7')
{	
	$tempTime = date('Y-m-d 16:00:00', strtotime($tempTime.'-2 day'));
}
if(date('Hi',strtoTime($tempTime)) > '1600')
{	
	$tempTime = date('Y-m-d 16:00:00', strtotime($tempTime));
}
elseif(date('Hi',strtoTime($tempTime)) < '0930')
{
	$tempTime = date('Y-m-d 16:00:00', strtotime($tempTime.'-1 day'));
	if(date('N',strtoTime($tempTime)) == '7')
	{	
		$tempTime = date('Y-m-d 16:00:00', strtotime($tempTime.'-2 day'));
	}
}

for($i = 0; $i <= 10; $i++)
{
if(!isset($data['Time Series (1min)'][$tempTime]))
{
	if($i == 10)
	{
		echo "Error getting data from API when checking minutes ".$tempTime.PHP_EOL;
		return "Error getting data from API";
	}
	
	$tempTime = date('Y-m-d H:i:00', strtotime($tempTime.'-1 minute'));
}
else
{
	break;
}
}
$i = 0;
while(($i < $arrayLength))
{
	if($tempTime == $latestTime)
	{
		break;
	}
	if(date('N',strtoTime($tempTime)) == '6')
	{	
		$tempTime = date('Y-m-d 16:00:00', strtotime($tempTime.'-1 day'));
	}
	elseif(date('N',strtoTime($tempTime)) == '7')
	{	
		$tempTime = date('Y-m-d 16:00:00', strtotime($tempTime.'-2 day'));
	}
	if(date('Hi',strtoTime($tempTime)) > '1600')
	{	
		$tempTime = date('Y-m-d 16:00:00', strtotime($tempTime));
	}
	elseif(date('Hi',strtoTime($tempTime)) < '0930')
	{
		$tempTime = date('Y-m-d 16:00:00', strtotime($tempTime.'-1 day'));
		if(date('N',strtoTime($tempTime)) == '7')
		{	
			$tempTime = date('Y-m-d 16:00:00', strtotime($tempTime.'-2 day'));
		}
	}
	$dataParsed[$mySymbol][$tempTime]['open'] = $data['Time Series (1min)'][$tempTime]['1. open'];
	$dataParsed[$mySymbol][$tempTime]['high'] = $data['Time Series (1min)'][$tempTime]['2. high'];
	$dataParsed[$mySymbol][$tempTime]['low'] = $data['Time Series (1min)'][$tempTime]['3. low'];
	$dataParsed[$mySymbol][$tempTime]['close'] = $data['Time Series (1min)'][$tempTime]['4. close'];
	$dataParsed[$mySymbol][$tempTime]['volume'] = $data['Time Series (1min)'][$tempTime]['5. volume'];

	$tempTime = date('Y-m-d H:i:00', strtotime($tempTime.'-1 minute'));
	$i++;
}
}
return $dataParsed;

}

?>
