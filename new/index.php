<!DOCTYPE html>
<html>
<head>
	<title>Send an IndieWeb Message</title>
</head>
<body>

<form action="send.php" method="post">
<table cellpadding="3" border="1" style="border-collapse: collapse; width: 480px;">
	<tr>
		<td>To (Domain)</td>
		<td><input type="text" name="to" id="to" style="width: 300px;" /></td>
	</tr>
	<tr>
		<td>Text</td>
		<td><input type="text" name="text" id="text" style="width: 300px;" /></td>
	</tr>
	<tr>
		<td colspan="2">
			When you click send, your message will be stored on this server and assigned a
			unique message ID. The message will then be sent to the remote server.
		</td>
	</tr>
	<tr>
		<td colspan="2" align="right"><input type="submit" value="Send" /></td>
	</tr>
</table>
</form>

</body>
</html>