<?php
include('config.php');

if(array_key_exists('from', $_POST) && array_key_exists('text', $_POST)) {
	
	// Validate the "from" address looks like a domain name with no path

	// Add a leading http:// if it doesn't exist
	if(!preg_match('|^https?://|', $_POST['from'])) {
		$from = 'http://' . $_POST['from'];
	} else {
		$from = $_POST['from'];
	}

	// Make sure there are no slashes, otherwise it's not a valid "from" address
	if(strpos($from, '/', 8) !== FALSE) {
		msg('Error: Your "from" address must be only a domain name with no path.');
	}

	// By this point, we can be sure the "from" address is only the hostname part
	$domain = preg_replace('|^https?://|', '', $from);
	
	$verified = FALSE;

	// If a 'message_id' parameter is present, query the sender to ask if this message came from them
	if(array_key_exists('message_id', $_POST)) {
		$ch = curl_init('http://' . $from . '/');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('message_id' => $_POST['message_id'])));
		$output = curl_exec($ch);
		
		// If the remote server did not confirm the message, don't send it.
		if($output == 'denied') {
			msg('Error: "From" domain (' . $from . ') did not confirm the message');
		}
		
		$verified = TRUE;
	}

	$msg = $domain . ': ' . $_POST['text'];

	if($verified)
		sendAndCloseConnection("Success: Your message was verified and sent!\n");
	else
		sendAndCloseConnection("Success: Your message was sent without verification.\n");
	
	// Save to a file
	$filename = date('YmdHis') . '-' . $domain . '.txt';
	$contents = $from . "\n" 
		. ($verified ? $_POST['message_id'] . "\n" : '') 
		. "\n" . $_POST['text'];
	file_put_contents('received/' . $filename, $contents);
	
	// Send the SMS via Tropo
	$params = array(
		'action' => 'create',
		'token' => TROPO_TOKEN,
		'text' => $msg
	);
	file_get_contents('https://api.tropo.com/1.0/sessions?' . http_build_query($params));
}
else if(array_key_exists('message_id', $_POST)) {
	// A remote server is asking us to verify that we sent the specified message
	
	// Simple version is implemented by looking for a matching file in the filesystem
	
	// Sanitize the message ID. We generated it so it's safe to do.
	$message_id = preg_replace('/[^a-z0-9-]/', '', $_POST['message_id']);
	
	if(file_exists('sent/' . $message_id . '.txt')) {
		msg('confirmed');
	} else {
		msg('denied');
	}
}
else {
	msg('Error: Send a POST request with fields "from" and "text"');
}


function msg($msg) {
	echo $msg . "\n";
	die();
}

function sendAndCloseConnection($msg) {
	// Close the user's browser connection but keep the PHP script running
	// See http://www.php.net/manual/en/features.connection-handling.php#71172
	ob_end_clean();
	header("Connection: close");
	ignore_user_abort(); // optional
	ob_start();

	echo $msg;

	$size = ob_get_length();
	header("Content-Length: $size");
	ob_end_flush();
	flush();
}
