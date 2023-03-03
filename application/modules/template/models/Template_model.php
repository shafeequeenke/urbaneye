<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class Template_model extends MY_Model {

	protected $table 	=	'user';
	
    public function __construct() {
        parent::__construct();
    }

}

