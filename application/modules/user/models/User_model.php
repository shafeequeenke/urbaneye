<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class User_model extends MY_Model {

	public $collection 			=	'user';
	
    public function __construct() {
        parent::__construct();
        $this->load->library('firestore');
        $this->firestoreObj         =   $this->firestore;
    }

    public function getUser($userId) {
        $userObj        =   $this->firestoreObj->getDocument('users',$userId);
        return count($userObj)>0?$userObj:[];
    }

    /**
    ** question by ids
    **/
    public function getUsersByIds($userId) {
        $userArr            =   $this->firestoreObj->getListById('users','userId',$userId);
        if($userArr) {
            return $userArr;
        }
        return false;
    }

    public function listUser() {
		$userObj 			= 	$this->firebaseAuth->listUsers($defaultMaxResults = 1000, $defaultBatchSize = 1000);
		foreach ($userObj as $user) {
			$userDet = $this->firebaseAuth->getUser($user->uid);
			if($userDet) {
				$this->results[] 	=	$userDet;
			}	
		}
		return $this->results;
    }

    /**
    ** Authenticate user
    **/
    public function authUser( $email,$password ) {
    	try {
		    $user = $this->firebaseAuth->verifyPassword($email, $password);
		    return $user;
		} catch (Kreait\Firebase\Exception\Auth\InvalidPassword $e) {
		    return $e->getMessage();
		}
    }

    public function addUser($userData) {
        $data           =   [
            'email'         =>  $userData['email'],
            'privilegeScore'=>  0,
            'role'          =>  'Student',
            'userId'        =>  $userData['uid'],
            'username'      =>  $userData['displayName']
        ];

        $user   =   $this->firestoreObj->getWhere('users','email','==', $userData['email']);
        if(!$user || empty($user)) {
            return $this->firestoreObj->newDocument('users',$data);
        }
        return true;
    }

    /**
    **/
    public function createUser($userData) {
        if( $userData['email'] == '' || $userData['password'] == '') {
            return false;
        }

        $userProperties = [
            'email'         =>  $userData['email'],
            'emailVerified' =>  $userData['email_verified']?$userData['email_verified']:false,
            'phoneNumber'   =>  $userData['phone']?$userData['phone']:'',
            'password'      =>  $userData['password'],
            'displayName'   =>  $userData['name']?$userData['name']:(explode('@',$userData['email'])[0]),
            'photoUrl'      =>  '',
            'disabled'      =>  false,
        ];
        return $this->createFirebaseUser($userProperties);
    }

    public function userToken() {
    	$additionalClaims = [
    		'premiumAccount' => true
		];

    	$customToken  	= 	$this->firebaseAuth->createCustomToken('9lQxVhQz98MYO88t99JoqPgFh2v2',$additionalClaims);
    	$customTokenString = (string) $customToken;
    	p($customTokenString,"ccpp");
    }

    /**
    * get api users
    **/
    public function getApiUsers() {
        $apiUserArr         = $this->firestoreObj->getList('webApi');
        foreach ($apiUserArr as $document) {
            if ($document->exists()) {
                $this->results[]        =   $document->data();
            }
        }
        return $this->results;
    }

}