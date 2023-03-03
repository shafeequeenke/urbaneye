

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bulkupload extends MY_Controller {

    public function __construct() {
    	parent::__construct();
        //load required models
    	$this->load->model('bulkupload/Bulkupload_model','bulkupload_model');
    	//set template params
    	$this->data_arr['_module_name'] 		=	'bulkupload';
		$this->data_arr['_load_page'] 			=	array('bulk_upload');
        $this->data_arr['_page_name']           =   'bulk_upload';        
        $this->data_arr['page_header_name']     =   'Bulk Upload';
        $this->data_arr['page_header_link']     =   'bulkupload';
        //input parameters
        $this->input_data 		= 	$this->input->post();
        $this->get_data         =   $this->input->get();
        //define actions for ajax request
		$this->action 			=	isset($this->input_data['ACTION'])?$this->input_data['ACTION']:'';

        // if( !$this->isUserLoggedin() && !isset($this->input_data['AJAX'])) {
        //     redirect(base_url());
        // }     
    }
    /**
    ** User index
    **/
    public function index() {
    	switch ($this->action) {
    		case 'LOGIN':
    			$this->auth();
    			break;
    		case 'APPROVE_QUESTION':
    			$this->approveQuestion();
    			break;
            case 'PARSE_QUESTION_FILE':
                $this->parseQuestionFile();
                break;
            case 'ADD_QUESTION':
                $this->addQuestion();
                break;
            default:    
                $this->getQuestions();
    			break;
    	}
    	$this->prepareResult();
    }

     /**
    * Upload Questions File
    **/
    public function uploadQuestions(){
        if( $this->isUserLoggedin() ) {
            $config['upload_path']          = './uploads';
            $config['allowed_types']        = 'docx|DOCX|doc|DOC|pdf|PDF';
            $config['max_size']             = '';
            $config['file_name'] = $_FILES['file']['name'];
            $this->load->library('upload', $config);
            if(!$this->upload->do_upload('file')) 
            {
                $this->session->set_flashdata('error', 'Something Went wrong.Try Again.');
            }
            else 
            {
                $filename =$this->config->item('question_file_path') . $this->upload->data('file_name') ;
                $command = escapeshellcmd($this->config->item('convert_python_file') . escapeshellarg(json_encode($filename)));
                $output = shell_exec($command);
                $output=iconv(mb_detect_encoding($output, mb_detect_order(), true), "UTF-8", $output);
                $data=explode("[[",str_replace("'",'',$output));
                $questions=explode(",",$data[0]);
                $options=explode("], [",$data[1]);
                foreach ($questions as $key => $value) {
                    $info=array(
                        'question_text'=>str_replace('[','',str_replace(']','',$value)),
                        'options'=>$options[$key],
                        'key'=>$key
                    );
                    $this->addQuestion($info);
                }
                $this->session->set_flashdata('success', 'Questions Uploaded Successfully.');
              
            }
        } else {
            $this->session->set_flashdata('error', 'Please Login Before Upload.');
        }
        redirect('bulk-upload');
    }

    /**
    * add Question
    **/
    protected function addQuestion($question) {
        
            $questionText       =   $question['question_text'];
            if(isset($question['options'])) {
                $options        =   explode(",", str_replace("\n","", $question['options']));
                if($options[count($options)-1]==' ' || $options[count($options)-1]==' ]]')
                        array_pop($options);
            }
            $answerOption       =   isset($options)?$options:[];
            $questionData       =   array(
                'text'          =>  $questionText,
                'options'       =>   $answerOption
                    );
            $successList        = [];
            $errorList          = [];
            $questionData       =   $this->prepareQuestionArray($questionData);
            if(!empty($questionData)) {
                $addQuestion        =   $this->bulkupload_model->submitQuestion($questionData);
                if($addQuestion) {
                    $questionData['questionId']     =   $addQuestion;
                    $updateQuestion     =   $this->bulkupload_model->updateQuestion($addQuestion,$questionData);    
                    if($updateQuestion) {
                        array_push($successList,$question['key']);
                    } else {
                        array_push($errorList,$question['key']);
                    }
                }
            } else {
                array_push($errorList,$question['key']);
            }
            
       
    }

 /**
    * prepare question array
    * @param $param array
    * @return question array
    **/
    protected function prepareQuestionArray($param) {
        $data       =   [
            'approvalStatus'=>  false,
            'choiceQuestion'=>  true,
            'mimeType'      =>  "text/plain",
            'date'          =>  $this->prepareFirebaseTimestring(),
            'text'          =>  $param['text'],
            'options'       =>  $param['options'],
            'userId'        =>  ($this->userId != ""?$this->userId:"P07DxO4rWwMQXLehMCHrypmRnWO2"),
            'questionId'    =>  "",
        ];
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
    *Upload file that contain Questions
    **/
    public function parseQuestionFile() {
        $command = escapeshellcmd("/xampp/htdocs/noteszen-web/application/python/show.py" . escapeshellarg(json_encode($filename)));
        $output = shell_exec($command);
        $this->status       =   200;
        $this->message      =   'file parsed';
        $this->response['AJAX_RES']     =   $output;    
    }
    /**
    *List Questions
    **/
    public function getQuestions() { 
        $this->load->model('bulkupload/Bulkupload_model','bulkupload_model');
        $this->load->model('course/course_model','course_model');
        $this->data_arr['_load_page']   =   array('bulk_upload','footer');
        $questions =$this->bulkupload_model->getAllQuestion();
        $activeCourseList       =   $this->course_model->getActiveCourse();
        $questionsArray=[];
        foreach ($questions as $key => $value) {
            $questionsArray[]=[
                'id'=>$value['questionId'],
                'text'=>$value['text'],
                'date'=>date('d-M-Y h:i a',$value['date']/1000),
                'options'=>$value['choiceQuestion']?$value['options']:'',
                'userId'=>$value['userId']
            ];
        }
        $this->data_arr['active_course']        =   $activeCourseList;
        $this->data_arr['load_question']        =   $questionsArray;
        $this->data_arr['page_name']            =   'custom';
        $this->data_arr['page_header_name']     =   'Bulk Upload';
        $this->meta_data                        =   array(
            'page_name'         =>      'Bulk Upload',
            'title'             =>      "Doubt Help Upload",
            'meta_description'  =>      "Doubt Help Bulk Upload Questions"
            );        
            $this->prepareResult();   
    }

    /**
    * approve Question
    **/
    protected function approveQuestion() {
        $questionIdList =   $this->input_data['questionIdList'];
        $addedQuestionIds=  [];
        $courseId       =   $this->input_data['courseId'];
        $userId         =   $this->data_arr['user_details']['uid'];
        $questionIdList =   explode(',',$questionIdList);
        $authParams     =   $this->bulkupload_model->getUserAuthData('P07DxO4rWwMQXLehMCHrypmRnWO2'); 
        $authParam      =   array(
            'app_id'    =>   $authParams[0]['app_id'], 
            'secret'    =>   $authParams[0]['app_secret'], 
            'api_key'   =>   $authParams[0]['api_key']
        );    
        foreach ($questionIdList as $key => $value) {
            $question   =   $this->bulkupload_model->getQuestionById($value);
            $data       = array(
                'text'          =>  $question[0]['text'],
                'course_id'     =>  $courseId,
                'type'          =>  'text',
                'force'         =>  true,
                'mimeType'      =>  $question[0]['mimeType'],
                'choiceQuestion'=>  $question[0]['choiceQuestion'],
                'options'       =>  $question[0]['choiceQuestion']?$question[0]['options']:[],
            );
            $addQuestion    =  $this->bulkupload_model->addQuestionByAPI($data,$authParam);
            if($addQuestion['STATUS']==200)
            {
                $addedQuestionIds[]     =   $value;
            }
        }
        foreach ($addedQuestionIds as $key => $value) {
            $question   =   $this->bulkupload_model->getQuestionById($value);
            $data       = array(
                'text'          =>  $question[0]['text'],
                'questionId'    =>  $question[0]['questionId'],
                'userId'        =>  $question[0]['userId'],
                'approvalStatus'=>  true,
                'date'          =>  $question[0]['date'],
                'mimeType'      =>  $question[0]['mimeType'],
                'choiceQuestion'=>  $question[0]['choiceQuestion'],
                'options'       =>  $question[0]['choiceQuestion']?$question[0]['options']:[],
            );
            $updateQuestion     =   $this->bulkupload_model->updateApprovalStatusApi($value,$data);
        }
        //$this->prepareResult();
    }
	/**
    ** prepare result
    ** @param $user_arr
    ** @return template page
    **/
    protected function prepareResult() {
        if( isset($this->input_data['AJAX'])) {
            if($this->input_data['TYPE'] == 'DT'  || $this->input_data['TYPE'] == 'HTM') {
                $this->data_arr['_load_page']           =   isset($this->input_data['LOAD_PAGE'])?array($this->input_data['LOAD_PAGE']):$this->dt_page;

                $this->template->datatable_template( $this->data_arr );
            } else if( $this->input_data['TYPE'] == 'ACTION' ) {
                $this->response['STATUS']       =   $this->status;
                $this->response['MESSAGE']      =   $this->message;
                $this->response['ERROR']        =   $this->error;
                print_r(json_encode($this->response));die();
            }
        } else {
            $page_name                              =   isset($this->data_arr['page_name'])?$this->data_arr['page_name']:'home';

            $this->data_arr['seo_contents']  =   $this->loadHeaderContents($page_name,$this->meta_data);

            $this->template->user_template( $this->data_arr ); 
        }
    }
}
