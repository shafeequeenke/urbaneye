<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class Question_model extends MY_Model {

	protected $firestoreObj;
	
    public function __construct() {

        parent::__construct();
    	$this->load->library('firestore');
    	$this->firestoreObj 		=	$this->firestore;
    }

    /**
    *get all questions
    **/
    public function getAllQuestion($collection) {
        $questionArr            =   $this->firestoreObj->getList($collection);
        foreach ($questionArr as $document) {
            if ($document->exists()) {
                $this->results[]        =   $document->data();
            }
        }
        return $this->results;
    }

    /**
    **/
    public function isQuestionSlugExist($slug) {
        $questionArr            =   $this->firestoreObj->getWhere('questions','slug','=',$slug);
        if ($questionArr && !empty($questionArr)) {
            return $questionArr;
        }
        return false;
    }

    /**
    *
    **/
    public function isQuestionIdExist($questionId) {
        $questionArr            =   $this->firestoreObj->getWhere('questions','questionId','=',$questionId);
        if ($questionArr && !empty($questionArr)) {
            return $questionArr;
        }
        return false;
    }

    /**
    ** get question
    **/
    public function getQuestion($courseId) {

    	$questionArr 			= 	$this->firestoreObj->getList();
    	foreach ($questionArr as $document) {
		    if ($document->exists()) {
		        $this->results[] 		=	$document->data();
		    }
		}

		return $this->results;
    }

    /**
    ** question by ids
    **/
    public function getQuestionById($questionId) {
        $questionArr          =   $this->firestoreObj->getListById('questions','questionId',$questionId);
        
        if($questionArr) {
            return $questionArr;
        }
        return false;
    }

    // /**
    // ** question by ids
    // **/
    // public function getQuestionById($questionId) {

    //     $questionArr            =   $this->firestoreObj->getWhere("questions","questionId","==",$questionId);
      
    //     return $questionArr;
    // }

    /**
    **get course question count
    **/
    public function getCourseQuestionCount($courseId) {
    	$questionRes 			= 	$this->firestoreObj->getCount('questions','courseId','==',$courseId);
    	return $questionRes;
    }

    /**
    ** get question
    **/
    public function getCourseQuestion($courseId) {

        $questionArr            =   $this->firestoreObj->getWhere('questions','courseId','=',$courseId,500);
        if ($questionArr && !empty($questionArr)) {
            $this->results          =   $questionArr;
        }

        return $this->results;
    }

    /**
    * get user question
    **/
    public function getUserQuestion($userId,$courseId) {
        $filter             =   ['userId'=>$userId,'courseId'=>$courseId];
        $questionArr        =   $this->firestoreObj->getUserQuestion($userId,$courseId,1000);

        if ($questionArr && !empty($questionArr)) {
            $this->results          =   $questionArr;
        }

        return $this->results;
    }

    public function getQuestionByCourse($courseId,$offset=0,$limit=20) { 
    	// $questionRes 			= 	$this->firestoreObj->getWhere('questions','courseId','==',$courseId);
    	// $questionArr 			=	[];
	    // if(count($questionRes) > 0) {
	    // 	$this->results			=	$questionRes;
	    // }
        // /course/:courseId/question
        $url        =   'course/'.$courseId.'/question';
        $input_params       =   array(
                                'offset'        =>  $offset,
                                'max'           =>  $limit,
                                'courseId'      =>  $courseId
                            );

        $questionArr        =   $this->getCurlResponse($url,$input_params);

		$this->results      =     json_decode($questionArr,true);
        return $this->results['status']?$this->results['data']:[];
    }

    /**
    **/
    public function questionUpVote($questionId,$userId) {

        $questionUpVote   =   $this->getUserQuestionVote($questionId,$userId);
        
        if($questionUpVote && $questionUpVote['upVote'] >= 1) {
            return ['STATUS'=>400, 'MESSAGE'=>"Already Voted"];
        }

        $url        =   'question/'.$questionId.'/up-vote';

        $questionUpVote     =   $this->postCurlResponse($url);

        $this->results      =     $this->prepareCurlResponse($questionUpVote);

        return $this->results;
    }

    public function questionDownVote($questionId,$userId) {

        $questionDownVote   =   $this->getUserQuestionVote($questionId,$userId);
        if($questionDownVote && $questionDownVote['downVote'] >= 1) {
            return ['STATUS'=>400, 'MESSAGE'=>"Already Voted"];
        }

        $url                =   'question/'.$questionId.'/down-vote';
        
        $questionDownVote   =   $this->postCurlResponse($url);

        $this->results      =     $this->prepareCurlResponse($questionDownVote);

        //p($questionDownVote, '');

        return $this->results;

    }

    /**
    * create Question
    **/
    public function createQuestion($data) {
        $url                =   'question/';

        $result             =   $this->postCurlResponse($url,$data);

        $createQuestion     =     $this->prepareCurlResponse($result);

        return $createQuestion;
    }

    
    /**
    * question vote
    **/
    public function getUserQuestionVote($questionId,$userId) {

        $masterCollection   =   'questions';
        $collection         =   'vote';
        $documentId         =   $questionId;
        $voteArr      =   [];
        $result     =   $this->firestoreObj->getSubCollection($masterCollection,$collection,$documentId);
        if($result && is_array($result)) {
            $upVote         =   0;
            $downVote       =   0;
            $favourite      =   0;
            foreach ($result as $key => $value) {
                if($userId ==  $key && $value['upVote'] >=1) {
                    $upVote++;
                }
                if($userId ==  $key && $value['downVote'] >=1) {
                    $downVote++;
                }
                if($userId == $key && $value['favorite'] >=1) {
                    $favourite++;
                }
            }
            return ['upVote'=>$upVote,'downVote'=>$downVote,'favourite'=>$favourite];
        }
        return false;
    }

    public function courseQuestionCount($courseId) {
    	$questionArr 			= 	$this->firestoreObj->getWhere('questions','courseId','==',$courseId,1000);

	    if ($questionArr && !empty($questionArr)) {
	        $this->results			=	$questionArr;
	    }

		return count($this->results);
    
    }

    public function userQuestionCount($userId) {
        $questionArr            =   $this->firestoreObj->getWhere('questions','userId','==',$userId,1000);

        if ($questionArr && !empty($questionArr)) {
            $this->results          =   $questionArr;
        }

        return count($this->results);
    }

    public function getUserCourseQuestionCount($userId,$courseId) {
        $results             =   [];
        $questionArr        =   $this->firestoreObj->getUserQuestion($userId,$courseId,1000);

        if ($questionArr && !empty($questionArr)) {
            $results          =   $questionArr;
        }

        return count($results);
    }

    /**
    * get user favourite questions
    **/
    public function getUserFavourites($userId) {
        $masterCollection   =   'users';
        $collection         =   'favoriteQuestions';
        $documentId         =   $userId;
        $favouritesArr      =   [];
        $result     =   $this->firestoreObj->getSubCollection($masterCollection,$collection,$documentId);
        if($result && is_array($result)) {
            foreach ($result as $key => $value) {
                if(!in_array($value['questionId'], $favouritesArr) ) {
                    array_push($favouritesArr, $value['questionId']);
                }
            }
        }
        return $favouritesArr;
    }

    public function questionFavorite($questionId) {
        $url                =   'question/'.$questionId.'/favourite';

        $questionFavorite   =   $this->postCurlResponse($url);

        $this->results      =    $this->prepareCurlResponse($questionFavorite);

        return $this->results;     
    }

    public function updateQuestion($questionId,$data) {
        $url        =   'question/'.$questionId;
        $result     =   $this->putCurlResponse($url,$data);
        $this->results      =    $this->prepareCurlResponse($result);

        return $this->results;
    }

    public function deleteQuestion($questionId) {
        $url                =   'question/'.$questionId;
        $result             =   $this->deleteCurlResponse($url);
        $this->results      =   $this->prepareCurlResponse($result);
        
        return $this->results;   
    }
 
    /**
    **/
    public function parseMathpix($filePath = "") {
        if($filePath == "") {
            return "no_file";
        }
        $this->load->library('mathpix');
        $postData   =   [
            'src' => $filePath,
            'formats' => ["text","data","html"],
            "data_options" => [
                            "include_asciimath" => true,
                            "include_latex" => true
                            ]
        ];    
        //initializeFirebase
        $result     =   $this->mathpix->postCurlResponse("text",$postData);
        return $this->prepareCurlResponse($result);
    }

    public function updateQuestionApi($questionId,$data) {
        $updateQuestion     =   $this->firestoreObj->updateDocument('questions',$questionId,$data);
        return $updateQuestion;
    }

    /**
    **/
    public function createQuestionForApi($data) {
        $createQuestion     =   $this->firestoreObj->newDocument('questions',$data);
        return $createQuestion;
    }
}
		