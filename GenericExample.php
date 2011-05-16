<?php
// MessageAPI.php file must be in the required path
// Note: Make sure to change the Account ID and API Key in the MessageAPIConfig.php file
require_once('MessageAPI.php');

sendEmail(array(
        "toName"=> 'John Smith',
	"toEmail" => 'john@example.com',
	"subject" => 'hi John',
	"body" => 'Hello World!',
	"fromName" => 'Message Bus Testing',
	"fromEmail" => 'test@messagebus.com',
	"tag" => 'tag1 tag2 tag3',
	)
);

sendEmail(array(
        "toName"=> 'Jane Smith',
	"toEmail" => 'jane@example.com',
	"subject" => 'hi Jane',
	"body" => 'Hello World!',
	"fromName" => 'Message Bus Testing',
	"fromEmail" => 'test@messagebus.com',
	"tag" => 'tag1 tag2 tag3',
	)
);


static $MessageObject = null;
function sendEmail($params)
{
   global $MessageObject;
   if (!$MessageObject)
      $MessageObject = new MessageAPI();
   $MessageObject->send($params);
}

 
?>