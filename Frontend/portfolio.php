<!DOCTYPE html>
<?php
//include('getUserStocks.php');
session_start();
include('header.php');
require_once('path.inc');
require_once('requestClient.php.inc');
require_once('loggerClient.php.inc');
try{

	$request['type'] = "getUserStocks";
        $request['username'] = 'gabriel';//$_SESSION['loginUser'];
        $myClient = new rabbitClient("testRabbitMQ.ini","stockServer");
        $response = $myClient->make_request($request);
        
	$request['type'] = "myStockStats";
        $myClient = new rabbitClient("testRabbitMQ.ini","stockServer");
        $response2 = $myClient->make_request($request);

		
}
catch(Error $e)
{
        $mylogger = new loggerClient();
        $mylogger->sendLog("userauth.log",2,"Error with user authentication: ".$e." in ".__FILE__." on line ".__LINE__);
        $response = "Sorry, something went wrong.";
}

?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<div class="container">

    <div id="linechart_div" style="width: 900px; height: 500px, padding: 200px"></div>
    <div id="table_div"></div>
</div>


<script>
google.charts.load('current', {'packages':['line']});
      google.charts.setOnLoadCallback(drawChart);

    function drawChart() {

      var data = new google.visualization.DataTable();
      data.addColumn('datetime', 'Time');
	<?php
	foreach($response as $key => $value)
	{
      		echo "data.addColumn('number', '$key');".PHP_EOL;	
	}
	$arraylength = 0;	
	foreach($response as $key => $value)
	{
		$keys[] = $key;
		if(count($response[$key]) > $arraylength)
		{
			$arraylength = count($response[$key]);
			reset($response);
			$tempTime = key($response[$key]);
		}
	}
	$symbolCount = count($keys);
      	if($arraylength != 0)
	{
		echo 'data.addRows([[';
	for($i=0;$i < $arraylength; $i++)
	{
		if(date('N',strtoTime($tempTime)) == '6')
		{
			$tempTime = date('Y-m-d 09:30:00', strtotime($tempTime.'+2 day'));
		}
		elseif(date('N',strtoTime($tempTime)) == '7')
		{
			$tempTime = date('Y-m-d 09:30:00', strtotime($tempTime.'+1 day'));
		}
		if(date('Hi',strtoTime($tempTime)) > '1600')
		{
			$tempTime = date('Y-m-d 09:30:00', strtotime($tempTime.'+1 day'));
		}	
		echo "new Date('$tempTime'), ";
		foreach($keys as $key)
		{
			if(isset($response[$key][$tempTime]['close']))
			{
				echo $response[$key][$tempTime]['close']; 
			}
			else
			{
				echo "null";
			}
			if($key == end($keys))
			{
				echo "]";
			}
			else
			{
				echo ", "; 
			}	
			
		}
		if($i != ($arraylength - 1))
		{
			echo ",".PHP_EOL."[";
		}
		$tempTime = date('Y-m-d H:i:00', strtotime($tempTime.'+1 minute'));	
	}
      		echo ']);'.PHP_EOL;
	}
	?>
	var formatter = new google.visualization.NumberFormat({fractionDigits: 4});
	<?php 
		$i = 1;
		foreach($keys as $key)
		{
			echo "formatter.format(data,".$i.");";
			$i++;
		}
	?>
	var dateformat = new google.visualization.DateFormat({pattern: 'yyyy-MM-d H:mm:ss'});
	dateformat.format(data,0);
      var options = {
        chart: {
          title: 'Your Stocks over time'
        },
        height: window.innerHeight/1.5,
	width: window.innerWidth/1.5,
	interpolateNulls: true,
    explorer: {
        maxZoomOut:2,
        keepInBounds: true
    }
      };

      var chart = new google.charts.Line(document.getElementById('linechart_div'));

      chart.draw(data, google.charts.Line.convertOptions(options));
    }



	google.charts.load('current', {'packages':['table']});
      google.charts.setOnLoadCallback(drawTable);

      function drawTable() {
        var datatable = new google.visualization.DataTable();        
        datatable.addColumn('string', 'Symbol'); 
      	datatable.addColumn('datetime', 'Time first bought');
        datatable.addColumn('number', 'Quantity');
	datatable.addColumn('number', 'Price Difference');
        <?php

        foreach($response2 as $key => $value)
        {
		$currentPrice = end($response[$key])['close'];
		$boughtPrice = $response[$key][$value["min(timestamp)"]]['close'];
		$currentPrice -= $boughtPrice;
                echo 'datatable.addRow(["'.$value["symbol"].'", new Date("'.$value["min(timestamp)"].'") ,'.$value["sum(quantity)"].','.$currentPrice.']);'.PHP_EOL;
        }
        ?>
        var formatter = new google.visualization.NumberFormat({fractionDigits: 4});
        formatter.format(datatable,3);
	var dateformat = new google.visualization.DateFormat({pattern: 'yyyy-MM-d H:mm:ss'});
	dateformat.format(datatable,1);
        var table = new google.visualization.Table(document.getElementById('table_div'));

        table.draw(datatable, {showRowNumber: true, width: '100%', height: '100%'});
      }


</script>

</body>
</html>
