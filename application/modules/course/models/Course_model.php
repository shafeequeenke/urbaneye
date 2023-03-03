<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class Course_model extends MY_Model {

	protected $firestoreObj;
	
    public function __construct() {
        parent::__construct();
    	$this->load->library('firestore');
    	$this->firestoreObj 		=	$this->firestore;
    }

    /**
    **/
    public function getCourseList( $courseId ) {
        $courseArr          =   $this->firestoreObj->getListById('courses','id',$courseId);

        if($courseArr) {
            return $courseArr;
        }
        return false;
    }


    /**
    **/
    public function getUserCourse( $userId ) {
    	$userDet 			=	$this->firestoreObj->getDocument('users',$userId);
    	$userCourseId 		=	isset($userDet['courseId'])?$userDet['courseId']:false;
        if( $userCourseId ) {
    	   $userCourseArr 		=	$this->firestoreObj->getWhere('courses',"id", "in", $userCourseId);
        } else {
            $userCourseArr      =   array();
        }
    	return $userCourseArr;
    }

    /**
    ** public course
    **/
    public function getPublicCourse() {
    	$courseArr 			= 	$this->firestoreObj->getList('courses');
    	foreach ($courseArr as $document) {
		    if ($document->exists()) {
		        $this->results[] 		=	$document->data();
		    }
		}
		return $this->results;
    }

    public function listCourse() {
    	$courseArr 			= 	$this->firestoreObj->getList('courses');
        if( !empty($courseArr) ) {
        	foreach ($courseArr as $document) {
    		    if ($document->exists()) {
    		        $this->results[] 		=	$document->data();
    		    }
    		}
        }
		return $this->results;
    }

    public function getActiveCourseTags($courseId) {
        $courseArr          =   $this->firestoreObj->getListById('courses','id',$courseId);
        p($courseArr,"cp");
    }

    public function getActiveCourseImage() {
        if(ENVIRONMENT == 'production') {
            $courseImage            =   array(
                'diS2GgcIRr9tIXHZWFs9' =>   'goverment-exams.webp',
                'LWsWBxq0hK57tAcJLWKJ' =>   'economics.webp',
                '2W3XDgDYpYG4sKZ7HeKA' =>   'iit-jee-physics.webp',
                'QvIZjIJ6QuGS6t4SfPr4' =>   'chemistry.webp',
                'a6sHkIir8NtzGBkdx0hJ' =>   'NTSE-MATHS.webp',
                'kQ4vm80e2n5ttZFEJ1X7' =>   'NEET-BIOLOGY.webp',
                'rwq3zSLZBLUypzxxn0QI' =>   'NEET-PHYSICS.webp',
                'ujS6tJFgImaYkyNczF3E' =>   'NTSE-SCIENCE.webp'
            );
        } else {
            $courseImage            =   array(
                '32NAY8UPgz2wCO0uy6r3' =>   'economics.webp',
                '3nZG4yrke5Ro8css9v4e' =>   'iit-jee-physics.webp',
                'FmI0cGOqVrl9AdbzV6rn' =>   'chemistry.webp',
                '1PyqGIhz7aAErUmB6h1j' =>   'NTSE-MATHS.webp',
                'FmI0cGOqVrl9AdbzV6rn' =>   'NEET-BIOLOGY.webp',
                '3hOfZCiOdptktgh9pyQD' =>   'NEET-PHYSICS.webp',
                'zrXve4xhAFUHGALrFlTC' =>   'NTSE-SCIENCE.webp'
            );
        }
        return $courseImage;
    }

    public function getCourseIdBySlug($courseSlug = "") {
        if(ENVIRONMENT == 'production') {
            $slugIdArr            =   array(
                'diS2GgcIRr9tIXHZWFs9' =>   'government-exam',
                'LWsWBxq0hK57tAcJLWKJ' =>   'us-highschool-economics',
                '2W3XDgDYpYG4sKZ7HeKA' =>   'iit-jee-maths',
                'QvIZjIJ6QuGS6t4SfPr4' =>   'neet-chemistry',
                'a6sHkIir8NtzGBkdx0hJ' =>   'ntse-maths',
                'kQ4vm80e2n5ttZFEJ1X7' =>   'neet-biology',
                'rwq3zSLZBLUypzxxn0QI' =>   'neet-physics',
                'ujS6tJFgImaYkyNczF3E' =>   'ntse-science'
            );
        } else {
            $slugIdArr            =   array(
                '32NAY8UPgz2wCO0uy6r3' =>   'us-highschool-economics',
                '3nZG4yrke5Ro8css9v4e' =>   'iit-jee-physics',
                'FmI0cGOqVrl9AdbzV6rn' =>   'neet-chemistry',
                '1PyqGIhz7aAErUmB6h1j' =>   'ntse-maths',
                'FmI0cGOqVrl9AdbzV6rn' =>   'neet-biology',
                '3hOfZCiOdptktgh9pyQD' =>   'neet-physics',
                'zrXve4xhAFUHGALrFlTC' =>   'ntse-science'
            );
        }

        if($courseSlug != "") {
            return $courseId    =   array_search($courseSlug, $slugIdArr);
        } else {
            return $slugIdArr;
        }
        
    }

    public function getActiveCourse() {
        if(ENVIRONMENT == 'production') {
            $firstElement           = 'diS2GgcIRr9tIXHZWFs9';
            $activeCourseIds        =   array(
                'diS2GgcIRr9tIXHZWFs9',
                'LWsWBxq0hK57tAcJLWKJ',
                '2W3XDgDYpYG4sKZ7HeKA',
                'QvIZjIJ6QuGS6t4SfPr4',
                'a6sHkIir8NtzGBkdx0hJ',
                'kQ4vm80e2n5ttZFEJ1X7',
                'rwq3zSLZBLUypzxxn0QI',
                'ujS6tJFgImaYkyNczF3E'
            );
        } else {
            $firstElement           = '32NAY8UPgz2wCO0uy6r3';
            $activeCourseIds        =   array(
                '32NAY8UPgz2wCO0uy6r3',
                '3nZG4yrke5Ro8css9v4e',
                'FmI0cGOqVrl9AdbzV6rn',
                '1PyqGIhz7aAErUmB6h1j',
                'ljDxQvOZiNZF7pHQx9uV',
                '3hOfZCiOdptktgh9pyQD',
                'zrXve4xhAFUHGALrFlTC'
            );
            
        }

        $courseArr               =   $this->listCourse();   
        $activeCourse            =   [];
        $activeCourseList        =   [];
        $i=0;
        foreach ($courseArr as $key => $value) {
            if(isset($value['id'])&& in_array($value['id'], $activeCourseIds)) {
               
                if($value['id'] ==  $firstElement){
                    $activeCourse[0]    =   $value;
                }
                else{
                    $i++;
                    $activeCourse[$i]   =   $value;
                }
            }
        }
        $j=0;
        foreach ($activeCourse as $key => $value) {
            $activeCourseList[$j]    = $activeCourse[$j];
            $j++;
        }
        return $activeCourseList;
    }    

    public function listPublicCourse() {
    	$publicCourse 	=	$this->firestoreObj->getListCondition('courses',array(['id','=','2j65hvNL56OLGYisBgkg']));
    	p($publicCourse,"cp");
    }
}
		