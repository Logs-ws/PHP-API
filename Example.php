<?php
require 'Logs.php';

$obj = new Logs('YOUR-API-KEY-HERE', 'json');

// Log that you want to send to the logs.ws server
$log ='Test log.'; 

// Send the log to the logs.ws server/
echo $obj->Send($log, 'INFO'); 
?>
