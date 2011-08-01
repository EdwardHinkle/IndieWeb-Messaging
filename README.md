Setup
=====

Pull this project down into a folder on your domain called "message".

In your top level .htaccess file, put this rule:

    RewriteCond %{REQUEST_METHOD} ^POST
    RewriteRule ^$ /message/receive.php [QSA,L]

This will direct any POST requests to the "/" path to the receive.php script.

Make the "sent" and "received" folders writable by the web server so that it can
store messages to those folders.

Copy config.template.php to config.php and add your email address.


Login
=====

Run this to create a password file

    $ htdigest -c IndieWeb.htpasswd IndieWeb yourusername

Edit the .htaccess file in the "new" folder to point to this file. If you put the file in /etc/httpd/
then you won't need to update the .htaccess file.

Feel free to secure this any other way, or even integrate it into your own framework if you already 
have a way to log in securely.


Sending a Message
=================

Visit http://example.com/message/new/ to compose a new message. Enter the destination domain in
the "to" field. Your server will store the sent message in the "sent" folder. The remote server
will then send a query back and ask your server if it sent a matching message.

If you don't use the "/message/new/" file to send messages, you'll just have to put the logic that
exists in the "new/send.php" script into somewhere in your environment. The core of its logic is:
* Generate a unique message ID
* Store the message somewhere (like on disk) where the receive.php message can look it up later


Receiving a Message
===================

Remote servers will make requests to http://example.com/, and the rewrite rule above will direct those
POST requests to the "receive.php" script. The receive.php script will make a request back to the 
sending server to verify the message ID matches one it knows about.

It will attempt to send the message to you via email if you've defined your email address in the 
config file, or it will send via Tropo if you have a Tropo token entered.

You can easily define other destinations for the message in this script.


Configuring Tropo
=================

To send received messages to you via SMS, you'll need to create a quick Tropo app. Create an account
at http://tropo.com, this service is completely free for development. Create a new Scripting app
and name it send_sms.rb. Paste in the following code, which will allow this messaging project to forward
SMSs to your phone number.

```ruby
message($message, { 
    :to => "+1"+$number, 
    :network=>'SMS'
    })
```

Add a phone number to your Tropo app. Finally, copy the outbound messaging token from tropo.com and paste 
it into the config file. Now you will get messages sent to your domain forwarded to your cell phone!


