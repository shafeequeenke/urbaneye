<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {

	private $input_data;

    private $dt_page;

	function __construct() {
		parent::__construct();
        $this->load->model('home/Home_model','home_model');
        
        //set template params
    	$this->data_arr['_module_name'] 		=	'home';
		$this->data_arr['_load_page'] 			=	array('header','home_page','footer');

		$this->input_data 		= 	$this->input->post();
        $this->get_data         =   $this->input->get();

		$this->action 			=	(isset($this->input_data['ACTION'])?($this->input_data['ACTION']):(isset($this->get_data['action'])?$this->get_data['action']:''));

        $this->data_arr['_page_name']       =   'home_page';
        $this->data_arr['page_name']        =   'Home';
        // $this->dt_page          =   array('dt_list_country');
    }

    /**
    ** User index
    ** 
    **/
    public function index() { 
        $this->prepareResult();
    }

    /**
    **/
    public function runOneTime() {
        $this->load->model('question/Question_model','question_model');
        $this->question_model->parseMathpix();
        die("cpdfdf");
        // $this->importToAlgolia();
        die();
        $searchText         =   'chemis';
        $this->createComplexQuestion();
        // $this->algoliaSearch($searchText);
        die("here");
    }

    public function about() {
        $this->data_arr['_load_page']           =   array('about');
        $this->page_header                      =   'page_header';
        $this->data_arr['page_name']            =   'About';
        $this->prepareResult();
    }

    public function faq() {
        $this->data_arr['_load_page']           =   array('faq');
        $this->page_header                      =   'page_header';
        $this->data_arr['page_name']            =   'FAQ';
        $this->prepareResult();
    }

    /**
    **/
    public function register() {
        if(isset($this->input_data['SIGN_UP'])) {

        } else {
            $this->data_arr['_load_page']           =   array('header','register','footer');
            $this->data_arr['_page_name']           =   'Register';
            $this->page_header                      =   'page_header';
            $this->data_arr['page_name']            =   'Register';
        }
        $this->prepareResult();
    }

    /**
     * 
     */
    public function contact() {
        $this->input_data 		                = 	$this->input->post();
        if(!empty($this->input_data)) {
            // $this->form_validation->set_rules('full_name','Name',"trim|required");
            // $this->form_validation->set_rules('designation','Designation',"trim|required");
            // $this->form_validation->set_rules('company_name','Company Name','required');
            // $this->form_validation->set_rules('phone','Phone','trim|required|email');
            // $this->form_validation->set_rules('email','Email','trim|required|email');

            // if($this->form_validation->run()===TRUE) {
                $data                   = $this->input_data;
                $date                   = strtotime(str_replace(" ","-",$data['meeting_date']));
                
                $data['meeting_date']   = date('Y-m-d',$date); 

                 $addEnq         = $this->home_model->add_enquiry($data);
            // } else {
                // print_r($this->input_data);die("cp1");
                // die("cp1");
            // }
        }
        $this->data_arr['_load_page']               =   array('contact');
        $this->page_header                          =   'page_header';
        $this->data_arr['page_name']                =   'Contact';
        $this->prepareResult();
        //https://codepen.io/jaromvogel/pen/aNPRwG
    }

    public function get_calendar() {
        $this->load->view("home/contact_calendar");
    }

    public function privacyPolicy() {
        $this->data_arr['_load_page']               =   array('privacy_policy');
        $this->page_header                          =   'page_header';
        $this->data_arr['page_name']                =   'Privacy Policy';
        $this->prepareResult();
    }

    public function onlineResource() {
         $this->data_arr['_load_page']               =   array('online_resource');
        $this->page_header                          =   'page_header';
        $this->data_arr['page_name']                =   '';
        $this->prepareResult();
    }

    public function askQuestion() {
         $this->data_arr['_load_page']               =   array('ask_your_question');
        $this->page_header                          =   'page_header';
        $this->data_arr['page_name']                =   '';
        $this->prepareResult();
    }

    public function onlineClassroom() {
         $this->data_arr['_load_page']               =   array('online_classroom');
        $this->page_header                          =   'page_header';
        $this->data_arr['page_name']                =   '';
        $this->prepareResult();
    }

    public function chatRoom() {
         $this->data_arr['_load_page']               =   array('chatroom');
        $this->page_header                          =   'page_header';
        $this->data_arr['page_name']                =   '';
        $this->prepareResult();
    }

    public function personalCourse() {
         $this->data_arr['_load_page']               =   array('personalized_course');
        $this->page_header                          =   'page_header';
        $this->data_arr['page_name']                =   '';
        $this->prepareResult();
    }

    public function enquiries() {
        $enquiries  = $this->home_model->getAllEnquiry();
        $data['enquiries']  = $enquiries;
        $this->load->view("home/contact_enquiry",$data);
    }

    public function testcorosel() {
        // $enquiries  = $this->home_model->getAllEnquiry();
        // $data['enquiries']  = $enquiries;
        $this->load->view("home/contact_calendar");
    }

    /**
    **/
    protected function listPublicCourse() {
        
        $activeCourseList       =   $this->course_model->getActiveCourse();
        $courseImage            =   $this->course_model->getActiveCourseImage();
        $courseSlug             =   $this->course_model->getCourseIdBySlug();

        $this->data_arr['course_slug']       =  $courseSlug;   
        $this->data_arr['course_image']      =  $courseImage;
        $this->data_arr['public_course']     =  $activeCourseList;
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
            $this->data_arr['page_header']          =   $this->page_header?$this->page_header:'home_header';
            $page_name                              =   isset($this->data_arr['page_name'])?$this->data_arr['page_name']:'home';

            $this->data_arr['seo_contents']         =   $this->loadHeaderContents($page_name);
            // p($this->data_arr,"cpd");
            $this->template->public_template( $this->data_arr ); 
        }
    }

}