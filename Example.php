<?php
require 'lib/Logs.php';

$Log = new Logs('YOUR-API-KEY-HERE', 'json');

// Log that you want to send to the logs.ws server
$logData ='Test log.'; 

// Send the log to the logs.ws server/
echo $Log->Send($logData, 'INFO'); 
?>
