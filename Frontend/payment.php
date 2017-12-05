<!DOCTYPE html>

<?php
include('header.php');
require_once('path.inc');
require_once('requestClient.php.inc');
require_once('loggerClient.php.inc');


<html lang=en>
<head>
	<meta charset=utf-8>
	<title>test payment</title>
</head>
<body>
<!-- Forms for paypal buttons -->
<h2>Add $100 to Balance</h2>
<table>
<tr>
	<td>
<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" metho="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="hosted_button_id" value="PXSXYKQRPFTGL">
<input type="hidden" name ="return" value="http://localhost/Frontend/success.php">
<input type="image" src="https://www.sandbox.paypal.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="Paypal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://sandbox.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
	</td>
</tr>
</table>
<h2>Add $1000 to Balance</h2>
<table>
<tr>
	<td>
<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" metho="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="hosted_button_id" value="A9ECZSMBMFYVC">
<input type="hidden" name ="return" value="http://localhost/Frontend/success.php">
<input type="image" src="https://www.sandbox.paypal.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="Paypal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://sandbox.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>

	</td>
</tr>
</table>
<h2>Add $10000 to Balance</h2>
<table>
<tr>
	<td>
<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" metho="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="hosted_button_id" value="3K6ZXX7LLFMJL">
<input type="hidden" name ="return" value="http://localhost/Frontend/success.php">
<input type="image" src="https://www.sandbox.paypal.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="Paypal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://sandbox.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>

	</td>
</tr>
</table>




</body>













</html>
