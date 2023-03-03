<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends MY_Controller {

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

	protected $firebaseObj;
	protected $database;
	protected $auth;
    protected $dbname = 'users';

    public function __construct(){
        $this->load->library('firebase');
		$this->firebaseObj 	= 	$this->firebase->init();
		//create Autherization object
		$this->auth 		= 	$this->firebaseObj->getAuth();
        $this->database 	= 	$this->firebaseObj->getDatabase();
    }

	public function index()
	{
		

		try {
		    $verifiedIdToken = $auth->verifyIdToken($token);
		} catch (Exception $e) {
		    echo "Failed";
		}

		$uid = $verifiedIdToken->getClaim('sub');
		echo $uid;die();

		$user = $this->get('2oEQEChQoY6glJIKeE0T');
		print_r($user); die("cp");
		// $auth 	  = 	$firebase->auth();

		$email 	  =		"shafeeque@trymph.com";
		$clearTextPassword	 	=	"123456";
		// $signInResult = $auth->signInWithEmailAndPassword($email, $clearTextPassword);	
		print_r($signInResult);die("cp");

		$database = $firebase->getDatabase();
		$data 		= $database->getReference('users')->getSnapshot();;

		$data 		= $database->getReference("courses")->getSnapshot()->numChildren();
		echo "<pre>";print_r($data);die("cp");
		$this->load->view('welcome_message');
	}

	public function getUser() {
		$users = $this->auth->listUsers($defaultMaxResults = 1000, $defaultBatchSize = 1000);
		foreach ($users as $user) {
			// die($user->uid);
			$userDet = $auth->getUser($user->uid);

			echo "<pre>";print_r($userDet);
		} die("cp11");	}

	public function get(int $userID = NULL){
        if (empty($userID) || !isset($userID)) { return FALSE; }

        if ($this->database->getReference($this->dbname)->getSnapshot()->hasChild($userID)){ die("cccp");
            return $this->database->getReference($this->dbname)->getChild($userID)->getValue();
        } else {
            return FALSE;
        }
    }
}
