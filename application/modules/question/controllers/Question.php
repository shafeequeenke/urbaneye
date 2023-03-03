<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Question extends MY_Controller {

    public $try_trim_count     =   0;

    public function __construct() {
        parent::__construct();
        //load required models
        $this->load->model('question/Question_model','question_model');
        $this->load->model('user/User_model','user_model');
        //set template params
        $this->data_arr['_module_name']         =   'question';
        $this->data_arr['_load_page']           =   array('user_course');
        $this->data_arr['_page_name']           =   'user_home';        
        
        //input parameters
        $this->input_data       =   $this->input->post();
        $this->get_data         =   $this->input->get();

        //define actions for ajax request
        $this->action           =   isset($this->input_data['ACTION'])?$this->input_data['ACTION']:'';
        
    }

    /**
    ** User index
    **/
    public function index() {
        $this->user_model->getApiUsers();
        switch ($this->action) {
            case 'LOGIN':
                $this->auth();
                break;
            case 'QUESTION_UPVOTE':
                $this->questionUpvote();
                break;
            case 'QUESTION_DOWNVOTE':
                $this->questionDownvote();
                break;
            case 'QUESTION_FAVORITE':
                $this->questionFavorite();
                break;
            case 'LIST_COURSE_QUESTION':
                $this->getCourseQuestions();
                break;
            case 'LIST_USER_QUESTION':
                $this->getUserQuestions();
                break;
            case 'ALGOLIA_SEARCH':
                $this->algoliaSearch();
                break;
            case 'SEARCH_BY_COURSE':
                $this->getCourseQuestions();
                break;
            case 'ADD_ANSWER':
                $this->addAnswer();
                break;
            case 'LOAD_LATEX':
                $this->loadLatexString();
                break;
            case 'ADD_QUESTION':
                $this->addQuestion();
                break;
            case 'UPDATE_QUESTION':
                $this->updateQuestion();
                break;
            case 'DELETE_QUESTION':
                $this->deleteQuestion();
                break;
            case 'DELETE_ANSWER':
                $this->deleteAnswer();
                break;
            case 'ANSWER_VOTE':
                $this->voteAnswer();
                break;
            case 'PARSE_QUESTION_IMAGE':
                $this->parseQuestionImage();
                break;
            case 'CHECK_QUESTION_TEXT_MCQ':
                $this->checkQuestionTextMCQ();
                break;
            case 'ADD_IMAGE_QUESTION':
                $this->addImageQuestion(); 
                break;
            case 'UPDATE_IMAGE_QUESTION':
                $this->updateImageQuestion(); 
                break;
            default:
                // $this->listCourse();
                // $this->getUserCourse();
                redirect('home');
                $this->getCourseQuestions();
                break;
        }

        $this->prepareResult();
    }

    /**
    *create question
    **/
    public function createComplexQuestion($courseId = "") {
        $activeCourseList       =   $this->course_model->getActiveCourse();

        $this->data_arr['active_course']            =   $activeCourseList;
        $this->data_arr['course_id']                =   $courseId;
        if($courseId && $courseId !="") {
            $courseList         =   $this->course_model->getCourseList([$courseId]);
            if($courseList && count($courseList)>0 && isset($courseList[0]['tags'])) {
                $tags             =   $courseList[0]['tags'];
                $this->data_arr['course_tags']  =   $tags;
            }
            $courseName           =     get_course_name($courseList[0]);
        }
        $meta_contents                          =   array(
            'title'             =>  isset($courseName)?$courseName." - Doubt Help Create Question":'Create Doubt Help Question.',
            'page_name'         =>  'Create'
        );

        $this->data_arr['_load_page']               =   array('header','create_question','footer');
        $this->page_header                          =   'page_header';
        
        $this->data_arr['page_name']                =   'custom';
        $this->data_arr['meta_contents']            =   $meta_contents;

        $this->prepareResult();
    }

    /**
    ** edit question
    **/
    public function edit($questionId,$editor = "") {
        
        $question               =   $this->question_model->getQuestionById($questionId);

        if( $this->isUserLoggedin() ) {
            $userId         =   $this->data_arr['user_details']['uid'];
            if($userId != $question[0]['userId']) {
                redirect('question/'.$questionId);
            }
        } else {
            redirect('question/'.$questionId);
        }

        if($question && is_array($question)) {
            $courseId           =   $question[0]['courseId'];
            $activeCourseList   =   $this->course_model->getActiveCourse();
            $courseList         =   $this->course_model->getCourseList([$courseId]);
            if($courseList && count($courseList)>0 && isset($courseList[0]['tags'])) {
                $tags             =   $courseList[0]['tags'];
                $this->data_arr['course_tags']  =   $tags;
            }

            if($courseId && $courseId !="") {
                $courseList         =   $this->course_model->getCourseList([$courseId]);
                if($courseList && count($courseList)>0 && isset($courseList[0]['tags'])) {
                    $tags             =   $courseList[0]['tags'];
                    $this->data_arr['course_tags']  =   $tags;
                }
                $courseName           =     get_course_name($courseList[0]);
            }
        }

        $meta_contents                          =   array(
            'title'             =>  isset($courseName)?$courseName." - Doubt Help Update Question":'Update Doubt Help Question.',
            'page_name'         =>  'Create'
        );

        $questionType               =    $question[0]['type'];;           
        if(isset($question[0]['mimeType'])) {
            $mimeType               =    $question[0]['mimeType'];
        } else if(isset($question[0]['htmlContent']) && $question[0]['htmlContent'] == true ) {
            $mimeType               =    "text/html";
        } else {
            $mimeType               =    "text/plain";
        }
        
        //auto detect question editor
        if($questionType == "image") {
            $question_editor                        =   "edit_image";
        } else if($mimeType == "application/x-latex") {
            $question_editor                        =   "edit_latex";
        } else if($mimeType == "text/html") {
            $question_editor                        =   "edit_ckeditor";
        }
         else {
            $question_editor                        =   "";
        }

        //user force to change editor
        
        if($editor == "image") {
            $question_editor                        =   "edit_image";
        } else if($editor == "html") {
            $question_editor                        =   "edit_ckeditor";
        } else if($editor == "latex") {
            $question_editor                        =   "edit_latex";
        } else if($editor == "text") {
            $question_editor                        =   "";
        }

         if(isset($this->input_data['latex_question_text']) && $this->input_data['latex_question_text'] != "") {
            $latex_question_text    =   $this->input_data['latex_question_text'];
            $this->data_arr['latex_question_text']  =   $latex_question_text;
        }

        if(isset($this->input_data['complex_question_text']) && $this->input_data['complex_question_text'] != "") {
            $complex_question_text    =   $this->input_data['complex_question_text'];
            $this->data_arr['complex_question_text']  =   $complex_question_text;
        }
        $this->data_arr['question']                 =   $question[0];
        $this->data_arr['active_course']            =   $activeCourseList;
        $this->data_arr['course_id']                =   $courseId;

        $this->data_arr['_load_page']           =   array('header','edit_question_wrapper','footer');
        $this->data_arr['question_editor']          =   $question_editor;
        $this->page_header                          =   'page_header';
        $this->data_arr['page_name']                =   'custom';
        $this->data_arr['meta_contents']            =   $meta_contents;
        
        $this->prepareResult();
    }

    /**
    ** algolia question search 
    **/
    protected function algoliaSearch($searchText = '') {
        $this->load->model('question/Question_model','question_model');
        $this->load->model('answer/Answer_model','answer_model');
        
        $searchText     =   ($searchText != ''?$searchText:$this->input_data['SEARCH_KEY']);
        include APPPATH . 'third_party/algolia/algoliaSearch.php';
        $algolia        =   new algoliaSearch();

        $indexName      =   $this->config->item('indexName');

        if($this->isUserLoggedin()) {
            $userId         =   $this->data_arr['user_details']['uid'];
            $userFavaorites =   $this->getUserFavouriteQuestions($userId);
        }
        
        $searchResult   =   $algolia->searchFullText($indexName,$searchText);
        $searchResult   =   $searchResult['hits'];

        $course_question    =   [];
        $courseList         =   [];
        $questionList       =   [];
        $count          =   0;
        if($searchResult && count($searchResult)>0) {
            foreach ($searchResult as $key => $value) {
                if(!in_array($value['objectID'], $questionList)) {
                    array_push($questionList, $value['objectID']);
                }
            }
        }

        $questionArr        =   $this->question_model->getQuestionById($questionList);
        $userIdArr          =   [];
        $user_arr           =   [];
        $isLatex            =   false;
        if($questionArr && count($questionArr)>0) {
            foreach ($questionArr as $key => $question) {
                if(isset($userId)) {
                    $votes      =   $this->question_model->getUserQuestionVote($question['questionId'],$userId);
                    $question['userVote']   =   $votes;
                }
                $answerArr      =   $this->answer_model->getQuestionAnswers($question['questionId']);
                //check question type for latex parsing
                $mimeType       =   (isset($question['mimeType'])?$question['mimeType']:'text/plain');
                if($mimeType&&$mimeType=="application/x-latex") {
                    $isLatex        =   true;
                }
                //check question type for latex parsing
                if(!in_array($question['userId'], $userIdArr)) {
                    array_push($userIdArr,$question['userId']);
                }

                $courseId       =   isset($question['courseId'])?$question['courseId']:'';
                if(!in_array($courseId, $courseList)) {
                    array_push($courseList, $courseId);
                }

                if(isset($userFavaorites) && in_array($question['questionId'], $userFavaorites)) {
                    $question['isFavourite']        =   true;
                } else {
                    $question['isFavourite']        =   false;
                }
                if((isset($question['isChoiceQuestion']) && $question['isChoiceQuestion'] == true) || (isset($question['textWithOutOption']) && $question['textWithOutOption'] == true)) {
                    $questionText           =   $this->parseQuestionText($question['text'],$question['options']);
                    $question['text']       =   $questionText;
                }

                $course_question[$key]              =   $question;
                $answeredUsers                      =   [];
                if( $answerArr && count($answerArr)>0 ) {
                    foreach ($answerArr as $ak => $ans) {
                        if(isset($ans['userId'])) {
                            array_push($userIdArr, $ans['userId']);
                        }
                        if(!in_array($ans['userId'], $answeredUsers)) {
                            array_push($answeredUsers, $ans['userId']);
                        }
                    }
                }
                
                $course_question[$key]['answeredUsers']         =   $answeredUsers;
                $course_question[$key]['answer']        =   $answerArr;
                // $course_question[$key]['userDetails']   =   $userDetails;
            }

            $users  =   $this->user_model->getUsersByIds($userIdArr);

            if(is_array($users) && count($users)) {
                foreach ($users as $key => $value) {
                    $user_arr[$value['userId']]     =   $value;
                }
            }
        }
        
        $this->session->set_userdata('course_tag_list',$courseList);
        $this->data_arr['user_list']            =   $user_arr;
        $this->data_arr['course_question']      =   $course_question;
        $this->data_arr['is_latex']             =   $isLatex;
        
    }

    protected function updateQuestion() {
       
        if( $this->isUserLoggedin() ) {
            $this->form_validation->set_rules('question_text','Question Text',"trim|required");
            $this->form_validation->set_rules('course_id','Course',"trim|required");
            //$this->form_validation->set_rules('question_tag[]','Tag','required');
            $isChoiceQuestion   =   (isset($this->input_data['question_is_mcq']) && $this->input_data['question_is_mcq'] !=''?true:false);
            //question option is required while it's a choice question
            if($isChoiceQuestion) {
                $this->form_validation->set_rules('options','Options','trim|required');
            }

            if($this->form_validation->run()===TRUE) {
                $questionText       =   $this->input_data['question_text'];
                $questionText       =   $this->trimQuestionText($questionText);

                $forceMcqSubmit     =   isset($this->input_data['forceMcqSubmit']) && $this->input_data['forceMcqSubmit'] == "force"?true:false;
                
                if(!$isChoiceQuestion && !$forceMcqSubmit) {                    $checkIsMcq         =   $this->checkIsQuestionTextMCQ($questionText);

                    if($checkIsMcq && is_array($checkIsMcq) && count($checkIsMcq)>1) {
                        $ajax_res           =   array('subject'=>'mcq_question','data'=>$checkIsMcq);

                        $this->status       =   203;
                        $this->message      =   "Question is mcq";
                        $this->response['AJAX_RES']     =   $ajax_res;
                        $this->error        =   "";
                        return;
                    }
                }

                if(isset($this->input_data['options'])) {
                    $options    =   explode(",", str_replace("\n","", $this->input_data['options']));
                    $optionErr              =   false;
                    if(is_array($this->input_data['options'])) {    
                        foreach ($this->input_data['options'] as $key => $value) {
                            if(strip_tags($value) == "") {
                                $optionErr      =   true;
                            }
                        }
                        if($optionErr) {
                            $ajax_res           =   array('subject'=>'fill_fields','data'=>'The option field is required');
                            $this->status       =   400;
                            $this->message      =   'Fill the required fields..!';
                            $this->error        =   '';
                            $this->response['AJAX_RES']     =   $ajax_res;
                            return;
                        }
                    }
                }

                $questionId         =   $this->input_data['QUESTION_ID'];

                $answerOption       =   isset($options)?$options:[];
                $courseId           =   $this->input_data['course_id'];
                $questionType       =   (isset($this->input_data['question_type'])?$this->input_data['question_type']:'text');

                $tagList            =   [];
                if(isset($this->input_data['question_tag'])) {
                    foreach ($this->input_data['question_tag'] as $k => $tag) {
                        $tagList[]  =   $tag;
                    }
                }

                $checkDuplicate     =   (isset($this->input_data['submit_question'])?$this->input_data['submit_question']:'');
                if( $checkDuplicate == "updateQuestion" ) {
                    /** check whether question/similar already exist**/
                    include APPPATH . 'third_party/algolia/algoliaSearch.php';
                    $algolia        =   new algoliaSearch();

                    $indexName      =   $this->config->item('indexName');
                    $searchResult   =   $algolia->searchFullText($indexName,strip_tags($questionText));
                    $searchResult   =   $searchResult['hits'];
                    
                    if($searchResult && count($searchResult)>0) {
                        $questionTextList       =   [];
                        $questionIds            =   [];
                        foreach ($searchResult as $key => $value) {
                            if(!in_array($value['objectID'], $questionIds)) {
                                if($value['objectID'] == $questionId) {
                                    continue;
                                }
                                array_push($questionIds, $value['objectID']);
                                $questionTextParsed     =   $this->parseQuestionText($value['text']);
                                array_push($questionTextList, $questionTextParsed);
                            }
                        }
                        if(count($questionIds)>0) {
                            $ajax_res           =   array('subject'=>'similar_question','data'=>$questionTextList);

                            $this->status       =   400;
                            $this->message      =   "Similar Question Found";
                            $this->response['AJAX_RES']     =   $ajax_res;
                            $this->error        =   "";
                            return;
                        }
                        
                    }
                    /** check whether question/similar already exist end**/
                }

                $mimeType           =   (isset($this->input_data['MIME_TYPE'])?$this->input_data['MIME_TYPE']:'text/plain');

                $questionData       =   array(
                    'text'          =>  $questionText,
                    'courseId'      =>  $courseId,
                    'type'          =>  $questionType,
                    'imageUrl'      =>  '',
                    'force'         =>  true,
                    'tags'          =>  $tagList,
                    'mimeType'      =>  $mimeType,
                    'htmlContent'   =>  true
                );
                if($isChoiceQuestion) {
                    $questionData['textWithOutOption']  =   true;
                    $questionData['isChoiceQuestion']   =   true;
                    $questionData['options']            =   $answerOption;
                    //adding options as string with question text
                    // $optCount                           =   1;
                    // foreach ($answerOption as $opt) {
                    //     $optionStr      =   $optionStr." (".$optCount.") ".$opt;
                    //     $optCount++;
                    // }
                    // $questionData['text']               =   $questionText." ".strip_tags($optionStr);
                }

                $updateQuestion        =   $this->question_model->updateQuestion($questionId,$questionData);

                if($updateQuestion) {
                    $this->status       =   200;
                    $this->message      =   'Question has been updated';
                } else {
                    $this->status       =   400;
                    $this->message      =   'Error to update Please try again.';
                    $this->error        =   '';                    
                }
            } else {
                $form_error_mess    =   validation_errors();
                $ajax_res           =   array('subject'=>'fill_fields','data'=>$form_error_mess);
                $this->status       =   400;
                $this->message      =   'Fill the required fields..!';
                $this->error        =   '';
                $this->response['AJAX_RES']     =   $ajax_res;
            }
        } else {
            $this->status       =   402;
            $this->message      =   'User Not Loggedin';
            $this->error        =   '';
        }           
    }

    /**
    ** parse question on paste
    **/
    protected function checkIsQuestionTextMCQ($questionText = "") {
        $questionText       =   $this->input_data['question_text'];
        if($questionText == "") {
            return false;
        } else {
            if(strpos($questionText, "<ul>") && strpos($questionText, "<li>")) {
                $questionArr    =   explode("<ul>", $questionText);
                foreach ($questionArr as $key => $value) {
                    if(strpos($value, "<li>")) {
                        $optionIn   =   explode("<li>", $value);
                        $optionArr  =   [];
                        if($optionIn && count($optionIn)>=3) {
                            foreach ($optionIn as $k => $optHtm) {
                                array_push($optionArr,rtrim($optHtm,"</li>"));
                            }
                            $mcqQuestionArr     =   [];
                            unset($questionArr[$key]);
                            $mcqQuestionArr["question"]     = $questionArr;
                            $mcqQuestionArr["options"]      = $optionArr;
                            return $mcqQuestionArr;
                        }
                    } else {
                        continue;
                    }
                }
            } else {
                return false;
            }
        }
        return false;
    }

    /**
    ** add image question
    **/
    protected function addImageQuestion() {
        if( $this->isUserLoggedin() ) {
            $this->form_validation->set_rules('question_image','Image file',"trim|required");
            $this->form_validation->set_rules('course_id','Course',"trim|required");
            if($this->form_validation->run()===TRUE) {
                $courseId           =   $this->input_data['course_id'];
                $questionImage      =   $this->input_data['question_image'];
                $imageId            =   $this->input_data['image_id'];
                $questionType       =   (isset($this->input_data['question_type'])?$this->input_data['question_type']:'image');
                $tagList            =   [];
                if(isset($this->input_data['question_tag']) && $this->input_data['question_tag'] !="") {
                    $tagList        =   $this->input_data['question_tag'];
                    $tagList        =   explode(",", $tagList);   
                }
                $mimeType           =   (isset($this->input_data['MIME_TYPE'])?$this->input_data['MIME_TYPE']:'text/plain');
                $slug               =   (isset($this->input_data['slug'])?$this->input_data['slug']:'');
                $options            =   (isset($this->input_data['options'])?$this->input_data['options']:false);
                
                $questionData       =   array(
                    'text'          =>  "",
                    'slug'          =>  $slug,
                    'courseId'      =>  $courseId,
                    'type'          =>  $questionType,
                    'imageUrl'      =>  $questionImage,
                    'image'         =>  $imageId,
                    'force'         =>  true,
                    'tags'          =>  $tagList,
                    'htmlContent'   =>  true,
                    'mimeType'      =>  $mimeType,
                    'isChoiceQuestion'=>   false,
                    'htmlContent'   =>  false
                );

                if($options && $options !="") {
                    $isChoiceQuestion   =   true;
                    $options            =   explode(",", str_replace("\n","", $options));
                    $questionData['isChoiceQuestion']   =   true;
                    $questionData['options']    =   $options;
                }

                $addQuestion        =   $this->question_model->createQuestion($questionData);

                if($addQuestion) {
                    $this->status       =   200;
                    $this->message      =   'Question Has been Added';
                } else {
                    $this->status       =   400;
                    $this->message      =   'Error to update Please try again.';
                    $this->error        =   '';                    
                }
            } else {
                $form_error_mess    =   validation_errors();
                $ajax_res           =   array('subject'=>'fill_fields','data'=>$form_error_mess);
                $this->status       =   400;
                $this->message      =   'Fill the required fields..!';
                $this->error        =   '';
                $this->response['AJAX_RES']     =   $ajax_res;
            }
        } else {
            $this->status       =   402;
            $this->message      =   'User Not Loggedin';
            $this->error        =   '';
        }
    }
 /**
    ** update image question
    **/
    protected function updateImageQuestion() {
        if( $this->isUserLoggedin() ) {
           // $this->form_validation->set_rules('question_image','Image file',"trim|required");
            $this->form_validation->set_rules('course_id','Course',"trim|required");
            if($this->form_validation->run()===TRUE) {
                $courseId           =   $this->input_data['course_id'];
                $questionImage      =   isset($this->input_data['question_image'])?$this->input_data['question_image']:'';
                $imageId            =   isset($this->input_data['image_id'])?$this->input_data['image_id']:'';
                $questionType       =   (isset($this->input_data['question_type'])?$this->input_data['question_type']:'image');
                $tagList            =   [];
                if(isset($this->input_data['question_tag']) && $this->input_data['question_tag'] !="") {
                    $tagList        =   $this->input_data['question_tag'];
                    $tagList        =   explode(",", $tagList);   
                }
                $mimeType           =   (isset($this->input_data['MIME_TYPE'])?$this->input_data['MIME_TYPE']:'text/plain');
                $slug               =   (isset($this->input_data['slug'])?$this->input_data['slug']:'');
                $options            =   (isset($this->input_data['options'])?$this->input_data['options']:false);
                $questionId         =   $this->input_data['QUESTION_ID'];
                //print_r($this->input_data['QUESTION_ID']);die('hi');
                if(isset($questionImage)){
                    //p($this->input_data,"cpd");
                    $questionData       =   array(
                        'text'          =>  "",
                        'slug'          =>  $slug,
                        'courseId'      =>  $courseId,
                        'type'          =>  $questionType,
                        'imageUrl'      =>  $questionImage,
                        'image'         =>  $imageId,
                        'force'         =>  true,
                        'tags'          =>  $tagList,
                        'htmlContent'   =>  true,
                        'mimeType'      =>  $mimeType,
                        'isChoiceQuestion'=>   false,
                        'htmlContent'   =>  false
                    );
                }
                else{
                     $questionData       =   array(
                        'text'          =>  "",
                        'slug'          =>  $slug,
                        'courseId'      =>  $courseId,
                        'type'          =>  $questionType,
                        'force'         =>  true,
                        'tags'          =>  $tagList,
                        'htmlContent'   =>  true,
                        'mimeType'      =>  $mimeType,
                        'isChoiceQuestion'=>   false,
                        'htmlContent'   =>  false
                    );
                }
                //print_r($questionData);exit;
                if($options && $options !="") {
                    $isChoiceQuestion   =   true;
                    $options            =   explode(",", str_replace("\n","", $options));
                    $questionData['isChoiceQuestion']   =   true;
                    $questionData['options']    =   $options;
                }

                $updateQuestion        =   $this->question_model->updateQuestion($questionId,$questionData);

                if($updateQuestion) {
                    $this->status       =   200;
                    $this->message      =   'Question Has been Updated';
                } else {
                    $this->status       =   400;
                    $this->message      =   'Error to update Please try again.';
                    $this->error        =   '';                    
                }
            } else {
                $form_error_mess    =   validation_errors();
                $ajax_res           =   array('subject'=>'fill_fields','data'=>$form_error_mess);
                $this->status       =   400;
                $this->message      =   'Fill the required fields..!';
                $this->error        =   '';
                $this->response['AJAX_RES']     =   $ajax_res;
            }
        } else {
            $this->status       =   402;
            $this->message      =   'User Not Loggedin';
            $this->error        =   '';
        }
    }
    /**
    * add Question
    **/
    protected function addQuestion() {
        if( $this->isUserLoggedin() ) {
            $this->form_validation->set_rules('question_text','Question Text',"trim|required");
            $this->form_validation->set_rules('course_id','Course',"trim|required");
            //$this->form_validation->set_rules('question_tag[]','Tag','required');
            $isChoiceQuestion   =   (isset($this->input_data['question_is_mcq']) && $this->input_data['question_is_mcq'] !=''?true:false);
            //question option is required while it's a choice question
            if($isChoiceQuestion) {
                $this->form_validation->set_rules('options','Options','trim|required');
            }

            if($this->form_validation->run()===TRUE) {
                $questionText       =   $this->input_data['question_text'];
                $forceMcqSubmit     =   isset($this->input_data['forceMcqSubmit']) && $this->input_data['forceMcqSubmit'] == "force"?true:false;
                
                if(!$isChoiceQuestion && !$forceMcqSubmit) {
                    $checkIsMcq         =   $this->checkIsQuestionTextMCQ($questionText);

                    if($checkIsMcq && is_array($checkIsMcq) && count($checkIsMcq)>1) {
                        $ajax_res           =   array('subject'=>'mcq_question','data'=>$checkIsMcq);

                        $this->status       =   203;
                        $this->message      =   "Question is mcq";
                        $this->response['AJAX_RES']     =   $ajax_res;
                        $this->error        =   "";
                        return;
                    }
                }
                $mimeType           =   (isset($this->input_data['MIME_TYPE'])?$this->input_data['MIME_TYPE']:'text/plain');
                if($mimeType != "text/html") {
                    $questionText       =   $this->trimQuestionText($questionText);
                }

                if(isset($this->input_data['options'])) {
                    $options    =   explode(",", str_replace("\n","", $this->input_data['options']));
                }

                $answerOption       =   isset($options)?$options:[];
                $courseId           =   $this->input_data['course_id'];
                $questionType       =   (isset($this->input_data['question_type'])?$this->input_data['question_type']:'text');

                $tagList            =   [];
                if(isset($this->input_data['question_tag'])) {
                    foreach ($this->input_data['question_tag'] as $k => $tag) {
                        $tagList[]  =   $tag;
                    }
                }

                $checkDuplicate     =   (isset($this->input_data['submit_question'])?$this->input_data['submit_question']:'');
                if( $checkDuplicate == "createQuestion" ) {
                    /** check whether question/similar already exist**/
                    include APPPATH . 'third_party/algolia/algoliaSearch.php';
                    $algolia        =   new algoliaSearch();

                    $indexName      =   $this->config->item('indexName');
                    if(strlen($questionText)<500) {
                        $searchResult   =   $algolia->searchFullText($indexName,strip_tags($questionText));
                        $searchResult   =   $searchResult['hits'];
                        
                        if($searchResult && count($searchResult)>0) {
                            $questionTextList       =   [];
                            $questionIds            =   [];
                            foreach ($searchResult as $key => $value) {
                                if(!in_array($value['objectID'], $questionIds)) {
                                    array_push($questionIds, $value['objectID']);
                                    $questionTextParsed     =   $this->parseQuestionText($value['text']);
                                    array_push($questionTextList, $questionTextParsed);
                                }
                            }

                            $ajax_res           =   array('subject'=>'similar_question','data'=>$questionTextList);

                            $this->status       =   400;
                            $this->message      =   "Similar Question Found";
                            $this->response['AJAX_RES']     =   $ajax_res;
                            $this->error        =   "";
                            return;    
                        }
                    }
                    /** check whether question/similar already exist end**/
                }
                $slug               =   $this->prepareQuestionSlug(strip_tags($questionText));
                
                $questionData       =   array(
                    'text'          =>  $questionText,
                    'slug'          =>  $slug,
                    'courseId'      =>  $courseId,
                    'type'          =>  $questionType,
                    'imageUrl'      =>  '',
                    'force'         =>  true,
                    'tags'          =>  $tagList,
                    'htmlContent'   =>  false,
                    'mimeType'      =>  $mimeType
                );
                
                if($isChoiceQuestion) {
                    $questionData['textWithOutOption']  =   true;
                    $questionData['isChoiceQuestion']   =   true;
                    $questionData['options']            =   $answerOption;
                    $optionStr                          =   "";
                    $optCount                           =   1;
                    //adding options as string with question text
                    // foreach ($answerOption as $opt) {
                    //     $optionStr      =   $optionStr." (".$optCount.") ".$opt;
                    //     $optCount++;
                    // }
                    // $questionData['text']               =   $questionText." ".strip_tags($optionStr);
                }

                $addQuestion        =   $this->question_model->createQuestion($questionData);
                if($addQuestion) {
                    $this->status       =   200;
                    $this->message      =   'Question Has been Added';
                } else {
                    $this->status       =   400;
                    $this->message      =   'Error to update Please try again.';
                    $this->error        =   '';                    
                }
            } else {
                $form_error_mess    =   validation_errors();
                $ajax_res           =   array('subject'=>'fill_fields','data'=>$form_error_mess);
                $this->status       =   400;
                $this->message      =   'Fill the required fields..!';
                $this->error        =   '';
                $this->response['AJAX_RES']     =   $ajax_res;
            }
        } else {
            $this->status       =   402;
            $this->message      =   'User Not Loggedin';
            $this->error        =   '';
        }
    }

    public function create($mode = "",$courseId = "") {
        $activeCourseList       =   $this->course_model->getActiveCourse();

        $this->data_arr['active_course']            =   $activeCourseList;
        $this->data_arr['course_id']                =   $courseId;
        if($courseId && $courseId !="") {
            $courseList         =   $this->course_model->getCourseList([$courseId]);
            if($courseList && count($courseList)>0 && isset($courseList[0]['tags'])) {
                $tags             =   $courseList[0]['tags'];
                $this->data_arr['course_tags']  =   $tags;
            }
            $courseName           =     get_course_name($courseList[0]);
        }
        $meta_contents                          =   array(
            'title'             =>  isset($courseName)?$courseName." - Doubt Help Create Question":'Create Doubt Help Question.',
            'page_name'         =>  'Create'
        );
        if(isset($this->input_data['latex_question_text']) && $this->input_data['latex_question_text'] != "") {
            $latex_question_text    =   $this->input_data['latex_question_text'];
            $this->data_arr['latex_question_text']  =   $latex_question_text;
        }

        if(isset($this->input_data['complex_question_text']) && $this->input_data['complex_question_text'] != "") {
            $complex_question_text    =   $this->input_data['complex_question_text'];
            $this->data_arr['complex_question_text']  =   $complex_question_text;
        }

        $this->data_arr['_load_page']               =   array('header','complex_question','footer');
        $this->page_header                          =   'page_header';
        
        $this->data_arr['page_name']                =   'custom';
        $this->data_arr['meta_contents']            =   $meta_contents;

        if($mode == "image") {
            $question_editor                        =   "create_image";
        } else if($mode == "latex") {
            $question_editor                        =   "create_latex";
        } else if($mode == "complex") {
            $question_editor                        =   "create_ckeditor";
        } else {
            $question_editor                        =   "";
        }
        $this->data_arr['question_editor']          =   $question_editor;
        $this->prepareResult();
    }

    public function parseQuestionImage() {

        $uploadedFilePath   =   $this->input_data['FILE_NAME'];
        $parsedResult       =   $this->question_model->parseMathpix($uploadedFilePath);
        $this->status       =   200;
        $this->message      =   'Image parsed';
        $this->response['AJAX_RES']     =   $parsedResult['MESSAGE'];        
    }

    /**
    **/
    protected function loadLatexString() {
        $this->data_arr['_load_page']           =   array('latex_parser');
        $this->page_header                      =   'page_header';
        $page   =   isset($this->input_data['LOAD_PAGE'])?$this->input_data['LOAD_PAGE']:'latex_parser';
        $this->data_arr['_page_name']           =   $page;
        $this->data_arr['page_name']            =   $page;
        $latexString                            =   $this->trimAnswerText($this->input_data['LATEX_STRING']);
        $this->data_arr['latex_string']         =   isset($this->input_data['LATEX_STRING'])?$latexString:'';
    }

    /**
    **/
    protected function trimAnswerText($answerText) {
        $this->try_trim_count   =   $this->try_trim_count+1;
        if(!strpos($answerText, "<math") && !strpos($answerText, "</pre")) {
            $answerText           =   str_replace("<p>&nbsp;</p>","",$answerText);
        }
        if(strpos($answerText, "<pre>") || strpos($answerText, "</pre>")) {
            $answerText           =   str_replace("<pre>", "", $answerText);
            $answerText           =   str_replace("</pre>", "", $answerText);
        }
        $answerText           =   ltrim($answerText,"\r\n");
        $answerText           =   rtrim($answerText,"\r\n");
        if( strpos($answerText, "<p>&nbsp;</p>") && $this->try_trim_count <=10) {
            return $this->trimAnswerText($answerText);
        } else if( strpos($answerText, "\r\n")  && $this->try_trim_count <=10) {
            return $this->trimAnswerText($answerText);
        }
        return $answerText;       
    }

    protected function trimQuestionText($questionText) {
        $this->try_trim_count   =   $this->try_trim_count+1;
      /*  if(!strpos($questionText, "<math")) {
            $questionText           =   ltrim($questionText,"<p>&nbsp;</p>");
            $questionText           =   rtrim($questionText,"<p>&nbsp;</p>");
        }*/
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
    * reverting slug to question text
    * @param slug string
    **/
    protected function convertSlugToString($slug) {
        $text       =   str_replace("-", " " , $slug);
        if($text) {
            return $text;
        } 
        return $slug;
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
    ** prepare question minified link
    **/
    protected function prepareMinLink() {
        return true;
    }

    /**
    * ajax action
    * add answer for question
    * @param question id firstore collection id
    * @param answer text string
    **/
    protected function addAnswer() {
        $userId                     =   $this->data_arr['user_details']['uid'];
        $questionId                 =   $this->input_data['QUESTION_ID'];
        $answerText                 =   $this->trimAnswerText($this->input_data['answer_text']);
        $this->form_validation->set_rules('answer_text','Answer','trim|required');
        
        if($this->form_validation->run()===TRUE) {
            $this->load->model('answer/Answer_model','answer_model');
            // $ansMimeType        =   $this->getTextMimeType($answerText);
            $mimeType           =   isset($this->input_data['mimeType'])?$this->input_data['mimeType']:"text/plain";
            $data               =   array(
                                    'image'         =>  '',
                                    'imageUrl'      =>  '',
                                    'questionId'    =>  $questionId,
                                    'text'          =>  $answerText,
                                    'type'          =>  'text',
                                    'descriptionType'=> 'text',
                                    'mimeType'      =>  $mimeType,
                                    'description'   =>  ''
                                );
            $addAnswer          =   $this->answer_model->addQuestionAnswer($data);
            if($addAnswer['STATUS'] == 200) {
                $this->status       =   200;
                $this->message      =   'Answer Added';
                $this->error        =   '';
            } else {
                $this->status       =   400;
                $this->message      =   'Error';
                $this->error        =   '';
            }
        } else {
            $form_error_mess    =   validation_errors();
            $ajax_res           =   array('subject'=>'fill_fields','data'=>$form_error_mess);
            $this->status       =   400;
            $this->message      =   'Please add answer';
            $this->error        =   '';
            $this->response['AJAX_RES']     =   $ajax_res;
        }
    }

    /**
    * identify answer mime Type
    **/
    protected function getTextMimeType($text) {
        $mimeType           =   "text/plain";
        if( strpos($text, "begin") && strpos($text, "end")) {
            $mimeType       =   "application/x-latex";
        } else if($text != strip_tags($text)) {
            $mimeType       =   "text/html";
        } else {
            $mimeType           =   "text/plain";
        }
        return $mimeType;
    }

    /**
    *upvoteDownVoteAnswe
    **/
    protected function voteAnswer() {

        if (isset($this->input_data['ANSWER_ID'])) {
            $answerId       =   $this->input_data['ANSWER_ID'];
            $voteType       =   $this->input_data['VOTE_TYPE'];
            $voted          =   false;

            $this->load->model('answer/Answer_model','answer_model');
            if($voteType == "UP") {
                $voted          =   $this->answer_model->upVoteAnswer($answerId);
            } else if($voteType == "DOWN") {
                $voted          =   $this->answer_model->downVoteAnswer($answerId);
            } else {

            }

            if($voted) {
                $this->status       =   200;
                $this->message      =   "success";
                $this->error        =   '';
            } else { 
                $this->status       =   400;
                $this->message      =   'Error';
                $this->error        =   '';
            }
        }
        
    }

    /**
    ** question upvote
    **/
    protected function questionUpvote () {
        $userId                     =   $this->data_arr['user_details']['uid'];
        $questionId                 =   $this->input_data['QUESTION_ID'];
        if (isset($this->input_data['QUESTION_ID'])&& $questionId) {
            $upVote                 =   $this->question_model->questionUpVote($questionId,$userId);
            if($upVote) {

                $this->status       =   $upVote['STATUS'];
                $this->message      =   $upVote['MESSAGE'];
                $this->error        =   '';
            } else { 
                $this->status       =   400;
                $this->message      =   'Error';
                $this->error        =   '';
            }
        }
    }

    protected function questionDownvote () {
        $userId                     =   $this->data_arr['user_details']['uid'];
        $questionId                 =   $this->input_data['QUESTION_ID'];
        if (isset($this->input_data['QUESTION_ID'])&& $questionId) {
            $downVote               =   $this->question_model->questionDownVote($questionId,$userId);
            if($downVote) {
                $this->status       =   $downVote['STATUS'];
                $this->message      =   $downVote['MESSAGE'];
                $this->error        =   '';
            } else { 
                $this->status       =   400;
                $this->message      =   'Error';
                $this->error        =   '';
            }
        }
    }

    public function questionFavorite () {
        $questionId                 =   $this->input_data['QUESTION_ID'];
        $userId                     =   $this->data_arr['user_details']['uid'];
        if (isset($this->input_data['QUESTION_ID'])&& $questionId) {
           $favorite                =  $this->question_model->questionFavorite($questionId,$userId);
           if ($favorite) {
                $this->status       =   200;
                $this->message      =   'Added to favorite';
                $this->error        =   '';
            } else { 
                $this->status       =   400;
                $this->message      =   'Error';
                $this->error        =   '';
            } 
        }
    }

    /**
    ** delete question
    ** @param $questionId int post
    **/
    protected function deleteQuestion() {
        $questionId             =   $this->input_data['QUESTION_ID'];
        $deleteQuestion         =   $this->question_model->deleteQuestion($questionId);
        if($deleteQuestion) {
              /** delete the object from algolia**/
            include APPPATH . 'third_party/algolia/algoliaSearch.php';
            $algolia            =   new algoliaSearch();
            $indexName          =   $this->config->item('indexName');
            $response           =   $algolia->deleteQuestion($indexName,$questionId);

            $this->status       =   200;
            $this->message      =   "Question has been deleted";
            $this->error        =   "";
        } else {
            $this->status       =   203;
            $this->message      =   "Can't delete, Please try again.";
            $this->error        =   "";
        }
    }

    /**
    ** delete question
    ** @param $questionId int post
    **/
    protected function deleteAnswer() {
        $this->load->model('answer/Answer_model','answer_model');
        $questionId             =   $this->input_data['QUESTION_ID'];
        $answerId              =   $this->input_data['ANSWER_ID'];

        $deleteQuestion         =   $this->answer_model->deleteAnswer($answerId,$questionId);
        if($deleteQuestion) {
            $this->status       =   200;
            $this->message      =   "Answer has been deleted";
            $this->error        =   "";
        } else {
            $this->status       =   203;
            $this->message      =   "Can't delete, Please try again.";
            $this->error        =   "";
        }
    }

    /**
    * list user questions
    **/
    protected function getUserQuestions() {
        //determine the total number of pages available
        if( !isset($this->input_data['page']) ) {
            $current_page       =   1;    
        } else {
            $current_page       =   $this->input_data['page'];
        }

        if($this->isUserLoggedin()) {
            $userId         =   $this->data_arr['user_details']['uid'];
            $userFavaorites =   $this->getUserFavouriteQuestions($userId);
        }

        $this->load->model('answer/Answer_model','answer_model');
        $this->load->model('user/User_model','user_model');
        $this->load->model('course/Course_model','course_model');

        $courseId             =   $this->input_data['COURSE_ID'];
        $userId               =   $userId;
        $user_question        =   $this->question_model->getUserQuestion($userId,$courseId);
        $course_data            =   $this->course_model->getCourseList($courseId);
        $course_slug            =   isset($this->input_data['course_slug'])?$this->input_data['course_slug']:false;
        $load_question          =   isset($this->input_data['load_question'])?$this->input_data['load_question']:"user";
        
        $limit                  =   $this->row_per_page;
        $number_of_result       =   isset($user_question) && is_array($user_question)?count($user_question):0;
        $number_of_page         =   ceil ($number_of_result / $limit);
        $offset                 =   ($current_page-1)*$limit;

        $count                  =   0;
        $user_question_arr    =   [];
        $user_arr               =   [];
        $isLatex                =   false;
        if($user_question && is_array($user_question)) {
            $userIdArr          =   [];
            foreach ($user_question as $key => $question) {
                $answerArr          =   [];
                if($count < $offset ) {
                    $count++;
                    continue;
                }
                if($count >= $offset+$limit) {
                    break;
                }
                $count++;
                $user_question_arr[$key]      =   $question;
                $answerArr      =   $this->answer_model->getQuestionAnswers($question['questionId']);

                //check question type for latex parsing
                $mimeType       =   (isset($question['mimeType'])?$question['mimeType']:'text/plain');
                if($mimeType&&$mimeType=="application/x-latex") {
                    $isLatex        =   true;
                }
                //check question type for latex parsing

                if(!in_array($question['userId'], $userIdArr)) {
                    array_push($userIdArr,$question['userId']);
                }

                if(isset($userId)) {
                    $votes      =   $this->question_model->getUserQuestionVote($question['questionId'],$userId);
                    $question['userVote']   =   $votes;
                }
                if(isset($userFavaorites) && in_array($question['questionId'], $userFavaorites)) {
                    $user_question_arr[$key]['isFavourite']        =   true;
                } else {
                    $user_question_arr[$key]['isFavourite']        =   false;
                }
                
                $user_question_arr[$key]['answer']         =  $answerArr;

                $answeredUsers          =   [];
                if( $answerArr && count($answerArr)>0 ) {
                    foreach ($answerArr as $ak => $ans) {
                        if(isset($ans['userId'])) {
                            array_push($userIdArr, $ans['userId']);
                        }
                        if(isset($ans['userId']) && !in_array($ans['userId'], $answeredUsers)) {
                            array_push($answeredUsers, $ans['userId']);
                        }
                    }
                }
                
                $user_question_arr[$key]['answeredUsers']         =   $answeredUsers;

                if((isset($question['isChoiceQuestion']) && $question['isChoiceQuestion'] == true) || (isset($question['textWithOutOption']) && $question['textWithOutOption'] == true)) {
                    $questionText           =   $this->parseQuestionText($question['text'],$question['options']);
                    $user_question_arr[$key]['text']  =   $questionText;
                }

            }

            $users  =   $this->user_model->getUsersByIds($userIdArr);

            if(is_array($users) && count($users)) {
                foreach ($users as $key => $value) {
                    $user_arr[$value['userId']]     =   $value;
                }
            }
        }

        $this->data_arr['current_page']         =   $current_page;
        $this->data_arr['total_page']           =   $number_of_page;
        $this->data_arr['course_slug']          =   isset($course_slug)?$course_slug:$courseId;
        $this->data_arr['load_question']        =   $load_question;
        $this->data_arr['course_id']            =   isset($courseId)?$courseId:'';
        $this->data_arr['user_list']            =   $user_arr;
        $this->data_arr['course_question']      =   $user_question_arr;
        $this->data_arr['course_tags']          =   $course_data[0]['tags'];
        $this->data_arr['is_latex']             =   $isLatex;
    }

    /**
    ** get course questions
    **/
    protected function getCourseQuestions() {
        //determine the total number of pages available
        if( !isset($this->input_data['page']) ) {
            $current_page       =   1;    
        } else {
            $current_page       =   $this->input_data['page'];
        }

        if($this->isUserLoggedin()) {
            $userId         =   $this->data_arr['user_details']['uid'];
            $userFavaorites =   $this->getUserFavouriteQuestions($userId);
        }

        $this->load->model('answer/Answer_model','answer_model');
        $this->load->model('user/User_model','user_model');
        $this->load->model('course/Course_model','course_model');

        $courseId               =   $this->input_data['COURSE_ID'];
        $course_question        =   $this->question_model->getCourseQuestion($courseId);
        $course_data            =   $this->course_model->getCourseList($courseId);
        $course_slug            =   isset($this->input_data['course_slug'])?$this->input_data['course_slug']:false;
        $load_question          =   isset($this->input_data['load_question'])&&$this->input_data['load_question']!=""?$this->input_data['load_question']:"public";
        
        $limit                  =   $this->row_per_page;
        $number_of_result       =   isset($course_question) && is_array($course_question)?count($course_question):0;
        $number_of_page         =   ceil ($number_of_result / $limit);
        $offset                 =   ($current_page-1)*$limit;

        $count                  =   0;
        $course_question_arr    =   [];
        $user_arr               =   [];
        $isLatex                =   false;
        if($course_question && is_array($course_question)) {
            $userIdArr          =   [];
            foreach ($course_question as $key => $question) {
                $answerArr          =   [];
                if($count < $offset ) {
                    $count++;
                    continue;
                }
                if($count >= $offset+$limit) {
                    break;
                }
                $count++;
                $course_question_arr[$key]      =   $question;
                $answerArr      =   $this->answer_model->getQuestionAnswers($question['questionId']);

                //check question type for latex parsing
                $mimeType       =   (isset($question['mimeType'])?$question['mimeType']:'text/plain');
                if($mimeType&&$mimeType=="application/x-latex") {
                    $isLatex        =   true;
                }
                //check question type for latex parsing

                if(!in_array($question['userId'], $userIdArr)) {
                    array_push($userIdArr,$question['userId']);
                }

                if(isset($userId)) {
                    $votes      =   $this->question_model->getUserQuestionVote($question['questionId'],$userId);
                    $question['userVote']   =   $votes;
                }
                if(isset($userFavaorites) && in_array($question['questionId'], $userFavaorites)) {
                    $course_question_arr[$key]['isFavourite']        =   true;
                } else {
                    $course_question_arr[$key]['isFavourite']        =   false;
                }
                
                $course_question_arr[$key]['answer']         =  $answerArr;

                $answeredUsers          =   [];
                if( $answerArr && count($answerArr)>0 ) {
                    foreach ($answerArr as $ak => $ans) {
                        if(isset($ans['userId'])) {
                            array_push($userIdArr, $ans['userId']);
                        }
                        if(isset($ans['userId']) && !in_array($ans['userId'], $answeredUsers)) {
                            array_push($answeredUsers, $ans['userId']);
                        }
                    }
                }
                
                $course_question_arr[$key]['answeredUsers']         =   $answeredUsers;

                if((isset($question['isChoiceQuestion']) && $question['isChoiceQuestion'] == true) || (isset($question['textWithOutOption']) && $question['textWithOutOption'] == true)) {
                    $questionText           =   $this->parseQuestionText($question['text'],$question['options']);
                    $course_question_arr[$key]['text']  =   $questionText;
                }
                // $course_question_arr[$key]['userDetails']     =  $userDetails;
            }

            $users  =   $this->user_model->getUsersByIds($userIdArr);

            if(is_array($users) && count($users)) {
                foreach ($users as $key => $value) {
                    $user_arr[$value['userId']]     =   $value;
                }
            }
        }

        $this->data_arr['current_page']         =   $current_page;
        $this->data_arr['total_page']           =   $number_of_page;
        $this->data_arr['course_slug']          =   isset($course_slug)?$course_slug:$courseId;
        $this->data_arr['load_question']        =   $load_question;
        $this->data_arr['course_id']            =   $courseId;
        $this->data_arr['user_list']            =   $user_arr;
        $this->data_arr['course_question']      =   $course_question_arr;
        $this->data_arr['course_tags']          =   $course_data[0]['tags'];
        $this->data_arr['is_latex']             =   $isLatex;
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


    /**
    **/
    protected function getUserFavouriteQuestions($userId) {
        return $this->question_model->getUserFavourites($userId);
    }

    /**
    * question details page
    * @param $questionId int
    **/
    public function questionDetails($questionId) {
        $this->load->model('answer/Answer_model','answer_model');

        $slugExist      =   $this->question_model->isQuestionSlugExist(trim($questionId));
        if($slugExist) {
            $question       =   $slugExist[0];
        } else if($this->question_model->isQuestionIdExist(trim($questionId))) {
            $question       =   $this->question_model->getQuestionById([$questionId]);
            $question       =   $question[0];
        } else {
            $questionSearchString       =   $this->convertSlugToString($questionId);
            redirect('home/?action=quick_search&search_question='.urlencode($questionSearchString));
        }

        $course_question=   [];
        $userIdArr      =   [];
        
        if($this->isUserLoggedin()) {
            $userId         =   $this->data_arr['user_details']['uid'];
            $userFavaorites =   $this->getUserFavouriteQuestions($userId);
        }

        //getting course details of the question
        $courseId           =   isset($question['courseId'])?$question['courseId']:false;
        $course_details     =   $courseId?$this->course_model->getCourseList([$courseId]):flase;
        if($course_details){
            $course_details             =   $course_details;
        }

        $answerArr      =   $this->answer_model->getQuestionAnswers($questionId);

        if(isset($userId)) {
            $votes      =   $this->question_model->getUserQuestionVote($question['questionId'],$userId);
            $question['userVote']   =   $votes;
        }
        if(isset($userFavaorites) && in_array($question['questionId'], $userFavaorites)) {
            $question['isFavourite']        =   true;
        } else {
            $question['isFavourite']        =   false;
        }

        if((isset($question['isChoiceQuestion']) && $question['isChoiceQuestion'] == true) || (isset($question['textWithOutOption']) && $question['textWithOutOption'] == true)) {
            $questionText           =   $this->parseQuestionText($question['text']);
            $question['text']       =   $questionText;
        }

        $answeredUsers              =   [];
        if ($answerArr&&is_array($answerArr)) {
            $answeredUsers          =   [];
            foreach($answerArr as $k=>$ans) {
                if(!in_array($ans['userId'], $userIdArr)) {
                    array_push($userIdArr,$ans['userId']);
                }

                if(!in_array($ans['userId'], $answeredUsers)) {
                    array_push($answeredUsers, $ans['userId']);
                }
            }
        }

        $question['answeredUsers']          =   $answeredUsers;

        if(!in_array($question['userId'], $userIdArr)) {
            array_push($userIdArr,$question['userId']);
        }

        $users  =   $this->user_model->getUsersByIds($userIdArr);
        if(is_array($users) && count($users)) {
            $user_arr       =   [];
            foreach ($users as $key => $value) {
                $user_arr[$value['userId']]     =   $value;
            }
        }

        $question['answer']                     =   $answerArr;

        //check question type for latex parsing
        $isLatex        =   false;
        $mimeType       =   (isset($question['mimeType'])?$question['mimeType']:'text');
        if($mimeType&&$mimeType=="application/x-latex") {
            $isLatex        =   true;
        }
        //check question type for latex parsing

        // set meta contents for question details page
        $meta_contents                          =   array(
            'meta_image'        =>  $question['type'] == 'text'?'':isset($question['imageUrl'])?$question['imageUrl']:'',
            'page_name'         =>  'Question Details',

            'meta_description'  =>  'Doubt Help - Question :'.strip_tags($question['text']),
            'title'             =>  (!$isLatex&&isset($question['text'])&&$question['text']!=''?strip_tags(substr($question['text'], 0, 60))."- Doubt Help Question":'Doubt Help Question'),
            'meta_keywords'     =>  ''.strip_tags(substr($question['text'], 0, 60)),
           'og_description'     =>  ''.strip_tags($question['text']),

        );
        $this->data_arr['user_list']            =   $user_arr;
        $this->data_arr['question_details']     =   $question;
        $this->data_arr['course_details']       =   $course_details;
        $this->data_arr['_load_page']           =   array('header','question_details','footer');
        $this->data_arr['page_header']          =   'page_header';

        $this->data_arr['page_name']            =   'custom';

        $this->data_arr['meta_contents']        =   $meta_contents;
        $this->prepareResult();
     }

 /**
    * check question text is mcq or not 
    **/
    protected function checkQuestionTextMCQ(){
        $questionText =  $this->input_data['TEXT'];
        if($questionText == '') {
          
        }
        $questionSplit  =   [$questionText];
        $options        =   [];
        if(strpos($questionSplit[0], '[A]') && strpos($questionSplit[0], '[B]') && strpos($questionSplit[0], '[C]')) {
            $question   =   explode("[A]", $questionSplit[0]);
            $option     =   explode("[B]", $question[1]);
            $options[]  =   $option[0];
            $option     =   explode("[C]", $option[1]);
            $options[]  =   $option[0];
            if(strpos(strtolower($questionSplit[0]), '[D]')){
            $option     =   explode("[D]", $option[1]);
            $options[]  =   $option[0];
            }
            $options[]  =   $option[1];
           
        } else if(strpos($questionSplit[0], '(A)') && strpos($questionSplit[0], '(B)') && strpos($questionSplit[0], '(C)')) {
            $question   =   explode("(A)", $questionSplit[0]);
            $option     =   explode("(B)", $question[1]);
            $options[]  =   $option[0];
            $option     =   explode("(C)", $option[1]);
            $options[]  =   $option[0];
            if(strpos(strtolower($questionSplit[0]), '(D)')){
            $option     =   explode("(D)", $option[1]);
            $options[]  =   $option[0];
            }
            $options[]  =   $option[1];
            
        } else if(strpos($questionSplit[0], 'A)') && strpos($questionSplit[0], 'B)') && strpos($questionSplit[0], 'C)')) {
            $question   =   explode("A)", $questionSplit[0]);
            $option     =   explode("B)", $question[1]);
            $options[]  =   $option[0];
            $option     =   explode("C)", $option[1]);
            $options[]  =   $option[0];
            if(strpos(strtolower($questionSplit[0]), 'D)')){
            $option     =   explode("D)", $option[1]);
            $options[]  =   $option[0];
            }
            $options[]  =   $option[1];
           
        } else if(strpos($questionSplit[0], 'A.') && strpos($questionSplit[0], 'B.') && strpos($questionSplit[0], 'C.')) {
            $question   =   explode("A.", $questionSplit[0]);
            $option     =   explode("B.", $question[1]);
            $options[]  =   $option[0];
            $option     =   explode("C.", $option[1]);
            $options[]  =   $option[0];
            if(strpos(strtolower($questionSplit[0]), 'D.')){
            $option     =   explode("D.", $option[1]);
            $options[]  =   $option[0];
            }
            $options[]  =   $option[1];
           
        } else if(strpos(strtolower($questionSplit[0]), '(a)') && strpos(strtolower($questionSplit[0]), '(b)') && strpos(strtolower($questionSplit[0]), '(c)')) {
            $question   =   explode("(a)", $questionSplit[0]);
            $option     =   explode("(b)", $question[1]);
            $options[]  =   $option[0];
            $option     =   explode("(c)", $option[1]);
            $options[]  =   $option[0];
            if(strpos(strtolower($questionSplit[0]), '(d)')){
            $option     =   explode("(d)", $option[1]);
            $options[]  =   $option[0];
            }
            $options[]  =   $option[1];

        } else if(strpos(strtolower($questionSplit[0]), 'a)') && strpos(strtolower($questionSplit[0]), 'b)') && strpos(strtolower($questionSplit[0]), 'c)')) {
            $question   =   explode("a)", $questionSplit[0]);
            $option     =   explode("b)", $question[1]);
            $options[]  =   $option[0];
            $option     =   explode("c)", $option[1]);
            $options[]  =   $option[0];
            if(strpos(strtolower($questionSplit[0]), 'd)')){
            $option     =   explode("d)", $option[1]);
            $options[]  =   $option[0];
            }
            $options[]  =   $option[1];
           
        } else if(strpos(strtolower($questionSplit[0]), 'a.') && strpos(strtolower($questionSplit[0]), 'b.') && strpos(strtolower($questionSplit[0]), 'c.')) {
            $question   =   explode("a.", $questionSplit[0]);
            $option     =   explode("b.", $question[1]);
            $options[]  =   $option[0];
            $option     =   explode("c.", $option[1]);
            $options[]  =   $option[0];
            if(strpos(strtolower($questionSplit[0]), 'd.')){
            $option     =   explode("d.", $option[1]);
            $options[]  =   $option[0];
            }
            $options[]  =   $option[1];
           
        } else if( strpos($questionSplit[0], '(1)') && strpos($questionSplit[0], '(2)') && strpos($questionSplit[0], '(3)')) {
            $question   =   explode("(1)", $questionSplit[0]);
            $option     =   explode("(2)", $question[1]);
            $options[]  =   $option[0];
            $option     =   explode("(3)", $option[1]);
            $options[]  =   $option[0];
            if(strpos(strtolower($questionSplit[0]), '(4)')){
            $option     =   explode("(4)", $option[1]);
            $options[]  =   $option[0];
            }
            $options[]  =   $option[1];
           
        } else if( strpos($questionSplit[0], '1)') && strpos($questionSplit[0], '2)') && strpos($questionSplit[0], '3)')) {
            $question   =   explode("1)", $questionSplit[0]);
            $option     =   explode("2)", $question[1]);
            $options[]  =   $option[0];
            $option     =   explode("3)", $option[1]);
            $options[]  =   $option[0];
            if(strpos(strtolower($questionSplit[0]), '4)')){
            $option     =   explode("4)", $option[1]);
            $options[]  =   $option[0];
            }
            $options[]  =   $option[1];
           
        } else if( strpos($questionSplit[0], "1.") && strpos($questionSplit[0],"2.") && strpos($questionSplit[0],"3.")) {
            $question   =   explode("1.", $questionSplit[0]);
            $option     =   explode("2.", $question[1]);
            $options[]  =   $option[0];
            $option     =   explode("3.", $option[1]);
            $options[]  =   $option[0];
            if(strpos(strtolower($questionSplit[0]), '4.')){
            $option     =   explode("4.", $option[1]);
            $options[]  =   $option[0];
            }
            $options[]  =   $option[1];
          
        } else if( strpos($questionSplit[0], '(I)') && strpos($questionSplit[0], '(I)') && strpos($questionSplit[0], 'III.')) {
            $question   =   explode("(I)", $questionSplit[0]);
            $option     =   explode("(II)", $question[1]);
            $options[]  =   $option[0];
            $option     =   explode("(III)", $option[1]);
            $options[]  =   $option[0];
            if(strpos(strtolower($questionSplit[0]), '(IV)')){
            $option     =   explode("(IV)", $option[1]);
            $options[]  =   $option[0];
            }
            $options[]  =   $option[1];
           
        } else if( strpos($questionSplit[0], 'I)') && strpos($questionSplit[0], 'II)') && strpos($questionSplit[0], 'III)')) {
            $question   =   explode("I)", $questionSplit[0]);
            $option     =   explode("II)", $question[1]);
            $options[]  =   $option[0];
            $option     =   explode("III)", $option[1]);
            $options[]  =   $option[0];
            if(strpos(strtolower($questionSplit[0]), 'IV)')){
            $option     =   explode("IV)", $option[1]);
            $options[]  =   $option[0];
            }
            $options[]  =   $option[1];
            
        } else if( strpos($questionSplit[0], 'I.') && strpos($questionSplit[0], 'II.') && strpos($questionSplit[0], 'III.')) {
            $question   =   explode("I.", $questionSplit[0]);
            $option     =   explode("II.", $question[1]);
            $options[]  =   $option[0];
            $option     =   explode("III.", $option[1]);
            $options[]  =   $option[0];
            if(strpos(strtolower($questionSplit[0]), 'IV.')){
            $option     =   explode("IV.", $option[1]);
            $options[]  =   $option[0];
            }
            $options[]  =   $option[1];
           
        } else if( strpos(strtolower($questionSplit[0]), '(i)') && strpos(strtolower($questionSplit[0]), '(ii)') && strpos(strtolower($questionSplit[0]), '(iii)')) {
            $question   =   explode("(i)", $questionSplit[0]);
            $option     =   explode("(ii)", $question[1]);
            $options[]  =   $option[0];
            $option     =   explode("(iii)", $option[1]);
            $options[]  =   $option[0];
            if(strpos(strtolower($questionSplit[0]), '(iv)')){
            $option     =   explode("(iv)", $option[1]);
            $options[]  =   $option[0];
            }
            $options[]  =   $option[1];
           
        } else if( strpos(strtolower($questionSplit[0]), 'i)') && strpos(strtolower($questionSplit[0]), 'ii)') && strpos(strtolower($questionSplit[0]), 'iii)')) {
            $question   =   explode("i)", $questionSplit[0]);
            $option     =   explode("ii)", $question[1]);
            $options[]  =   $option[0];
            $option     =   explode("iii)", $option[1]);
            $options[]  =   $option[0];
            if(strpos(strtolower($questionSplit[0]), 'iv)')){
            $option     =   explode("iv)", $option[1]);
            $options[]  =   $option[0];
            }
            $options[]  =   $option[1];
            
        } else if( strpos(strtolower($questionSplit[0]), 'i.') && strpos(strtolower($questionSplit[0]), 'ii.') && strpos(strtolower($questionSplit[0]), 'iii.')) {
            $question   =   explode("i.", $questionSplit[0]);
            $option     =   explode("ii.", $question[1]);
            $options[]  =   $option[0];
            $option     =   explode("iii.", $option[1]);
            $options[]  =   $option[0];
            if(strpos(strtolower($questionSplit[0]), 'iv.')){
            $option     =   explode("iv.", $option[1]);
            $options[]  =   $option[0];
            }
            $options[]  =   $option[1];
            
        }

       // return $questionSplit[0];
        $this->response['TEXT']     =   $question[0];
        $this->response['OPTIONS']     =   $options;
    }

    /**
    ** prepare result
    ** @param $user_arr
    ** @return template page
    **/
    protected function prepareResult() {
        if( isset($this->input_data['AJAX'])) {
            if($this->input_data['TYPE'] == 'DT'  || $this->input_data['TYPE'] == 'HTM') {
                $this->data_arr['_load_page']           =   isset($this->input_data['LOAD_PAGE'])?array('header',$this->input_data['LOAD_PAGE'],'footer'):$this->dt_page;
                $this->template->datatable_template( $this->data_arr);
            } else if( $this->input_data['TYPE'] == 'ACTION' ) {
                $this->response['STATUS']       =   $this->status;
                $this->response['MESSAGE']      =   $this->message;
                $this->response['ERROR']        =   $this->error;
                print_r(json_encode($this->response));die();
            }
        } else {
            $this->data_arr['page_header']          =   $this->page_header?$this->page_header:'home_header';
            $page_name                              =   isset($this->data_arr['page_name'])?$this->data_arr['page_name']:'home';
            $meta_contents                          =   isset($this->data_arr['meta_contents'])?$this->data_arr['meta_contents']:[];

            $this->data_arr['seo_contents']         =   $this->loadHeaderContents($page_name,$meta_contents);
            $this->template->user_template( $this->data_arr ); 
        }
    }
}