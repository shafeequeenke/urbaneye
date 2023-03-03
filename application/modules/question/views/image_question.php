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
				<!-- <p>Add normal text question</p> -->
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
							<label>Attachment:*</label>
							<p style="font-size:14px;float:inherit;">Allowed extensions jpeg, jpg, png</p>
							<div class="upload-question-console" id="upload_question_console">
							<div class="file-drop-area">
							  	<span class="fake-btn">Choose files</span>
							  	<span class="file-msg">or drag and drop files here</span>
							  	<input type="file" id="question_image" name="question_image" class="file-input">
							</div>
						</div>
						</div>
						<!-- <div class="add-form">
							<div class="switch_box">
								<input type="checkbox" id="question_is_mcq" name="question_is_mcq" class="switch_1 is-multiple">
					      		<label>Multiple Choice Question</label>
							</div>
						</div> -->
						<div class="add-form multiple-choice" style="display:none">
							<div class="mcq-opt">
								<label>Add Options</label>
								<div class="radio radio-opt">
									<label>
										<input type="text" name="question_option1" class="form-control ans-opt-ck" placeholder="Option 1">
									</label>
								</div>
								<div class="radio radio-opt">
									<label>
										<input type="text" name="question_option2" class="form-control ans-opt-ck" placeholder="Option 2">
									</label>
								</div>
								<div class="radio radio-opt">
									<label>
										<input type="text" name="question_option3" class="form-control ans-opt-ck" placeholder="Option 3">
									</label>
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
							<input type="hidden" name="forceMcqSubmit" id="forceMcqSubmit" value="">
							<input type="hidden" name="submit_question" value="imageSubmit">
							<button type="submit" name="submit_form" value="createQuestion" class="btn btn-primary submitBtn">Submit <i class="fa fa-arrow-right"></i></button>

							<button type="button" name="reset-form" class="btn btn-primary reset-form">Cancel</button>
						</div>
					<?=form_close()?>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<div class="row add-question-console" id="add_question_console">
						</div>
						
					</div>
				</div>
			</div>
		</div>
    </div><!-- /.container -->
</div>