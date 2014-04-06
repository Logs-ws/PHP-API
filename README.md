[Logs.ws](http://logs.ws/) - PHP API Library
==================================================

This library can be used in your PHP script to send your application logs to the Logs.ws server.

**(c) 2013-2014 Logs.ws**

<hr>

# How To use this library #

## Step 1: ##
Download the PHP API Library “Logs.php” from 
<code>https://github.com/Logs-ws/PHP-API</code>

## Step 2: ##
Include the "Logs.php" file that you have downloaded.
```php
<?php
include 'path/to/lib/Logs.php';
?>
```

## Step 3: ##
Initialize the Logs object with your Logs.ws API KEY and your prefered format (json/xml). API key can be found at <code>http://logs.ws/user-settings</code>
```php
<?php
$Log = new Logs('your-api-key', 'json');
?>
```


## Step 4: ##
With the help of Logs object start sending your logs:
```php
<?php
  $logData = 'Example log text...'; 
  $type = 'INFO'; //Optional. Possible values INFO, WARN, ERROR. Default is INFO.
  $Log->Send($logData, $type); 
?>
```
