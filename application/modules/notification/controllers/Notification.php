<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification extends MY_Controller {

    public function __construct(){
    	parent::__construct();
    	$this->load->model('user/User_model','user_model');
    	//set template params
    	$this->data_arr['_module_name'] 		=	'user';
		$this->data_arr['_load_page'] 			=	array('user_home');
		$this->input_data 		= 	$this->input->post();
		$this->action 			=	isset($this->input_data['ACTION'])?$this->input_data['ACTION']:'';
        $this->data_arr['_page_name']       =   'user_home';
    }

	/**
    ** User index
    **/
    public function index() {
    	switch ($this->action) {
    		case 'LOGIN':
    			$this->auth();
    			break;
            default:    
    			$this->listPublicCourse();
    			$this->getUserCourse();
    			break;
    	}
    	$action 			=	'';
    	$this->prepareResult();
    }

    /**
    **/
    protected function auth() {
    	$user_name 		=	'shafeeque@asterbyte.com';//$this->input_data['user_name'];
    	$password 		=	'123456';//$this->input_data['password'];
    	$userArr 		=	$this->user_model->authUser($user_name,$password);
    	if( $userArr ) {
    		$this->autherizeUser($userArr);
    	}
    	if( $this->isUserLoggedin() ) {
    		$this->status 	=	'200';
    		$this->message 	=	'Logged in success';
    		$this->error 	=	'';
    	}
    }

    /**
    **/
    protected function listPublicCourse() {
        $courseArr              =   $this->course_model->listCourse();
        $publicCourse 			=	[];
        if( is_array($courseArr) && !empty($courseArr) ) {
        	foreach ($courseArr as $key => $course) {
        		if( !isset($course['privateList']) || empty($course['privateList']) ) {
        			array_push($publicCourse, $course);
        		}
        	}
        }
        $this->data_arr['public_course']     =   $publicCourse;
    }

    protected function getUserCourse() { 
    	$userId 		=	$this->data_arr['user_details']['uid'];
    	$userCourseArr 	=	$this->course_model->getUserCourse($userId);
    	$this->data_arr['user_course_list'] 	=	$userCourseArr;
    }

    /**
    ** prepare result
    ** @param $user_arr
    ** @return template page
    **/
    protected function prepareResult() {
        if( isset($this->input_data['AJAX'])) {
            if($this->input_data['TYPE'] == 'DT' ) {
                $this->data_arr['_load_page']           =   isset($this->input_data['LOAD_PAGE'])?array($this->input_data['LOAD_PAGE']):$this->dt_page;
                $this->data_arr['table_data']           =   $this->data_arr['locationArr'];
                $this->template->datatable_template( $this->data_arr );
            } else if( $this->input_data['TYPE'] == 'ACTION' ) {
                $this->response['STATUS']       =   $this->status;
                $this->response['MESSAGE']      =   $this->message;
                $this->response['ERROR']        =   $this->error;
                print_r(json_encode($this->response));die();
            }
        } else {
        	// p($this->data_arr,"cp");
            $this->template->user_template( $this->data_arr ); 
        }
    }
}
