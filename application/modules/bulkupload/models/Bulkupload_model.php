<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class Bulkupload_model extends MY_Model {

	protected $firestoreObj;
	
    public function __construct() {
        parent::__construct();
    	$this->load->library('firestore');
    	$this->firestoreObj 		=	$this->firestore;
    }
    /**
    ** get Questions
    **/
    public function getAllQuestion() {
         $questionArr            =   $this->firestoreObj->getWhere('bulkQuestion','approvalStatus','=',false,500);
        if ($questionArr && !empty($questionArr)) {
            $this->results          =   $questionArr;
        }

        return $this->results;
    }
        /**
    ** create question using API
    **/
    public function addQuestionByAPI($data,$AuthParam) {
        $url               =   base_url().'api/question';

        $result             =   $this->BulkPostCurlResponse($url,$data,$AuthParam);

        $createQuestion     =     $this->bulkPrepareCurlResponse($result);

        return $createQuestion;
    }

    protected function BulkPostCurlResponse($url,$post,$AuthParam) {
        $post             =   json_encode($post); // Encode the data array into a JSON string
        /* Init cURL resource */
        $curl = curl_init($url); // Prepare the authorisation token
        curl_setopt_array($curl, array(
            CURLOPT_HTTPHEADER      => array('Content-Type: application/json','app_id:'.$AuthParam['app_id'],'api_key:'.$AuthParam['api_key'],'secret:'.$AuthParam['secret']),
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
    public function bulkPrepareCurlResponse( $response ) {
        json_decode($response);
        if(json_last_error() == JSON_ERROR_NONE) {
            $responseObj        =   json_decode($response);
            return ['STATUS'=>200, 'MESSAGE'=>$responseObj];
        } else {
            return ['STATUS'=>500,'MESSAGE'=>$response];
        }
    }
        /**
    ** get User Authentication Details
    **/
    public function getUserAuthData($userId) {
        $authArr            =   $this->firestoreObj->getWhere('webApi','user_id','=',$userId,500);
        if ($authArr && !empty($authArr)) {
            $this->results          =   $authArr;
        }
        return $this->results;
    }
        /**
    ** update Approval Status of uploaded Question
    **/
    public function updateApprovalStatusApi($questionId,$data) {
        $updateQuestion     =   $this->firestoreObj->updateDocument('bulkQuestion',$questionId,$data);
        return $updateQuestion;
    }
        /**
    ** get question By Id
    **/
    public function getQuestionById($questionId) {
        $questionArr    =   $this->firestoreObj->getListById('bulkQuestion','questionId',$questionId);        
        if($questionArr) {
            return $questionArr;
        }
        return false;
    }

     /**
     ** add questions
    **/
    public function submitQuestion($data) {
        $submitQuestion     =   $this->firestoreObj->newDocument('bulkQuestion',$data);
        return $submitQuestion;
    }
    /**
     ** update questions
    **/
    public function updateQuestion($questionId,$data) {
        $updateQuestion     =   $this->firestoreObj->updateDocument('bulkQuestion',$questionId,$data);
        return $updateQuestion;
    }
}
		