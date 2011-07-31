<!DOCTYPE>
<html>
<head>
	<title>IndieWeb Messaging</title>
</head>
<body>

<h2>About</h2>


<h2>.htaccess</h2>
<pre>
RewriteCond %{REQUEST_METHOD} ^POST
RewriteRule ^$ /message/receive.php [QSA,L]
</pre>

</body>
</html>