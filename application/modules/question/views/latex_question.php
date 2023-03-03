<?php 
$createQuestionForm = [  
    'class'     =>  '',
    'id'        =>  'create_question',
    'name'      =>  'create_question',
    'enctype'   =>  "multipart/form-data"
	];
$exam_tags 		=	array('JEE','NEET','CUCET','Chemistry','Physics','Maths','Biology');
?>
<div class="ed_transprentbg">
	<div class="container">
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="ed_heading_top" style="text-align: left;">
				</div>
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<!-- <p>latex code as question</p> -->
				<div class="row add-question-wrapper">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
					<?=form_open_multipart('question/create',$createQuestionForm)?>
						<div class="add-form">
							<label>Course:*</label>
							<select data-placeholder="Change Course" name="course_id" id="course_id" class="select course-name">
								<?php foreach ($active_course as $key => $course) { 
									$selected 	=	(isset($course_id)&&$course_id != ""&&$course['id']==$course_id?"selected":'');
									?>
									<option <?=$selected?> value="<?=$course['id']?>"><?=$course['title']?></option>
								<?php } ?>
							</select>
						</div>
						<div class="add-form">
							<label>Question:*</label>
							<textarea name="question_text" id="question_text" class="latex-question form-control"><?=isset($latex_question_text)?$latex_question_text."":''?></textarea>
						</div>
						<div class="add-form">
							<div class="switch_box">
								<input type="checkbox" id="question_is_mcq" name="question_is_mcq" class="switch_1 is-multiple">
					      		<label>Multiple Choice Question</label>
							</div>
						</div>
						<div class="add-form multiple-choice" style="display:none">
							<div class="mcq-opt">
								<label>Add Options</label>
								<div class="radio radio-opt">
									<label>
										<!-- <input type="radio" name="question_ans_option" class="control-success" checked="checked"> -->
										<input type="text" name="question_option1" class="form-control ans-opt-ck" placeholder="Option 1">
										<i class="fa fa-pencil enable-ckedit-opt" aria-hidden="true" elem-name="question_option1" title="Advanced editor"></i>
									</label>
									<label class="latex_option1"></label>
								</div>
								<div class="radio radio-opt">
									<label>
										<!-- <input type="radio" name="question_ans_option" class="control-success"> -->
										<input type="text" name="question_option2" class="form-control ans-opt-ck" placeholder="Option 2">
										<i class="fa fa-pencil enable-ckedit-opt" aria-hidden="true" elem-name="question_option2" title="Advanced editor"></i>
									</label>
									<label class="latex_option2"></label>
								</div>
								<div class="radio radio-opt">
									<label>
										<!-- <input type="radio" name="question_ans_option" class="control-success"> -->
										<input type="text" name="question_option3" class="form-control ans-opt-ck" placeholder="Option 3">
										<i class="fa fa-pencil enable-ckedit-opt" aria-hidden="true" elem-name="question_option3" title="Advanced editor"></i>
									</label>
									<label class="latex_option3"></label>
								</div>
							</div>
							<div class="text-center">
								<button title="Add more option" type="button" class="btn btn-primary add-opt">Add <i class="fa fa-plus"></i></button>
							</div>
						</div>
						<div class="add-form">
							<label>Tags:</label>
							<select multiple="multiple" id="question_tag" name="question_tag[]" data-placeholder="Enter tags" class="select-icons">
								<?php

							  	if(isset($course_tags) && count($course_tags)>0) { ?>
							  		<optgroup label="Exams">
									<?php foreach ($course_tags as $key => $tag) {
									 ?>
										<option value="<?=$tag?>" data-icon="wordpress2"><?=$tag?></option>
									<?php } ?>
								</optgroup>
								<?php }?>
								<optgroup label="Exams">
									<?php foreach ($exam_tags as $key => $exam) { ?>
									<option value="<?=$exam?>" data-icon="wordpress2"><?=$exam?></option>
								<?php } ?>
								</optgroup>
							</select>
						</div>
						<div class="text-right add-form">
							<span class="warning-msg" id="form_submit_msg"></span>
							<span class="success-msg" id="form_success_msg"></span>
							<input type="hidden" id="questionType" value="application/x-latex">
							<input type="hidden" name="forceMcqSubmit" id="forceMcqSubmit" value="">
							<input type="hidden" name="submit_question" value="createQuestion">
							<button type="submit" name="submit_form" value="createQuestion" class="btn btn-primary submitBtn">Submit <i class="fa fa-arrow-right"></i></button>

							<button type="button" name="reset-form" class="btn btn-primary reset-form">Cancel</button>
						</div>
					<?=form_close()?>
					</div>
					<input type="hidden" id="pasted_value" name="pasted_value">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<div class="row add-question-console" id="add_question_console">
						</div>
						<!-- <div class="upload-question-console" id="upload_question_console">
							<div class="file-drop-area">
							  	<span class="fake-btn">Choose files</span>
							  	<span class="file-msg">or drag and drop files here</span>
							  	<input type="file" id="question_image" name="question_image" class="file-input">
							</div>
						</div> -->
					</div>
				</div>
				<div class="row">
					<div class="col-md-6" id="equation">
      				</div>
		            <div class="col-md-6" style="display: none;">
		              <h3>Sample Mathjax parsed equation</h3>
		            <p>
		            In equation \eqref{eq:sample}, we find the value of an
		            interesting integral:
		            \begin{equation}
		            \ce{CO2 + C -> 2 CO}
		            \end{equation}

		            \begin{equation}
		              \int_0^\infty \frac{x^3}{e^x-1}\,dx = \frac{\pi^4}{15}
		              \label{eq:sample}
		            \end{equation}

		            \begin{equation}
		                    \ x = {-b \pm \sqrt{b^2-4ac} \over 2a}
		            \end{equation}
		            \begin{equation}
		                    \ y = {a \pm \sqrt{a^3+b^2} \over 2b}
		            \end{equation}
		            </p>

		            </div>
		    	</div>
			</div>
		</div>
    </div><!-- /.container -->
</div>

