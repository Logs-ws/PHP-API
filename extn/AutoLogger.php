<?php
require_once __DIR__ . "/../lib/Logs.php";

class AutoLogger extends Logs
{
  // Specify your API key here.
  const API_KEY = 'MKBit2Zkl3bOEdu';
  //const API_KEY = 'YOUR-API-KEY';

  // Format of the return data (JSON/XML). Not very useful unless you want to see whats being returned
  // from the logs.ws server
  const FORMAT = 'JSON';

  // Set to TRUE if you wish to see errors on your page, else set it to FALSE.
  const DEBUG = false;

  // Different types of errors handled by the library. Please do not change these unless you know what you are doing.
  const TYPE_PARSE = 'Parse error';
  const TYPE_FATAL = 'Fatal error';
  const TYPE_NOTICE = 'Notice';
  const TYPE_STRICT = 'Strict Standards';
  const TYPE_WARNING = 'Warning';
  const TYPE_RECOVERABLE = 'E_RECOVERABLE_ERROR';

  public function __construct() 
  {
      parent::__construct(self::API_KEY, self::FORMAT);

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
    $error = $errorMessage . " in " . $errorFile . " on line " . $errorLine;

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

    $error = $lastError['message'] . " in " . $lastError['file'] . " on line " . $lastError['line'];

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
}

// Create an instance of the class so that the error handlers get active for handling the errors.
new AutoLogger();