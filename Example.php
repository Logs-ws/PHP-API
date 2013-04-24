<?php
require 'Logs.php';

$obj = new Logs('your-api-key', 'json');

//Log that you want to send to the logs.ws server
$log ='Test log.'; 

//Send the log to the logs.ws server using the sendRequest() method that accepts two arguments. 
//allowed log types are "INFO", "WARNING", "ERROR"
echo $obj->sendRequest($log, 'INFO'); 
?>
