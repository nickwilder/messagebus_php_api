<?php
// MessageAPI.php file must be in the required path
// Note: Make sure to change the Account ID and API Key in the MessageAPIConfig.php file
require_once('MessageAPI.php');
$params = array("toEmail" => 'apitest1@messagebus.com',
				"toName" => 'EmailUser',
				"subject" => 'Single Message Sample',
				"body" => 'This message is only a test sent by the PHP MessageBus client library.',
				"fromName" => 'API',
				"fromEmail" => 'api@messagebus.com',
				"tag" => 'PHP',
				"plainText" => '1');

if (!SingleMessageAPI::send($params))
{
  	echo "Error sending message: ".SingleMessageAPI::getLastError();
}
else
{
	echo "Message sent successfully!\n";
}
 
?>