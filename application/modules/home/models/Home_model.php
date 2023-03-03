<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class Home_model extends MY_Model {

	protected $firestoreObj;
	
    public function __construct() {

        parent::__construct();
    }

    public function createNew() {
        $data = array(
                "name"=>"shafeeque",
                "email"=>"shafeeque@trymph.com",
                "message"=>"Test doc with autoid"
            );
        $res = $this->firestoreObj->newDocument("enquiries",$data);
    }

    public function listQuestion($courseId) {

    	$questionArr 			= 	$this->firestoreObj->getList();
    	foreach ($questionArr as $document) {
		    if ($document->exists()) {
		        $this->results[] 		=	$document->data();
		    }
		}

		return $this->results;
    }

}