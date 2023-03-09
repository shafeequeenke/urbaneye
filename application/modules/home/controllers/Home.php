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

    public function createDoc() {
        p("test over","cp");
        // $this->home_model->createNew();
    }

    /**
    **/
    public function createComplexQuestion() {
        $this->data_arr['_load_page']           =   array('header','mathjax','footer');
        $this->page_header                      =   'page_header';
        $this->data_arr['page_name']            =   'About';
        $this->prepareResult();
    }

    protected function algoliaSearch($searchText = '') {
        $this->load->model('question/Question_model','question_model');
        $this->load->model('answer/Answer_model','answer_model');
        $searchText     =   ($searchText != ''?$searchText:$this->input_data['SEARCH_KEY']);
        include APPPATH . 'third_party/algolia/algoliaSearch.php';
        $algolia        =   new algoliaSearch();
        $indexName      =   $this->config->item('indexName');

        $searchResult   =   $algolia->searchFullText($indexName,$searchText);
        $searchResult   =   $searchResult['hits'];

        $questionIds    =   [];

        if($searchResult && count($searchResult)>0) {
            foreach ($searchResult as $key => $value) {
                array_push($questionIds, $value['objectID']);
            }
        }

        $questionIds    =   json_encode($questionIds);
        $questionArr    =   $this->question_model->getQuestionByIds($questionIds);
        foreach ($questionArr as $key => $question) {
            $answerArr      =   $this->answer_model->getQuestionAnswers($question['questionId']);
            $userDetails    =   isset($question['userId'])?$this->user_model->getUser($question['userId']):array();
            
            $course_question[$key]['answer']    =   $answerArr;
            $course_question[$key]['userDetails']   =   $userDetails;
        }

        $this->data_arr['course_question']      =   $course_question;
        
        // $searchResultHtm            =   $this->load->view('question/header',$this->data_arr);
        $searchResultHtm            =   $this->load->view('question/course_question',$this->data_arr);
        // $searchResultHtm            .=   $this->load->view('question/footer',$this->data_arr);

        
        // p($searchResultHtm,"cppc");
        $this->status               =   200;
        $this->response['data']     =   $searchResultHtm;
    }

    protected function importToAlgolia() {
        $this->load->model('question/Question_model','question_model');
        include APPPATH . 'third_party/algolia/algoliaSearch.php';
        $algolia        =   new algoliaSearch();

        $questionArr    =   $this->question_model->getAllQuestion("questions");
        $importData     =   array();
        $i              =   0;
        // p($questionArr,"cp");
        foreach ($questionArr as $key => $value) {

            if( $i < 1100 ) {
                $i++;
                continue;
            }

            if( $i >= 1200 ) {
                break;
            }

            $keys           =   isset($value['keys'])?$value['keys']:'';
            if(is_array($keys)) {
                $keys       =   json_encode($keys);
            }
            $countInfo      =   isset($value['countInfo'])?$value['countInfo']:'';
            if(is_array($countInfo)) {
                $countInfo  =   json_encode($countInfo);
            }

            $courseId       =   isset($value['courseId'])?$value['courseId']:'';
            $courseName     =   '';
            if( $courseId != '' ) {
                $courseDet      =   $this->course_model->getCourseList($courseId);
                $courseDet      =   $courseDet[0];
                // p($courseDet,"cpc");
                $courseName     =   (isset($courseDet['department'])?($courseDet['department']):''." ".(isset($courseDet['title'])?($courseDet['title']):''));
            }

            $importData[$i]['objectID']         =   $value['questionId'];
            $importData[$i]['courseName']       =   $courseName;
            $importData[$i]['courseId']         =   $courseId;
            $importData[$i]['type']             =   $value['type'];
            $importData[$i]['text']             =   $value['text'];
            $importData[$i]['imageUrl']         =   isset($value['imageUrl'])?$value['imageUrl']:'';
            $importData[$i]['modified']         =   $value['modified'];
            $importData[$i]['countInfo']        =   $countInfo;
            $i++;
        }
        echo "total IMported".count($importData);
        // p($importData);
        // die("cp");
        $result     =   $algolia->importToAlgolia('doubthelp_prod',json_encode($importData));
        echo "====================";
        p($result,"cp");
    }

    public function user_token() {
        p($this->data_arr['user_details'],"cp");
    }

    public function searchDemo() {
        $this->data_arr['_load_page']           =   array('header','search_demo','footer');
        $this->page_header                      =   'page_header';
        $this->data_arr['page_name']            =   'About';
        $this->prepareResult();
    }

    public function quickSearch() {
        $this->data_arr['_load_page']           =   array('search_algolia');
        $this->page_header                      =   'page_header';
        $this->data_arr['page_name']            =   'Search Question';
        $this->data_arr['course_name']          =   isset($this->get_data['course_name'])?$this->get_data['course_name']:'';
        $this->data_arr['search_text']          =   isset($this->get_data['search_question'])?$this->get_data['search_question']:'';
        if(!isset($this->get_data['action'])) {
            $this->prepareResult();
        }
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

    public function contact() {
        $this->data_arr['_load_page']               =   array('contact');
        $this->page_header                          =   'page_header';
        $this->data_arr['page_name']                =   'Contact';
        $this->prepareResult();
    }

    public function testcontact() {
        $this->load->view("home/testcontact");
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