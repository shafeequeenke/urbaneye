<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Course extends MY_Controller {

    public function __construct() {
    	parent::__construct();
        //load required models
    	$this->load->model('course/Course_model','course_model');
    	//set template params
    	$this->data_arr['_module_name'] 		=	'course';
		$this->data_arr['_load_page'] 			=	array('user_course');
        $this->data_arr['_page_name']           =   'user_home';        
		
        $this->data_arr['page_header_name']     =   'User Course';
        $this->data_arr['page_header_link']     =   'course';
        //input parameters
        $this->input_data 		= 	$this->input->post();
        $this->get_data         =   $this->input->get();

        //define actions for ajax request
		$this->action 			=	isset($this->input_data['ACTION'])?$this->input_data['ACTION']:'';

        // if( !$this->isUserLoggedin() && !isset($this->input_data['AJAX'])) {
        //     redirect(base_url());
        // }
        
    }

    /**
    ** User index
    **/
    public function index($courseSlug='') {
    	switch ($this->action) {
    		case 'LOGIN':
    			$this->auth();
    			break;
    		case 'LIST_COURSE':
    			$this->listCourse();
    			break;
            case 'SUBSCRIBE':
                $this->subscribeUserCourse();
                break;
            case 'LIST_USER_COURSE':
                $this->listCourse();
                $this->getUserCourse();
                break;
            case 'COURSE_TAG_LIST':
                $this->courseTagList();
                break;
            default:    
                $this->getCourse($courseSlug);
    			break;
    	}

    	$this->prepareResult();
    }


    /**
    *Course details
    **/
    public function getCourse($courseSlug="") { 
        $this->load->model('question/Question_model','question_model');
        $this->data_arr['_load_page']   =   array('course_single','footer');
        if($courseSlug && $courseSlug != "") {
            $courseId               =   $this->course_model->getCourseIdBySlug($courseSlug);
            if(!$courseId || $courseId == "") {
                //check whether parameter is course id
                $courseDet          =   $this->course_model->getCourseList([$courseSlug]);
                if($courseDet && is_array($courseDet) && count($courseDet)>0) {
                    $courseId       =   $courseDet[0]['id'];
                }
            }
        } else {
            $courseId               =   $this->get_data['course']?$this->get_data['course']:false;
        }

        $load_question              =   "";
        if(isset($this->get_data['question']) && $this->get_data['question'] == "user") {
            $load_question          =   $this->get_data['question'];
            if(!$this->isUserLoggedin()) {
                $load_question      =   'public';
            }
        } else if(isset($this->get_data['question']) && $this->get_data['question'] == "public") {
            $load_question          =   $this->get_data['question'];
        }

        if(isset($this->get_data['page']) && $this->get_data['page'] != "" ) {
            $page_number            =   $this->get_data['page'];
        } else {
            $page_number            =   1;
        }
        $this->data_arr['load_question']    =   $load_question;

        if($courseId || isset($this->input_data['COURSE_ID'])) {
            $course_details             =   array();
            $course_details             =   $this->course_model->getCourseList([$courseId]);

            $courseTitle                =   isset($course_details[0]['department'])?$course_details[0]['department']:''." ".isset($course_details[0]['title'])?$course_details[0]['title']:'';

            $this->data_arr['question_count']       =   $this->question_model->courseQuestionCount($courseId);
            if($this->isUserLoggedin()) {
                $userId         =   $this->data_arr['user_details']['uid'];
                $this->data_arr['my_question_count']    =   $this->question_model->getUserCourseQuestionCount($userId,$courseId);
            }
            $this->data_arr['course_slug']          =   isset($courseSlug)?$courseSlug:$courseId;
            $this->data_arr['page_number']          =   $page_number;
            $this->data_arr['course_details']       =   $course_details[0];
            $this->data_arr['course_id']            =   $courseId;
            $this->data_arr['page_name']            =   'custom';

            $this->data_arr['page_header_name']     =   'Course';
            $this->data_arr['page_header_link']     =   $courseId;
            $this->meta_data                        =   array(
                        'page_name'         =>      'Course',
                        'title'             =>      $courseTitle."-Doubt Help Course",
                        'meta_description'  =>      $courseTitle
            );        
        } else {
            redirect('');
        }
        
        if($courseSlug && $courseSlug != "") {
            $this->prepareResult();
        }
        
    }

    /**
	**
    **/
	protected function listCourse()
	{
		$this->load->model('question/Question_model','question_model');
		$courseArr 		=	$this->course_model->listCourse();
		
		foreach ($courseArr as $key => $course) {
            $questionCount      =   0;
			$questionArr 		=	[];
			if( isset($course['id'])) {
				$questionCount 		=	$this->question_model->getCourseQuestionCount($course['id']);
			}
			$courseArr[$key]['question_count'] 	=	$questionCount;
		}
		$this->data_arr['course_list'] 		=	$courseArr;
	}

    /**
    ** Course Tag List
    **/
    public function courseTagList() {
        if(isset($this->input_data['COURSE_ID'])) {
            $courseId               =   $this->input_data['COURSE_ID'];
            $courseIdArr            =   [$courseId];
        } else {
            $courseIdArr            =   $this->session->userdata('course_tag_list');    
        }

        $courseArr              =   [];
        if( !empty($courseIdArr) && count($courseIdArr) > 0 ) {
            $courseList         =   $this->course_model->getCourseList($courseIdArr);
            $this->data_arr['course_tag_list']  =   $courseList;
        }

        if(isset($this->input_data['COURSE_ID']) && isset($courseList)) {
            $this->status       =   200;
            $this->message      =   "Done";
            $this->error        =   "";
            $this->response['AJAX_RES']     =   $courseList[0];
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
        foreach ($userCourseArr as $key => $course) {
            $questionCount      =   0;
            if( isset($course['id'])) {
                $questionCount      =   $this->question_model->getCourseQuestionCount($course['id']);
            }
            $userCourseArr[$key]['question_count']  =   $questionCount;
        }
    	$this->data_arr['user_course_list'] 	=	$userCourseArr;
    }

    /**
    **/
    protected function subscribeUserCourse() {
        $courseId       =   $this->input_data['COURSE_ID'];
        if($courseId && $courseId != '') {
            p($courseId,"cpcpc");
        } else {

        }
    }

	/**
    ** prepare result
    ** @param $user_arr
    ** @return template page
    **/
    protected function prepareResult() {
        if( isset($this->input_data['AJAX'])) {
            if($this->input_data['TYPE'] == 'DT'  || $this->input_data['TYPE'] == 'HTM') {
                $this->data_arr['_load_page']           =   isset($this->input_data['LOAD_PAGE'])?array($this->input_data['LOAD_PAGE']):$this->dt_page;

                $this->template->datatable_template( $this->data_arr );
            } else if( $this->input_data['TYPE'] == 'ACTION' ) {
                $this->response['STATUS']       =   $this->status;
                $this->response['MESSAGE']      =   $this->message;
                $this->response['ERROR']        =   $this->error;
                print_r(json_encode($this->response));die();
            }
        } else {
            $page_name                              =   isset($this->data_arr['page_name'])?$this->data_arr['page_name']:'home';

            $this->data_arr['seo_contents']  =   $this->loadHeaderContents($page_name,$this->meta_data);

            $this->template->user_template( $this->data_arr ); 
        }
    }
}
