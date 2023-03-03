<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Template extends MY_Controller {

	function __construct() {
		parent::__construct();
      	$this->load->model('template/Template_model','template_model');
    }

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->view('template');
	}

	/**
	public template
	**/
	public function public_template( $data )
	{
		$module 					=	$data['_module_name'];
		$page_name 					=	$data['_page_name'];
		$load_pages					=	$data['_load_page'];

		//load admin template head
		$this->load->view('public_header',$data);
		//load module view start
		if( is_array($load_pages) ) {
			foreach ($load_pages as $key => $page) {
				$this->load->view($module.'/'.$page,$data);
			}
		} else {
			$this->load->view($module.'/'.$page_name,$data);
		}		
		//load module view end
		//load admin template footer
		// $this->load->view('template_modal',$data);
		$this->load->view('public_footer',$data);		
	}

	/**
	user template
	**/
	public function user_template( $data )
	{
		$module 					=	$data['_module_name'];
		$page_name 					=	$data['_page_name'];
		$load_pages					=	$data['_load_page'];

		//load admin template head
		$this->load->view('user_header',$data);
		//load module view start
		if( is_array($load_pages) ) {
			foreach ($load_pages as $key => $page) {
				$this->load->view($module.'/'.$page,$data);
			}
		} else {
			$this->load->view($module.'/'.$page_name,$data);
		}		
		//load module view end
		//load admin template footer
		$this->load->view('template_modal',$data);
		$this->load->view('user_footer',$data);
	}

	/**
	datatable template
	**/
	public function datatable_template( $data ) {
		$module 					=	$data['_module_name'];
		$load_pages					=	$data['_load_page'];
		//load datatable view start
		if( is_array($load_pages) ) {
			foreach ($load_pages as $key => $page) {
				$this->load->view($module.'/'.$page,$data);
			}
		} else {
			$this->load->view($module.'/'.$page_name,$data);
		}		
		//load datatable view end
	}
}
