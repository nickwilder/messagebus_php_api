<?php
// MessageAPI.php file must be in the required path
// Note: Make sure to change the Account ID and API Key in the MessageAPIConfig.php file
require_once('MessageAPI.php');
$toEmails = array('apitest1@messagebus.com', 'apitest2@messagebus.com');
$msg = new MessageAPI();
foreach($toEmails as $toEmail)
{
   $params = array("toEmail" => $toEmail,
				"toName" => '',
				"subject" => 'Multiple Message Sample',
				"body" => 'This message is only a test sent by the PHP MessageBus client library.',
				"fromName" => 'API',
				"fromEmail" => 'api@messagebus.com',
				"tag" => 'PHP',
				"plainText" => '1');

	if (!$msg->send($params))
	{
		echo "Error sending message to $toEmail : ".$msg->getLastError();
	}
	else
	{
		echo "Message sent successfully to $toEmail \n";
	}
}
$msg->close();

 
?>