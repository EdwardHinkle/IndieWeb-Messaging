<?php
include('../config.php');

// Add a leading http:// if it doesn't exist
if(!preg_match('|^https?://|', $_POST['to'])) {
	$to = 'http://' . $_POST['to'];
} else {
	$to = $_POST['to'];
}

// Make sure there are no slashes, otherwise it's not a valid "to" address
if(strpos($to, '/', 8) !== FALSE) {
	die('Error: The "to" address must be only a domain name with no path.' . "\n");
}

// Generate a unique message ID and store it so the message can be verified later
$message_id = date('YmdHis') . '-' . sha1(microtime(TRUE) . $_POST['to'] . mt_rand());

file_put_contents('../sent/' . $message_id . '.txt', $to . "\n\n" . $_POST['text']);

$params = array(
	'from' => $_SERVER['SERVER_NAME'],
	'text' => $_POST['text'],
	'message_id' => $message_id
);

$ch = curl_init($to . '/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
$output = curl_exec($ch);
	
echo $output;
