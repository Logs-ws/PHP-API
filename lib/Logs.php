<?php
/**
 * Logs.ws PHP API Library
 *
 * LICENSE: Logs.ws
 *
 * @category   Logs.ws
 * @package    API Library
 * @copyright  Copyright (c) 2013 Logs.ws (https://logs.ws)
 * @version    1.1
 * @since      File available since Release 1.0
 *
 *
 *  This library file is used to send API request to the logs.ws server
 *  to send / receive logs of the user's application
 *
 * @category   Logs.ws
 * @package    API Library
 * @copyright  Copyright (c) 2013 Logs.ws (https://logs.ws)
 * @version    Release:  1.1
 * @since      Class available since Release 1.0
 * @author Shouvik Chatterjee (shouvik@logs.ws)
 */


class Logs
{
    // Specify your API key here.
    const API_KEY = 'YOUR-API-KEY';

    // Format of the return data (JSON/XML). Not very useful unless you want to see whats being returned from the logs.ws server
    const FORMAT = 'JSON';

    // Set to TRUE if you wish to see errors on your page, else set it to FALSE.
    const DEBUG = false;

    // The URL of the API engine
    const API_URL = 'https://logs.ws/api/1.1/';

    // Different types of errors handled by the library. Please do not change these unless you know what you are doing.
    const TYPE_PARSE = 'Parse error';
    const TYPE_FATAL = 'Fatal error';
    const TYPE_NOTICE = 'Notice';
    const TYPE_STRICT = 'Strict Standards';
    const TYPE_WARNING = 'Warning';
    const TYPE_RECOVERABLE = 'E_RECOVERABLE_ERROR';
    
    public function __construct() 
    {
        $this->apikey = self::API_KEY;
        $this->format = self::FORMAT;

        error_reporting(self::DEBUG);
        register_shutdown_function(array($this, 'shutDownHandler'));
        set_error_handler(array($this, 'errorHandler'));
    }

    /*
    * The method will be invoked when an error occurs.  It builds an error message 
    * and calls the logHandler() method and passes the error message and the type of the error.
    *
    * @param int $errorLevel The value of the error.
    * @param string $errorrrMessage The error string
    * @param string $errorFile The file name where the error occured.
    * @param int $errorLine The line number of the error.
    * @param string $errorContext The type of the log (INFO, WARNING, ERROR)
    *
    * @link http://www.php.net/manual/en/errorfunc.constants.php
    */
    public function errorHandler($errorLevel, $errorMessage, $errorFile, $errorLine, $errorContext)
    {
        $error = $this->GetMessage($errorMessage, $errorFile, $errorLine);

        switch ($errorLevel) {
          case E_ERROR:
          case E_CORE_ERROR:
          case E_COMPILE_ERROR:
          case E_PARSE:
          case E_USER_ERROR:
              $this->logHandler($error, self::TYPE_FATAL);
              break;
          case E_PARSE:
              $this->logHandler($error, self::TYPE_PARSE);
              break;
          case E_USER_ERROR:
          case E_RECOVERABLE_ERROR:
              $this->logHandler($error, self::TYPE_RECOVERABLE);
              break;
          case E_WARNING:
          case E_CORE_WARNING:
          case E_COMPILE_WARNING:
          case E_USER_WARNING:
              $this->logHandler($error, self::TYPE_WARNING);
              break;
          case E_NOTICE:
          case E_USER_NOTICE:
              $this->logHandler($error, self::TYPE_NOTICE);
              break;
          case E_STRICT:
              $this->logHandler($error, self::TYPE_STRICT);
              break;
          default:
              $this->logHandler($error, self::TYPE_WARNING);
        }
    }

    /*
    * Is called when the php script ends. It builds an error message 
    * and calls the logHandler() method and passes the error message and the type of the error.
    */
    public function shutDownHandler()
    {
        $lastError = error_get_last();

        $error = $this->GetMessage($lastError['message'], $lastError['file'], $lastError['line']);

        switch ($lastError['type'])
        {
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
            case E_RECOVERABLE_ERROR:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
                $this->logHandler($error, self::TYPE_FATAL);
                break;
            case E_PARSE:
                $this->logHandler($error, self::TYPE_PARSE);
                break;
        }
    }

    /*
    * This method will build up a message that will serve as a log text.
    *
    * @param string $errorMessage The error message
    * @param string $errorFile The file name
    * @param string $errorLine The line number of the error
    */
    private function GetMessage($errorMessage, $errorFile, $errorLine){
        return $errorMessage . " in " . $errorFile . " on line " . $errorLine;
    }

    /*
    * This method is invoked from the error handlers. It pushes the log to the log.ws server by
    * invoking the Send() method from the parent class.
    *
    * @param string $error The log message to be sent
    * @param string $errorType The php error type.
    */
    private function logHandler($error, $errorType)
    {
        switch ($errorType) {
          case self::TYPE_PARSE:
            $type='ERROR';
            break;
          case self::TYPE_FATAL:
            $type='ERROR';
            break;
          case self::TYPE_NOTICE:
            $type='INFO';
            break;
          case self::TYPE_STRICT:
            $type='WARNING';
            break;
          case self::TYPE_WARNING:
            $type='WARNING';
            break;      
          case self::TYPE_RECOVERABLE:
            $type='ERROR';
            break;                                              
          default:
            $type='INFO';
            break;
        }

        $this->Send($error, $type);     
    }

    /*
    * Post the data to Logs.ws server using CURL
    *
    * @param string $log The log message to be sent
    * @param string $type The type of the log (INFO, WARNING, ERROR)
    *
    * @return xml|json
    */
    public function Send($log='', $type='INFO')
    {
        // Check if CURL is enabled on the server.
        if( ! function_exists('curl_version')){
             throw new Exception('Curl is not installed on this server');
        }
        
        $log = trim($log);
        $type = ($type) ? trim(strtoupper($type)) : 'INFO';
        $types = array('INFO', 'WARNING', 'ERROR');
        
        if( ! in_array($type, $types)){
            $type = 'INFO';
        }
        
        if (strlen($log) < 1 ) {
            throw new Exception('Log message is empty. A log should at least be 1 char in length.');
        }   
        
        $data = array();        
        $data['apikey']= $this->apikey;
        $data['format']= $this->format;        
        $data['type'] = $type;        
        $data['log'] = $log;        
        
        $ch = curl_init();

        //Prepare data for curl post
        $curlData='';
        if(is_array($data)) {
            foreach($data as $key=>$value){
                $curlData .= $key . '=' . urlencode($value) . '&';
            }
        }else {
            $curlData=$data;
        }
        
        try {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$curlData);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            curl_setopt($ch, CURLOPT_URL, self::API_URL);
            $data = curl_exec($ch);
            curl_close($ch);

            return $data;      
            
        }  catch (Exception $e){
             echo 'An error occoured: ',  $e->getMessage(), "\n";
        }
    }
}

// Create an instance of the class so that the error handlers get active for handling the errors.
$Log = new Logs();