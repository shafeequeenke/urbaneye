<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/* load the MX_Router class */
require APPPATH . "third_party/MX/Controller.php";

class MY_Controller extends MX_Controller
{	
	public $data_arr;

	public $_module;

	public $page_header;

	public $userId;

	public $response;

	public $status;

	public $message;

	public $row_per_page;

	public $error;

	public $meta_data;

	function __construct() 
	{
		parent::__construct();
		$this->_hmvc_fixes();

		$this->meta_data 			=	[];

		$this->row_per_page 		=	15;

		// $this->load->model('course/Course_model','course_model');

		$this->loadUserDefaults();
	}
	
	function _hmvc_fixes()
	{		
		//fix callback form_validation		
		//https://bitbucket.org/wiredesignz/codeigniter-modular-extensions-hmvc
		$this->load->library('form_validation');
		$this->form_validation->CI =& $this;
		$this->load->module('template');
	}

	public function loadHeaderContents($pageName='',$metaContents = array()) {
		return seo_content_list($pageName,$metaContents);
	}

	protected function autherizeUser($user) { 
		$user 		= 	json_decode(json_encode($user), true);
		$userArr 	=	array(
					'uid' 			=> 	$user['uid'],
					'email' 		=>	$user['email'],
					'email_verified'=>  $user['emailVerified'],
					'photo_url'		=> 	$user['photoUrl'],
					'display_name' 	=>  $user['displayName']?$user['displayName']:(explode('@',$user['email'])[0]),
					'access_token' 	=> 	$user['accessToken']?$user['accessToken']:'',
					'token_time' 	=> 	strtotime(date("Y/m/d h:i:s")),
					'is_loggedin' 	=>  TRUE
							);

		$this->session->set_userdata('user_data',$userArr);
		return;
	}

	protected function unauthUser() {
		$this->session->unset_userdata('user_data');
		return true;
	}

	/**
	**/
	protected function isUserLoggedin() {
		$userSession 		=	$this->session->userdata('user_data');

		if(isset($userSession['is_loggedin']) && $userSession['is_loggedin']) {
			return TRUE;
		} else {
			return false;
		}
	}

	protected function loadUserDefaults() {
		if($this->isUserLoggedin()) {
			$this->data_arr['user_details'] 	=	$this->session->userdata('user_data');
			$this->data_arr['user_details']['token_exp_sec'] 	=	$this->tokenExpireTime();	
		} else {
			$this->data_arr['user_details'] 	=	false;
		}
	}

	protected function tokenExpireTime() {
		$token_expire_time 			=	(strtotime(date("Y/m/d h:i:s")))-($this->data_arr['user_details']['token_time']);
		return $token_expire_time;
	}

	/**
    * random string generator
    **/
    protected function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }


}

/* End of file MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */
