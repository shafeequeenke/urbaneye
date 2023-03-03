<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model  extends CI_Model

{

	protected $results 		=	array();

	protected $firebaseDb;

	protected $firebaseObj;

	protected $firebaseAuth;

	protected $firebaseBaseurl;

	public function __construct($param = array())

	{
		parent::__construct();

    //   $this->load->library('firebase');
    //   //initializeFirebase
	// 	  $this->firebaseObj 	= 	$this->firebase->init();
	// 	  //create Autherization object
	// 	  $this->firebaseAuth 		= 	$this->firebaseObj->getAuth();
    //   $this->firebaseDb 			= 	$this->firebaseObj->getDatabase();
    //   $this->firebaseBaseurl 	=	$this->config->item('firebaseBaseurl');
	}

	public function createFirebaseUser($userProperties) {
		$createdUser = $this->firebaseAuth->createUser($userProperties);
		if($createdUser) {
			return $createdUser;
		} else {
			return false;
		}
	}

	protected function postCurlResponse($url='',$post = array()) {
		  /* API URL */
      $url  			= 	$this->firebaseBaseurl.'/api/'.$url;
      $post 			= 	json_encode($post); // Encode the data array into a JSON string
   		$userSession 	=	$this->session->userdata('user_data');
   		$token 			=	$userSession['access_token'];
   		if( !$token )
   			return false;

        /* Init cURL resource */
        $curl = curl_init($url);
        $authorization = "Authorization: Bearer ".$token; // Prepare the authorisation token
   
       	curl_setopt_array($curl, array(
       		CURLOPT_HTTPHEADER 		=> array('Content-Type: application/json',$authorization ),
		    CURLOPT_URL 			=> $url,
		    CURLOPT_RETURNTRANSFER 	=> true,
		    CURLOPT_ENCODING  		=> "",
		    CURLOPT_TIMEOUT  		=> 30000,
		    CURLOPT_HTTP_VERSION  	=> CURL_HTTP_VERSION_1_1,
		    CURLOPT_CUSTOMREQUEST  	=> 'POST',
		    CURLOPT_POSTFIELDS 		=> $post
		));
            
        /* execute request */
        $result = curl_exec($curl);
             
        /* close cURL resource */
        curl_close($curl);
        return $result;
	}

	protected function getCurlResponse($url='',$data='') {
   		$userSession 	=	$this->session->userdata('user_data');

   		$token 			=	$userSession['access_token'];
   		if( !$token )
   			return false;
		$params 			=	'';
		$params 			=	is_array($data)?http_build_query($data):$data;

        $url  			= 	$this->firebaseBaseurl.'/api/'.$url.($params != ''?'?'.$params:'');
        /* Init cURL resource */
        $curl = curl_init($url);
        $authorization = "Authorization: Bearer ".$token; // Prepare the authorisation token
   
       	curl_setopt_array($curl, array(
       		CURLOPT_HTTPHEADER => array('Content-Type: application/json',$authorization ),
		    CURLOPT_URL => $url,
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_ENCODING => "",
		    CURLOPT_TIMEOUT => 30000,
		    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		    CURLOPT_CUSTOMREQUEST => 'GET'
		));
            
        /* execute request */
        $result = curl_exec($curl);
             
        /* close cURL resource */
        curl_close($curl);


        return $result;

        /////////////////////////////autherrization bearer token curl///////////////
    	// header('Content-Type: application/json'); // Specify the type of data
     	//   	$ch = curl_init('https://APPURL.com/api/json.php'); // Initialise cURL
     	//   	$post = json_encode($post); // Encode the data array into a JSON string
     	//   	$authorization = "Authorization: Bearer ".$token; // Prepare the authorisation token
     	//   	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization )); // Inject the token into the header
     	//   	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     	//   	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method?$method:'GET'); // Specify the request method as POST
     	//   	curl_setopt($ch, CURLOPT_POSTFIELDS, $post); // Set the posted fields
     	//   	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // This will follow any redirects
     	//   	$result = curl_exec($ch); // Execute the cURL statement
     	//   	curl_close($ch); // Close the cURL connection
     	//   	return json_decode($result); // Return the received data
	}

	/**
	**/
	protected function putCurlResponse($url='',$post='') {
   		/* API URL */
        $url  			= 	$this->firebaseBaseurl.'/api/'.$url;
        $post 			= 	json_encode($post); // Encode the data array into a JSON string
   		$userSession 	=	$this->session->userdata('user_data');
   		$token 			=	$userSession['access_token'];
   		if( !$token )
   			return false;

        /* Init cURL resource */
        $curl = curl_init($url);
        $authorization = "Authorization: Bearer ".$token; // Prepare the authorisation token
   
       	curl_setopt_array($curl, array(
       		CURLOPT_HTTPHEADER 		=> array('Content-Type: application/json',$authorization ),
		    CURLOPT_URL 			=> $url,
		    CURLOPT_RETURNTRANSFER 	=> true,
		    CURLOPT_ENCODING  		=> "",
		    CURLOPT_TIMEOUT  		=> 30000,
		    CURLOPT_HTTP_VERSION  	=> CURL_HTTP_VERSION_1_1,
		    CURLOPT_CUSTOMREQUEST  	=> 'PUT',
		    CURLOPT_POSTFIELDS 		=> $post
		));
            
        /* execute request */
        $result = curl_exec($curl);
             
        /* close cURL resource */
        curl_close($curl);
        return $result;
	}

	protected function deleteCurlResponse($url='',$post='') {
		/* API URL */
        $url  			= 	$this->firebaseBaseurl.'/api/'.$url;
   		$userSession 	=	$this->session->userdata('user_data');
   		$token 			=	$userSession['access_token'];
   		if( !$token )
   			return false;

        /* Init cURL resource */
        $curl = curl_init($url);
        $authorization = "Authorization: Bearer ".$token; // Prepare the authorisation token
   
       	curl_setopt_array($curl, array(
       		CURLOPT_HTTPHEADER 		=> array('Content-Type: application/json',$authorization ),
		    CURLOPT_URL 			=> $url,
		    CURLOPT_RETURNTRANSFER 	=> true,
		    CURLOPT_ENCODING  		=> "",
		    CURLOPT_TIMEOUT  		=> 30000,
		    CURLOPT_HTTP_VERSION  	=> CURL_HTTP_VERSION_1_1,
		    CURLOPT_CUSTOMREQUEST  	=> "DELETE"
		));
            
        /* execute request */
        $result = curl_exec($curl);
             
        /* close cURL resource */
        curl_close($curl);
        return $result;
	}

	/**
	*
	**/
	public function prepareCurlResponse( $response ) {
		json_decode($response);
		if(json_last_error() == JSON_ERROR_NONE) {
			$responseObj		=	json_decode($response);
			return ['STATUS'=>200, 'MESSAGE'=>$responseObj];
		} else {
			return ['STATUS'=>500,'MESSAGE'=>$response];
		}
	}
}