<!DOCTYPE html>
<?php
session_start();
include('header.php');
require_once('path.inc');
require_once('requestClient.php.inc');
require_once('loggerClient.php.inc');
try{

	$request['type'] = "list";
	$myClient = new rabbitClient("testRabbitMQ.ini","stockServer");
	$response = $myClient->make_request($request);
}
catch(Error $e)
{
        $mylogger = new loggerClient();
        $mylogger->sendLog("userauth.log",2,"Error with user authentication: ".$e." in ".__FILE__." on line ".__LINE__);
        $response = "Sorry, something went wrong.";
}




?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="js/dropdown.js"></script>
<div class="container">
  <div class="jumbotron">
    <h1>Welcome!</h1>      
    <p>This is Stocks R Us, where you can fulfill your stock market needs</p>
  </div>

  <form class="navbar-form navbar" style="margin-top: -20px; margin-left: -10px; width: 100%;">
    <button type="button" class="btn btn-default" onclick="submitBuy()">Buy</button>
    <button type="button" class="btn btn-default" onclick="submitSell()">Sell</button>
    <input type="quantity" id="inputNum" class="form-control" placeholder="Amount">
	<div class="dropdown">            
<a class="btn btn-default btn-select btn-select-light">
                <input type="hidden" class="btn-select-input" id="dropdownStock" name="" value="" />
                <span class="btn-select-value">Select an Item</span>
                <span class='btn-select-arrow glyphicon glyphicon-chevron-down'></span>
                <ul>
			<?php
			foreach($response as $data)
			{
				echo '<li>'.$data['symbol'].'</li>';
			} 
			?>
		</ul>
            </a>
        </div>   
	</form>   
	<h1>Stock timestamp: <?php echo $response['0']['timestamp']; ?> </h1> 
  <form class="navbar-form navbar" style="width: 100%;">
    <button type="button" class="btn btn-default" onclick="submitSearch()">Search</button>
	<div class="dropdown">            
<a class="btn btn-default btn-select btn-select-light">
                <input type="hidden" class="btn-select-input" id="dropdownSearch" name="" value="" />
                <span class="btn-select-value">Select an Item</span>
                <span class='btn-select-arrow glyphicon glyphicon-chevron-down'></span>
                <ul>
			<?php
			foreach($response as $data)
			{
				echo '<li>'.$data['symbol'].'</li>';
			} 
			?>
		</ul>
            </a>
        </div>   
	</form>   
    <div id="table_div"></div>
    <div id="linechart_div"></div>

    <div id="output">status<p></div>    
</div>    
		<script src="js/ie10-viewport-bug-workaround.js"></script>

<script>

//This is the code that stores the usernames from the session
<?php echo "var user = '" .$_SESSION['loginUser']. "';"; ?>
var datachart;
var chartoptions;
google.charts.load('current', {'packages':['table']});
      google.charts.setOnLoadCallback(drawTable);
	
      function drawTable() {
        var data = new google.visualization.DataTable();	
        data.addColumn('string', 'symbol');
        data.addColumn('number', 'open');
        data.addColumn('number', 'close');
        data.addColumn('number', 'high');
        data.addColumn('number', 'low');
        data.addColumn('number', 'volume');
        <?php
	
	foreach($response as $key => $value)
	{	
		echo 'data.addRow(["'.$value["symbol"].'",'.$value["open"].','.$value["close"].','.$value["high"].','.$value["low"].','.$value["volume"].']);';
	}
	?>
	var formatter = new google.visualization.NumberFormat({fractionDigits: 4});
	formatter.format(data,1);
	formatter.format(data,2);
	formatter.format(data,3);
	formatter.format(data,4);
        var table = new google.visualization.Table(document.getElementById('table_div'));

        table.draw(data, {showRowNumber: true, width: '100%', height: '100%'});
      }

	google.charts.load('current', {'packages':['line']});
      google.charts.setOnLoadCallback(drawChart);

    function drawChart() {

     	datachart = new google.visualization.DataTable();
      datachart.addColumn('datetime', 'Time');
	datachart.addColumn('number', 'Stock');
	var dateformat = new google.visualization.DateFormat({pattern: 'yyyy-MM-d H:mm:ss'});
	dateformat.format(datachart,0);
      chartoptions = {
        chart: {
          title: 'Stock over time'
        },
        height: window.innerHeight/1.5,
	width: window.innerWidth/1.5,
	interpolateNulls: true,
    explorer: {
        maxZoomOut:2,
        keepInBounds: true
    }
      };


    }


function CreateChart(response)
{
	var response = JSON.parse(response);
	datachart.removeRows(0, datachart.getNumberOfRows());
	for (var key in response) {
		datachart.addRow([new Date(response[key].timestamp), parseInt(response[key].close)]);
	}
	var formatter = new google.visualization.NumberFormat({fractionDigits: 4});
	formatter.format(datachart,1);
	 var chart = new google.charts.Line(document.getElementById('linechart_div'));
      	chart.draw(datachart, google.charts.Line.convertOptions(chartoptions));
}


function HandleResponse(response)
{
	var text = JSON.parse(response);
	document.getElementById("output").innerHTML = text;
}
function sendSearchRequest(symbol)
{
	var request = new XMLHttpRequest();
	request.open("POST","stock.php",true);
	request.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	request.onreadystatechange= function ()
	{
		if ((this.readyState == 4)&&(this.status == 200))
		{
			CreateChart(this.responseText);
		}		
	}
	request.send("type=search&symbol="+symbol);
}

function submitSearch()
{
  var symbol = document.getElementById("dropdownSearch").value;
  document.getElementById("output").innerHTML = "Searching stock: " + symbol + "<p>";
 sendSearchRequest(symbol);
  return 0;
}

function submitBuy()
{
  var num = document.getElementById("inputNum").value;
  var symbol = document.getElementById("dropdownStock").value;
  document.getElementById("output").innerHTML = "Buying stock: " + symbol + "<p>amount: " + num + "<p>";
 sendBuyRequest(symbol,num);
  return 0;
}
function sendBuyRequest(symbol,num)
{
  var request = new XMLHttpRequest();
  request.open("POST","stock.php",true);
  request.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
  request.onreadystatechange= function ()
  {
    if ((this.readyState == 4)&&(this.status == 200))
    {
      HandleResponse(this.responseText);
    }   
  }
  request.send("type=buy&symbol="+symbol+"&quantity="+num+"&username="+user);
}

function submitSell()
{
  var num = document.getElementById("inputNum").value;
  var symbol = document.getElementById("dropdownStock").value;
  document.getElementById("output").innerHTML = "Selling stock: " + symbol + "<p>amount: " + num + "<p>";
  sendSellRequest(symbol,num);
  return 0;
}
function sendSellRequest(symbol,num)
{
  var request = new XMLHttpRequest();
  request.open("POST","stock.php",true);
  request.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
  request.onreadystatechange= function ()
  {
    if ((this.readyState == 4)&&(this.status == 200))
    {
      HandleResponse(this.responseText);
    }   
  }
  request.send("type=sell&symbol="+symbol+"&quantity="+num+"&username="+user);
}


</script>

</body>
</html>
