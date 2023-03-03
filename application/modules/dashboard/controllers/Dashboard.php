<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

    public function __construct(){
    	parent::__construct();
    	$this->load->model('user/User_model','user_model');
    	//set template params
    	$this->data_arr['_module_name'] 		=	'dashboard';
		$this->data_arr['_load_page'] 			=	array('user_dashboard');
		$this->input_data 		= 	$this->input->post();
		$this->action 			=	isset($this->input_data['ACTION'])?$this->input_data['ACTION']:'SHOW_DASHBOARD';
        $this->data_arr['_page_name']       =   'user_dashboard';
    }

	/**
    ** User index
    **/
    public function index() {
    	switch ($this->action) {
    		case 'LOGIN':
    			$this->auth();
			break;
    		case 'SHOW_DASHBOARD':
				$this->data_arr['page_title'] 	=	'Title';
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
            if($this->input_data['TYPE'] == 'DT' || $this->input_data['TYPE'] == 'HTM') {
                $this->data_arr['_load_page']           =   isset($this->input_data['LOAD_PAGE'])?array($this->input_data['LOAD_PAGE']):$this->dt_page;
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
