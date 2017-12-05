<!DOCTYPE html>

<?php
include('header.php');
require_once('path.inc');
require_once('requestClient.php.inc');
require_once('loggerClient.php.inc');

try{
	$request['type'] = "list";
	$myClient = new rabbitClient("testRabbitMQ.ini","stockServer");
	$respone = $myClient->make_request($request);
}
catch(Error $e)
{
	$mylogger = new loggerClient();
	$mylogger->sendLog("userauth.log",2,"Error with user authentication: ".$e." in ".__FILE__." on line ".__LINE__;
	$respone = "Sorry, something went wrong.";
}

//Get transaction details from URL
//item number 1 is $100 dollars, 2 is $1000, 3 is $10000
$itemNumber = $_GET['item_number'];
$transactionId = $_GET['tx'];
$paymentAmount = $_GET['amt'];
$currency = $_GET['cc'];
$paymentStat = $_GET['st'];

?>
<script>
sendAddBalRequest(<?php$itemNumber?>,<?php$transactionId?>,<?php$paymentAmount?>,<?php$currency?>,<?php$paymentStat?>);

function sendAddBalRequest(itemNumber,transactionId,paymentAmount,currency,paymentStat)
{
var request = new XMLHttpRequest();
request.open("POST","stock.php",true);
request.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
request.onreadystatechange=function ()
{
	if((this.readyState == 4)&&(this.status == 200))
	{
	HandleResponse(this.reponseText);
	}

}
request.send("type=addBal&itemNumber)
}
</script>
</html>
