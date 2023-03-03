<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends Api_Controller {

    public $try_trim_count      =   0;

    public $api_key_arr         =   [];

    public $response            =   [];

    public $question_id;

    public $userId;

    public $message;

    public $status;

    public $error;

    public function __construct() {
        parent::__construct();
        //load required models
        $this->load->model('question/Question_model','question_model');
        $this->load->model('user/User_model','user_model');
        //set template params
        $this->data_arr['_module_name']         =   'api';
        
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Headers: authorization");

        //load required models
        $this->load->model('question/Question_model','question_model');
        //set template params
        $this->data_arr['_module_name']         =   'api';
        
        //input parameters
        $this->input_data       =   $this->input->post();
        $this->get_data         =   $this->input->get();

        //define actions for ajax request
        $this->action           =   isset($this->input_data['ACTION'])?$this->input_data['ACTION']:'';

        $this->apiUsers();

        header("Content-Type:application/json");
    }

    /**
    ** User index
    **/
    public function index() {
        switch ($this->action) {
            case 'LOGIN':
                $this->auth();
                break;
            case 'QUESTION_UPVOTE':
                $this->questionUpvote();
                break;
            default:
                break;
        }

        $this->prepareResult();
    }

    /**
    * add Question
    **/
    public function question($action="") {
        $headers = apache_request_headers();
        //print_r($headers);exit;
        if(!$headers['api_key'] || !$this->authenticateUser($headers) ) {
            print_r(json_encode(['status'=>false,'message'=>"Authentication failed"]));die();
        }
        $data = json_decode(file_get_contents('php://input'), true);
        //print_r($data);die('hi');
        if(count($data) >0) {
            if(isset($data[0]) && is_array($data[0]) && count($data[0])>0) {
                $addCount               =   0;
                $duplicateCount         =   0;
                $newCount               =   0;
                $errorCount             =   0;
                $duplicateList          =   [];
                $errorList              =   [];
                $addList                =   [];

                foreach ($data as $key => $question_value) { 
                    $add    =   $this->addQuestion($question_value);
                    if($add) {
                        $addCount++;
                        array_push($addList,$key);
                    } else {
                        $errorCount++;
                        array_push($errorList,$key);
                    }
                }
                print_r(json_encode(['add_count'=>$addCount,'error_count'=>$errorCount,'add_list'=>$addList]));
            } else if( is_array($data) && !isset($data[0]) && isset($data['text'])) {
                $add    =   $this->addQuestion($data);
                if($add) {
                    print_r(json_encode(['status'=>$this->status,'message'=>$this->message,'data'=>$this->data]));
                } else {
                    print_r(json_encode(['status'=>$this->status,'message'=>$this->message,'data'=>$this->data]));
                }
            }
        } else {
            $this->status   =   "fail";
            $this->message  =   "No data";
            print_r(json_encode(['status'=>$this->status,'message'=>$this->message]));die();
        }
    }

    protected function addQuestion($question_value) {
        $questionText   =   (isset($question_value['text']) && $question_value['text'] != ""?$question_value['text']:"");

        $mimeType           =   (isset($question_value['mimeType'])?$question_value['mimeType']:'text/plain');
        if($mimeType != "text/html") {
            $questionText   =   $this->trimQuestionText($questionText);
        }

        $slug               =   $this->prepareQuestionSlug(strip_tags($questionText));

        $checkDuplicate     =   (isset($question_value['force'])&&$question_value['force']==true?false:true);

        $duplicate          =   false;
        if( $checkDuplicate && $checkDuplicate != false ) {

            include APPPATH . 'third_party/algolia/algoliaSearch.php';
            $algolia        =   new algoliaSearch();
            $indexName      =   $this->config->item('indexName');
            /** check whether question/similar already exist**/
            if(strlen($questionText)<500) {
                $searchResult   =   $algolia->searchFullText($indexName,strip_tags($questionText));
                $searchResult   =   $searchResult['hits'];
                if($searchResult && count($searchResult)>0) {
                    $searchResult   =   $this->prepareDuplicateResponse($searchResult);
                    $duplicate  =   true;
                    $this->status   =   "fail";
                    $this->message  =   "Duplicate question";
                    $this->data     =   ['similar_question'=>$searchResult];
                    return false;
                }
            }
        }

        $courseId           =   (isset($question_value['course_id'])?$question_value['course_id']:null);
        $questionType       =   (isset($question_value['type'])?$question_value['type']:"text");
        $tagList            =   (isset($question_value['tags'])?$question_value['tags']:"");

        if(isset($question_value['options'])) {
            $options        =   $question_value['options'];
        }
        //print_r($options);exit;
        $choiceQuestion     =   isset($question_value['choiceQuestion'])?$question_value['choiceQuestion']:false;
        $answerOption       =   isset($options)?$options:[];

        if($courseId == null) {
            $this->status   =   "fail";
            $this->message  =   "Course missing";
            return false;
        }

        $questionData       =   array(
            'text'          =>  $questionText,
            'slug'          =>  $slug,
            'courseId'      =>  $courseId,
            'type'          =>  "text",
            'tags'          =>  $tagList,
            'mimeType'      =>  $mimeType
        );
        
        if( $choiceQuestion ) {
            $questionData['textWithOutOption']  =   false;
            $questionData['isChoiceQuestion']   =   true;
            $questionData['options']            =   $answerOption;
        } else {
            $questionData['isChoiceQuestion']   =   false;
        }
        
        $questionData       =   $this->prepareQuestionArray($questionData);

        $addQuestion        =   $this->question_model->createQuestionForApi($questionData);
        $questionData['questionId']     =   $addQuestion;
        $updateQuestion     =   $this->question_model->updateQuestionApi($addQuestion,$questionData);

        if($addQuestion) {
            $this->status   =   "success";
            $this->message  =   "Question has been added";
            $this->data     =   $questionData;
            return true;
        } else {
            $this->status   =   "fail";
            $this->message  =   "Can not add question.";
            return false;
        }
    }

    /**
    ** prepare duplicate response
    **/
    protected function prepareDuplicateResponse($searchResult) {
        if( $searchResult && count($searchResult) > 0 ) {
            $resultArr      =   [];
            foreach ($searchResult as $key => $value) {
                $resultArr[$key]['questionId']  =   isset($value['objectID'])?$value['objectID']:'';
                $resultArr[$key]['courseId']    =   isset($value['courseId'])?$value['courseId']:'';
                $resultArr[$key]['htmlContent']  =   isset($value['htmlContent'])?$value['htmlContent']:'';
                $resultArr[$key]['isChoiceQuestion']  =   isset($value['isChoiceQuestion'])?$value['isChoiceQuestion']:'';
                $resultArr[$key]['mimeType']  =   isset($value['mimeType'])?$value['mimeType']:'';
                $resultArr[$key]['options']  =   isset($value['options'])?$value['options']:'';
                $resultArr[$key]['slug']  =   isset($value['slug'])?$value['slug']:'';
                $resultArr[$key]['text']  =   isset($value['text'])?$value['text']:'';
                $resultArr[$key]['type']  =   isset($value['type'])?$value['type']:'';
                $resultArr[$key]['userId']  =   isset($value['userId'])?$value['userId']:'';
                $resultArr[$key]['courseName']  =   isset($value['courseName'])?$value['courseName']:'';
            }            
            return $resultArr;
        }
    }

    /**
    * prepare question array
    * @param $param array
    * @return question array
    **/
    protected function prepareQuestionArray($param) {
        $countInfo  =   ['answers'=>0,'downVote'=>0,'favorites'=>0,'upVote'=>0];
        $data       =   [
            'countInfo'     =>  $countInfo,
            'courseId'      =>  $param['courseId'],
            'created'       =>  $this->prepareFirebaseTimestring(),
            'htmlContent'   =>  (isset($param['html']) && $param['html'] == true)?true:false,
            'isChoiceQuestion'=>    (isset($param['isChoiceQuestion']) && $param['isChoiceQuestion'] == true?true:false),
            'mimeType'      =>  (isset($param['mimeType'])?$param['mimeType']:"text/plain"),
            'modified'      =>  $this->prepareFirebaseTimestring(),
            'slug'          =>  $param['slug'],
            'tags'          =>  $param['tags'],
            'text'          =>  $param['text'],
            'type'          =>  $param['type'],
            'userId'        =>  ($this->userId != ""?$this->userId:"P07DxO4rWwMQXLehMCHrypmRnWO2"),
            'questionId'    =>  "",
            'userType'      =>  "api"
        ];
        if($data['isChoiceQuestion'] && isset($param['options'])) {
            $data['options']    =   $param['options'];
        }
        return $data;
    }

    /**
    * 
    **/
    protected function prepareFirebaseTimestring() {
        $timeparts = explode(" ",microtime());
        $currenttime = bcadd(($timeparts[0]*1000),bcmul($timeparts[1],1000));
        return $currenttime;
    }

    /**
    * prepare question slug
    * @param questionText string
    **/
    protected function prepareQuestionSlug($questionText) {
        if(strlen($questionText)>120) {
            $questionText       =   substr($questionText,0,120);
        }
        
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $questionText);
        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        // trim
        $text = trim($text, '-');
        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);
        // lowercase
        $slug = strtolower($text);
        if (empty($text)) {
            return 'n-a';
        }
        $exist    =   $this->question_model->isQuestionSlugExist($slug);
        if($exist) {
            $randomStr  =   $this->generateRandomString(5);
            $slug       =   $slug."-".strtolower($randomStr);
            return $this->prepareQuestionSlug($slug);
        }
        return $slug;      
    }

    /**
    * trim question text
    **/
    protected function trimQuestionText($questionText) {
        $this->try_trim_count   =   $this->try_trim_count+1;
        if(!strpos($questionText, "<math")) {
            $questionText           =   ltrim($questionText,"<p>&nbsp;</p>");
            $questionText           =   rtrim($questionText,"<p>&nbsp;</p>");
        }
        $questionText           =   ltrim($questionText,"\r\n");
        $questionText           =   rtrim($questionText,"\r\n");
        if( strpos($questionText, "<p>&nbsp;</p>") && $this->try_trim_count <=10) {
            return $this->trimQuestionText($questionText);
        } else if( strpos($questionText, "\r\n")  && $this->try_trim_count <=10) {
            return $this->trimQuestionText($questionText);
        }
        return $questionText;
    }

    /**
    * clean question text
    **/
    protected function parseQuestionText($questionText,$option=array()) {
        if($questionText == '') {
            return false;
        }
        $questionSplit  =   [$questionText];
      
        if(strpos($questionSplit[0], '[A]') && strpos($questionSplit[0], '[B]') && strpos($questionSplit[0], '[C]')) {
            $question   =   explode("[A]", $questionSplit[0]);
            return $question[0];
        } else if(strpos($questionSplit[0], '(A)') && strpos($questionSplit[0], '(B)') && strpos($questionSplit[0], '(C)')) {
            $question   =   explode("(A)", $questionSplit[0]);
            return $question[0];
        } else if(strpos($questionSplit[0], 'A)') && strpos($questionSplit[0], 'B)') && strpos($questionSplit[0], 'C)')) {
            $question   =   explode("A)", $questionSplit[0]);
            return $question[0];
        } else if(strpos($questionSplit[0], 'A.') && strpos($questionSplit[0], 'B.') && strpos($questionSplit[0], 'C.')) {
            $question   =   explode("A.", $questionSplit[0]);
            return $question[0];
        } else if(strpos(strtolower($questionSplit[0]), '(a)') && strpos(strtolower($questionSplit[0]), '(b)') && strpos(strtolower($questionSplit[0]), '(c)')) {
            $question   =   explode("(a)", $questionSplit[0]);
            return $question[0];
        } else if(strpos(strtolower($questionSplit[0]), 'a)') && strpos(strtolower($questionSplit[0]), 'b)') && strpos(strtolower($questionSplit[0]), 'c)')) {
            $question   =   explode("a)", $questionSplit[0]);
            return $question[0];
        } else if(strpos(strtolower($questionSplit[0]), 'a.') && strpos(strtolower($questionSplit[0]), 'b.') && strpos(strtolower($questionSplit[0]), 'c.')) {
            $question   =   explode("a.", $questionSplit[0]);
            return $question[0];
        } else if( strpos($questionSplit[0], '(1)') && strpos($questionSplit[0], '(2)') && strpos($questionSplit[0], '(3)')) {
            $question   =   explode("(1)", $questionSplit[0]);
            return $question[0];
        } else if( strpos($questionSplit[0], '1)') && strpos($questionSplit[0], '2)') && strpos($questionSplit[0], '3)')) {
            $question   =   explode("1)", $questionSplit[0]);
            return $question[0];
        } else if( strpos($questionSplit[0], "1.") && strpos($questionSplit[0],"2.") && strpos($questionSplit[0],"3.")) {
            $question   =   explode("1.", $questionSplit[0]);
            return $question[0];
        } else if( strpos($questionSplit[0], '(I)') && strpos($questionSplit[0], '(I)') && strpos($questionSplit[0], 'III.')) {
            $question   =   explode("(I)", $questionSplit[0]);
            return $question[0];
        } else if( strpos($questionSplit[0], 'I)') && strpos($questionSplit[0], 'II)') && strpos($questionSplit[0], 'III)')) {
            $question   =   explode("I)", $questionSplit[0]);
            return $question[0];
        } else if( strpos($questionSplit[0], 'I.') && strpos($questionSplit[0], 'II.') && strpos($questionSplit[0], 'III.')) {
            $question   =   explode("I.", $questionSplit[0]);
            return $question[0];
        } else if( strpos(strtolower($questionSplit[0]), '(i)') && strpos(strtolower($questionSplit[0]), '(ii)') && strpos(strtolower($questionSplit[0]), 'iii.')) {
            $question   =   explode("(i)", $questionSplit[0]);
            return $question[0];
        } else if( strpos(strtolower($questionSplit[0]), 'i)') && strpos(strtolower($questionSplit[0]), 'ii)') && strpos(strtolower($questionSplit[0]), 'iii)')) {
            $question   =   explode("i)", $questionSplit[0]);
            return $question[0];
        } else if( strpos(strtolower($questionSplit[0]), 'i.') && strpos(strtolower($questionSplit[0]), 'ii.') && strpos(strtolower($questionSplit[0]), 'iii.')) {
            $question   =   explode("i.", $questionSplit[0]);
            return $question[0];
        }

        return $questionSplit[0];
    }

    protected function authenticateUser($headers) {
        $apiKey         =   $headers['api_key'];
        $apiSecret      =   $headers['secret'];
        $appId          =   $headers['app_id'];

        if(isset($this->api_key_arr[$appId])) {
            if($this->api_key_arr[$appId]['app_id'] == $appId && $this->api_key_arr[$appId]['api_key'] == $apiKey && $this->api_key_arr[$appId]['app_secret'] == $apiSecret) {
                    $this->userId   =   $this->api_key_arr[$appId]['user_id'];
                    return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
        return false;
    }

    /**
    *get api users
    **/
    protected function apiUsers() {
        $this->api_key_arr  =   [];
        $apiUser            =   $this->user_model->getApiUsers();
        if( count($apiUser)>0 ) {
            $keyArr         =   [];
            foreach ($apiUser as $key => $user) {
                if(isset($user['app_id']) ) {
                    $keyArr[$user['app_id']] = $user;
                }
            }
            $this->api_key_arr  =   $keyArr;
        }
    }


    /**
    ** prepare result
    ** @param $user_arr
    ** @return template page
    **/
    protected function prepareResult() {
        $this->response['status']       =   $this->status;
        $this->response['data']         =   $this->data_arr;
        $this->response['message']      =   $this->message;
        $this->response['error']        =   $this->error;
        print_r(json_encode($this->response));die();            
    }
}