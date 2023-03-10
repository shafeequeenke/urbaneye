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

    public function add_enquiry($postData) {
        $data       = $postData;
        $data['created_date']       = date('Y-m-d');
        $data['status']             = 'Send';
        $insert     = $this->db->insert('enquiry',$data);
        if($insert) {
            return $this->db->insert_id();
        } else {
            return false;
        }
    }

    public function getAllEnquiry() {
        $results    = [];
        $this->db->select("*");
        $this->db->from('enquiry');
        $this->db->order_by('enquiry_id','desc');
        $query = $this->db->get();
        if($query->num_rows() > 0) {
            $results    = $query->result_array();
        }
        return $results;
    }

    public function listQuestion($courseId) {
		return $this->results;
    }

}