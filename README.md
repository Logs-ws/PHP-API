[Logs.ws](http://logs.ws/) - PHP API Library
==================================================

This library can be used in your PHP script to send your application error logs to the Logs.ws server.

**(c) 2013-2014 Logs.ws**

<hr>

# How To use this library #

## Step 1: ##
Download the PHP API Library “Logs.php” from 
<code>https://github.com/Logs-ws/PHP-API</code>

## Step 2: ##
Open _php.ini_ and add the path of the _Logs.php_ class file in the _auto_prepend_file_ directive.
Learn more about [auto_prepend_file](http://docs.php.net/manual/en/ini.core.php#ini.auto-prepend-file)
```php
auto_prepend_file = "path/to/Logs.php"
```

Alternatively, if you do not have access to _php.ini_, you can create _.htaccess_ file on your project root and add the following line in it.
```php
php_value  auto_prepend_file "path/to/Logs.php"
```


## Step 3: ##
Open _Logs.php_ class file and change the following setting with your Logs.ws API key. Your API key can be found at the _Account Settings_ page.
```php
<?php const API_KEY = 'YOUR-API-KEY'; ?>
```

You can also set the value for _DEBUG_ to **true** if you want to display errors on your site.
```php
<?php const DEBUG = true; ?>
```

## Step 4: ##
With the completion of the above 3 steps, you are done setting up the library and its ready to use. You do not have to include the _Logs.php_ class file anywhere as it will be included automatically as per the settings done in Step 2 above. To see it in action, open any php page inside your project and write a code that would generate an error. 

For example, let us see an example of generating a fatal error. Lets call a function that has not yet defined.

```php
<?php 
	TestFunc(); 
?>
```

Once you execute the above script, it will silently (assuming _DEBUG_ is set to **false**) send the error log to your Logs.ws account. You can see the log from your account dashboard.

## Sending Logs Manually: ##

You can also manually send your logs using the **Send()** method. You do not have to create an instance of the Logs class since its already created inside the _Logs.php_ file.

```php
<?php 
    try{
        // Try code here..
    } catch(Exception \$e){
        \$type = 'INFO'; //Optional. Possible values INFO, WARNING, ERROR.
        \$Log->Send(\$e->getMessage(), \$type); 
    }
?>
```