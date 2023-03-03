<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class Answer_model extends MY_Model {

	protected $firestoreObj;
	
    public function __construct() {

        parent::__construct();
    	$this->load->library('firestore');
    	$this->firestoreObj 		=	$this->firestore;
    }

    public function getQuestion($courseId) {

    	$questionArr 			= 	$this->firestoreObj->getList();
    	foreach ($questionArr as $document) {
		    if ($document->exists()) {
		        $this->results[] 		=	$document->data();
		    }
		}

		return $this->results;
    }

    public function getQuestionAnswers( $questionId ) {
    	$answerRes 			=	$this->firestoreObj->getWhere('answers','questions',"array-contains",$questionId);
    	if( count($answerRes) > 0 ) {
    		return $answerRes;
	    }

	    return $this->results;
    }

    public function courseQuestionCount($courseId) {
    	$questionArr 			= 	$this->firestoreObj->getWhere('questions','courseId','==',$courseId);

	    if ($questionArr && !empty($questionArr)) {
	        $this->results			=	$questionArr;
	    }

		return count($this->results);
    
    }

    public function upVoteAnswer($answeId) {
        $url                =   "answer/".$answeId."/up-vote";
        $addQuestionAns     =   $this->postCurlResponse($url);
        
        $this->results      =   $this->prepareCurlResponse($addQuestionAns);

        return $this->results;

    }

    public function downVoteAnswer($answeId) {
        $url                =   "answer/".$answeId."/down-vote";
        $addQuestionAns     =   $this->postCurlResponse($url);

        $this->results      =   $this->prepareCurlResponse($addQuestionAns);

        return $this->results;

    }

    public function addQuestionAnswer($data) {
        $url                =   'answer';
        $addQuestionAns     =   $this->postCurlResponse($url,$data);

        $this->results      =   $this->prepareCurlResponse($addQuestionAns);

        return $this->results;
    }

    public function deleteAnswer($answerId,$questionId) {
        $url                =   'delete/answer/'.$questionId."/".$answerId;
        $result             =   $this->deleteCurlResponse($url);
        $this->results      =   $this->prepareCurlResponse($result);
        
        return $this->results;   
    }


}
		