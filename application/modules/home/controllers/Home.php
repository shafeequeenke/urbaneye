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
		$this->data_arr['_load_page'] 			=	array('home_page');

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

    public function about() {
        $this->data_arr['_load_page']           =   array('about');
        $this->page_header                      =   'page_header';
        $this->data_arr['page_name']            =   'About';
        $this->prepareResult();
    }

    public function abouturbaneye() {
        $this->data_arr['_load_page']           =   array('sub_header','abouturban');
        $this->page_header                      =   'page_header';
        $this->data_arr['page_name']            =   'About UrbanEye AI';
        $this->prepareResult();
    }

    public function setup() {
        $this->data_arr['_load_page']           =   array('sub_header','setup');
        $this->page_header                      =   'page_header';
        $this->data_arr['page_name']            =   'Features';
        $this->prepareResult();
    }

    public function features() {
        $this->data_arr['_load_page']           =   array('sub_header','features');
        $this->page_header                      =   'page_header';
        $this->data_arr['page_name']            =   'Features';
        $this->prepareResult();
    }

    public function careers() {
        $this->data_arr['_load_page']           =   array('careers');
        $this->page_header                      =   'page_header';
        $this->data_arr['page_name']            =   'Careers';
        $this->prepareResult();
    }

    public function pricing() {
        $this->data_arr['_load_page']           =   array('sub_header','pricing');
        $this->page_header                      =   'page_header';
        $this->data_arr['page_name']            =   'Pricing';
        $this->prepareResult();
    }

    public function enquiry() {
        $this->data_arr['_load_page']           =   array('enquiry');
        $this->page_header                      =   'page_header';
        $this->data_arr['page_name']            =   'Enquiry';
        $this->prepareResult();
    }

    public function termsofservices() {
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

    public function contactus() {
        $this->load->view("home/contactus");
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