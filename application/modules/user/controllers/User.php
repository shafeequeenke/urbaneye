<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Controller {

    public function __construct() {
    	parent::__construct();
    	$this->load->model('user/User_model','user_model');
    	//set template params
    	$this->data_arr['_module_name'] 		=	'user';
		$this->data_arr['_load_page'] 			=	array('header','user_home','footer');
		$this->input_data 		= 	$this->input->post();
		$this->action 			=	isset($this->input_data['ACTION'])?$this->input_data['ACTION']:'test';
        $this->data_arr['_page_name']       =   'user_home';

        $this->data_arr['page_header_name']     =   'User Course';
        $this->data_arr['page_header_link']     =   'course';
        
        $this->home_actions     =   array('GOOGLE_LOGIN','LOGIN','FIREBASE_CREDS');
        if( !in_array($this->action,$this->home_actions) && !$this->isUserLoggedin() ) {
            redirect(base_url());
        }
    }

	/**
    ** User index
    **/
    public function index() { 
    	switch ($this->action) {
    		case 'LOGIN':
    			$this->auth();
    			break;
            case 'FIREBASE_CREDS':
                $this->getFirebaseCredentials();
                break;
            case 'GOOGLE_LOGIN':
                $this->googleLogin();
                break;
            case 'REFRESH_TOKEN':
                $this->refreshAccessToken();
                break;
            case 'LOGOUT':
                $this->logout();
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
    	$user_name 		=	$this->input_data['user_name'];
    	$password 		=	$this->input_data['password'];
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
    protected function googleLogin() {
        $userArr            =   array(
                    'uid'           =>  $this->input_data['uid'],
                    'email'         =>  $this->input_data['email'],
                    'emailVerified' =>  $this->input_data['emailVerified'],
                    'photoUrl'      =>  $this->input_data['photoURL'],
                    'displayName'   =>  $this->input_data['displayName']?$this->input_data['displayName']:(explode('@',$this->input_data['email'])[0]),
                    'accessToken'   =>  $this->input_data['accessToken']
                );

        $addUser        =   $this->user_model->addUser($userArr);
        if( $userArr ) {
            $this->autherizeUser($userArr);
        }
        if( $this->isUserLoggedin() ) {
            $this->status   =   '200';
            $this->message  =   'Logged in success';
            $this->error    =   '';
        }
    }

    protected function refreshAccessToken() {
        $accessToken        =   $this->input_data['accessToken'];
        $userArr            =   $this->session->userdata('user_data');
        $userArr['access_token']    =   $accessToken;
        $userArr['token_time']      =   strtotime(date("Y/m/d h:i:s"));
        $this->session->set_userdata('user_data',$userArr);
        $this->status       =   200;
        $this->message      =   'Updated Access Token';
        return true;       
    }

    public function getFirebaseCredentials() {
        $firebaseCreds      =   array(
            'fire_apiKey'       =>  $this->config->item('apiKey'),
            'fire_authDomain'   =>  $this->config->item('authDomain'),
            'fire_databaseURL'  =>  $this->config->item('databaseURL'),
            'fire_projectId'    =>  $this->config->item('projectId'),
            'fire_storageBucket'=>  $this->config->item('storageBucket'),
            'fire_messagingSenderId'=>  $this->config->item('messagingSenderId'),
            'fire_appId'        =>  $this->config->item('appId')
        );

        $this->response['fireCreds']   =    $firebaseCreds;

        $this->status           =   '200';
        $this->message          =   'SUCCESS';
        $this->error            =   '';
    }

    /**
    **/
    public function signUp() {
        //replace data here
        // $userData           =   [
        //     'email'         =>  'shafeeque.awok@gmail.com',
        //     'password'      =>  '123456',
        //     'name'           =>  'shafeeque',
        //     'phone'         =>  '+919633220199',
        //     'email_verified'=>  true
        // ];
        
        // $createUser     =   $this->user_model->createUser($userData);
        // p($createUser,"cp11");
    }

    /**
    **/
    public function logout() { 
        if( $this->isUserLoggedin() ) {
            if($this->unauthUser()) {
                $this->status   =   '200';
                $this->message  =   'Logout success';
                $this->error    =   '';
            } else {
                $this->status   =   '400';
                $this->message  =   'Logout Failed';
                $this->error    =   'Can not logout user';
            }
        } else {
            $this->status   =   '200';
            $this->message  =   'User Not logged in';
            $this->error    =   '';
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
        if(!$userId)
            return false;
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
            $page_name                              =   isset($this->data_arr['page_name'])?$this->data_arr['page_name']:'home';
            
            $this->data_arr['seo_contents']  =   $this->loadHeaderContents($page_name);
            $this->template->user_template( $this->data_arr ); 
        }
    }
}
