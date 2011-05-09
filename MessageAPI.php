<?php
/**
 * Message API
 *
 * This is the message API code used to send messages
 * to Message Bus
 * @package MessageAPI
 * @version 2.0
 * @example send_sample1.php Singe Message sample code
 *
 * Sample use cases are below:
 *
 * 1. Send a single message
 * <code>
 *   
 *   // MessageAPI.php file must be in the required path
 *   // Note: Make sure to change the Account ID and API Key in the MessageAPIConfig.php file
 *   require_once('MessageAPI.php');
 *   $params = array("toEmail" => 'apitest1@messagebus.com',
 *				"toName" => 'EmailUser',
 *				"subject" => 'Single Message Sample',
 *				"body" => 'This message is only a test sent by the PHP MessageBus client library.',
 *				"fromName" => 'API',
 *				"fromEmail" => 'api@messagebus.com',
 *				"tag" => 'PHP',
 *				"plainText" => '1');
 *   
 *   if (!SingleMessageAPI::send($params))
 *   {
 *     echo "Error sending message: ".SingleMessageAPI::getLastError();
 *   }
 *   else
 *   {
 *     echo "Message sent successfully!\n";
 *   }
 * </code>
 *
 * 2. Sending multiple messages by keeping connection open
 * <code>
 *   // MessageAPI.php file must be in the required path
 *   // Note: Make sure to change the Account ID and API Key in the MessageAPIConfig.php file
 *   require_once('MessageAPI.php');
 *   $toEmails = array('apitest1@messagebus.com', 'apitest2@messagebus.com');
 *   $msg = new MessageAPI();
 *   foreach($toEmails as $toEmail)
 *   {
 *      $params = array("toEmail" => $toEmail,
 *				"toName" => '',
 *				"subject" => 'Multiple Message Sample',
 *				"body" => 'This message is only a test sent by the PHP MessageBus client library.',
 *				"fromName" => 'API',
 *				"fromEmail" => 'api@messagebus.com',
 *				"tag" => 'PHP',
 *				"plainText" => '1');
 *
 *	if (!$msg->send($params))
 *   	{
 *	  echo "Error sending message to $toEmail : ".$msg->getLastError();
 *   	}
 *   	else
 *   	{
 *	  echo "Message sent successfully to $toEmail \n";
 *   	}
 *   }
 *   $msg->close();
 * </code>
 *
 */


define('MESSAGE_API_VERSION', '2');
define('MESSAGE_API_URL', 'api.messagebus.com/send');
define('MESSAGE_API_ACCOUNTID', '0');

// Note: It is recommended to put the UserID and Key in a MessageAPIConfig.php file
// This will prevent having to edit this file when you update it.
if (!file_exists(dirname(__FILE__).'/MessageAPIConfig.php'))
{
	define('MESSAGE_API_KEY', 'apikey'); // this is secret!  DO NOT SHARE/PUBLISH!
}
else
{
	require_once('MessageAPIConfig.php');
}


/**
 * MessageAPI Class for sending email to MessageBus
 * @author MessageBus Development Team
 */
class MessageAPI
{
	private $lastError;
	private $error;
	private $ch;
	private $curlInfo;
	private $msgParams;
	private $baseParams;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->init();
	}

	/**
	 * Initialize message object and connection information
	 */
	public function init()
	{
		$this->lastError = '';
		$this->error = false;
		$url = 'https://'.MESSAGE_API_URL;
		$this->ch = curl_init();
		curl_setopt($this->ch, CURLOPT_URL, $url);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->ch, CURLOPT_USERAGENT, 'Mail Bypass API - PHP ' . phpversion());
		curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($this->ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, true);

		$header = array("MESSAGE_API_KEY: ".MESSAGE_API_KEY, "MESSAGE_API_USERID: ".MESSAGE_API_ACCOUNTID);
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		$this->curlInfo = curl_getinfo($this->ch);
		
		$this->baseParams = array("version" => MESSAGE_API_VERSION,
							"operation" => "sendEmail",
							"accountId" => MESSAGE_API_ACCOUNTID,
							"apiKey" => MESSAGE_API_KEY,
							"mid" => '');
		
		$this->msgParams = array("toEmail" => '',
							"toName" => '',
							"subject" => '',
							"body" => '',
							"fromName" => '',
							"fromEmail" => '',
							"tag" => '',
							"plainText" => '1',
							"priority" => '3',
							"charset" => '',
							"encoding" => '',
							"mimeVersion" => '',
							"replyTo" => '',
							"errorsTo" => '',
							"unsubscribeEmail" => '',
							"unsubscribeURL" => '');
		return;
	}

	/**
	 * Set URL for Message sending
	 * Note: Should be used for testing only!
	 * @param string $url
	 */
	public function setURL($url)
	{
		if (preg_match('/^(https):\/\//', $url))
		{
			curl_setopt($this->ch, CURLOPT_URL, $url);
		}
		$this->curlInfo = curl_getinfo($this->ch);
		return;
	}

	/**
	 * Get Curl Information (stats from curl call)
	 * @return array
	 */
	public function getCurlInfo()
	{
		return $this->curlInfo;
	}

	/**
	 * Last Error Message
	 * @return string
	 */
	public function getLastError()
	{
		return $this->lastError;
	}

	/**
	 * Close Curl Connection
	 */
	public function close()
	{
		curl_close($this->ch);
		return;
	}

	/**
	 * Send message
	 * @param array $args
	 * @return boolean
	 */
	public function send(array $params)
	{

		$this->lastError = '';
		// Check that valid parameters are passed into method
		foreach ($params as $key => $val)
		{
			if (array_key_exists($key, $this->msgParams))
			{
				$this->msgParams[$key] = $val;
			}
			else
			{
				$this->lastError = "Parameter is invalid for message.";
				return false;
			}
		}
		$postParams = array();
		foreach ($this->baseParams as $key => $val)
		{
			$postParams[] = $key.'='.urlencode($val);
		}
		foreach ($this->msgParams as $key => $val)
		{
			$postParams[] = $key.'='.urlencode($val);
		}
		$postString = implode('&', $postParams);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postString);
		$curlResult = curl_exec($this->ch);
		$curlErr = curl_error($this->ch);
		$this->curlInfo = curl_getinfo($this->ch);
		$result = explode(':', $curlResult, 2);
 		if (!is_array($result))
		{
			$this->lastError = 'Failed to connect to Message Bus API Server. Details: "'.$result.'"';
			$result = array();
		}
		else
		{
			if ($result[0] != "OK")
			{
				$this->lastError = implode(' ', $result).' '.$curlErr;
			}
		}
		return ($this->lastError == '');
	}

}

/**
 * Static implementation of MessageAPI class
 * @author MessageBus Development Team
 */
class SingleMessageAPI
{
	private static $errorMessage;

	/**
	 * Send message
	 * @param array $params
	 * @param string $url (optional)
	 * @return boolean
	 */
	static public function send(array $params, $url = MESSAGE_API_URL)
	{
		$msg = new MessageAPI();
		if ($url != MESSAGE_API_URL)
		{
			$msg->setURL($url);
		}
		$rc = $msg->send($params);
		SingleMessageAPI::$errorMessage = $msg->getLastError();
		$msg->close();
		return $rc;
	}

	/**
	 * Get Last Error from message send
	 */
	static public function getLastError()
	{
		return SingleMessageAPI::$errorMessage;
	}
}

/*
 * The API requires the use of SSL to send messages
 * OpenSSL and Curl are required in order to send messages to the Message Bus API
 */
$requiredExtensions = array('openssl', 'curl');
foreach ($requiredExtensions AS $ext) {
    if (!extension_loaded($ext)) {
    	die ("Extension $ext required");
    }
}

?>