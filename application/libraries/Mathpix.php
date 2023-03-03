<?php
/**
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Mathpix {
    
    protected $mathPixAppId;

    protected $mathPixAppKey;

    protected $mathPixBaseUrl;

    public function __construct()
    {
        // Assign the CodeIgniter super-object
        $this->CI =& get_instance();
        $this->CI->config->load('mathpix');
        $this->mathPixAppId     =   $this->CI->config->item('mathPixAppId');
        $this->mathPixAppKey    =   $this->CI->config->item('mathPixAppKey');
        $this->mathPixBaseUrl   =   $this->CI->config->item('mathPixBaseUrl');
    }

    public function postCurlResponse($url='',$post = array()) {
        /* API URL */
        $url            =   $this->mathPixBaseUrl.$url;
        $post           =   json_encode($post); // Encode the data array into a 
        
        /* Init cURL resource */
        $curl = curl_init($url);
                
        $headers        =   [
            'Content-Type: application/json',
            'app_id: '.$this->mathPixAppId,
            'app_key: '.$this->mathPixAppKey
        ];

        curl_setopt_array($curl, array(
            CURLOPT_HTTPHEADER      => $headers,
            CURLOPT_URL             => $url,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => "",
            CURLOPT_TIMEOUT         => 30000,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => 'POST',
            CURLOPT_POSTFIELDS      => $post
        ));
            
        /* execute request */
        $result = curl_exec($curl);
             
        /* close cURL resource */
        curl_close($curl);
        return $result;
    }
}