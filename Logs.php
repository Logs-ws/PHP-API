<?php
/**
 * Logs.ws PHP API Library
 *
 * LICENSE: Logs.ws
 *
 * @category   Logs.ws
 * @package    API Library
 * @copyright  Copyright (c) 2013 Logs.ws (https://logs.ws)
 * @version    1.0
 * @since      File available since Release 1.0
 *
 *
 *  This library file is used to send API request to the logs.ws server
 *  to send logs of the user's application
 *
 * @category   Logs.ws
 * @package    API Library
 * @copyright  Copyright (c) 2013 Logs.ws (https://logs.ws)
 * @version    Release:  1.0
 * @since      Class available since Release 1.0
 * @author Shouvik Chatterjee (shouvik@logs.ws)
 */
class Logs
{
    
    private $_url = 'https://logs.ws/api/1.0/';
    
    public function __construct($apikey, $mode) {
        $this->apikey = $apikey;
        $this->mode = $mode;
    }
    
    /*
    * Function to post data to Logs.ws server using CURL
    * $data = array
    * returns xml | json
    */
    public function sendRequest($log='', $type='INFO'){
        
        //Check if CURL is enabled on the server.
        if( ! function_exists('curl_version')){
            return 'Curl is not installed on this server';
        }
        
        $log = trim($log);
        $type = ($type) ? trim(strtoupper($type)) : 'INFO';
        $types = array('INFO', 'WARNING', 'ERROR');
        
        if( ! in_array($type, $types)){
            $type = 'INFO';
        }
        
        if( strlen($log) < 1 ) {
            return 'Nothing to send.';
        }
        
        $data = array();        
        $data['apikey']= $this->apikey;
        $data['format']= $this->mode;        
        $data['type'] = $type;        
        $data['log'] = $log;        
        
        $ch = curl_init();

        //Prepate data for curl post
        $curlData='';
        if(is_array($data)){
            foreach($data as $key=>$value){
                $curlData .= $key . '=' . urlencode($value) . '&';
            }
        }else{
            $curlData=$data;
        }
        
        try {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
            curl_setopt($ch, CURLOPT_POST,1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$curlData);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);            
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
            curl_setopt($ch, CURLOPT_URL, $this->_url);
            $data = curl_exec($ch);
            curl_close($ch);

            return $data;      
            
        }  catch (Exception $e){
             echo 'The following error occoured: ',  $e->getMessage(), "\n";
        }

        
        
    }
    
    
}
