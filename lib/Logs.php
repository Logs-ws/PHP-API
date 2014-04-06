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
    const API_URL = 'https://logs.ws/api/1.1/';
    
    public function __construct($apikey, $format) 
    {
        if (empty($apikey)) {
            throw new Exception('API Key Missing. Please specify a valid API key.');
        }

        $this->apikey = $apikey;
        $this->format = $format;
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
            //Set curl to return the data instead of printing it to the browser.            
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