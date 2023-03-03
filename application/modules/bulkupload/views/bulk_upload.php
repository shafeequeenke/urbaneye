<style type="text/css">
	.nav-tabs > li {
		float: left !important;
	}
	/* The container */
.container-checkbox {
    display: block;
    position: relative;
    padding-left: 35px;
    margin-bottom: 12px;
    cursor: pointer;
    font-size: 22px;
    float: left;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

/* Hide the browser's default checkbox */
.container-checkbox input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
}

/* Create a custom checkbox */
.container-checkbox .checkmark {
    position: absolute;
    top: 8px;
    left: 0;
    height: 20px;
    width: 20px;
    background: #FFFFFF 0% 0% no-repeat padding-box;
	border: 3px solid #707070;
	opacity: 1;
}

/* On mouse-over, add a grey background color */
.container-checkbox:hover input ~ .checkmark {
    background-color: #ccc;
}

/* When the checkbox is checked, add a blue background */
.container-checkbox input:checked ~ .checkmark {
    background-color: #2196F3;
}

/* Create the checkmark/indicator (hidden when not checked) */
.container-checkbox .checkmark:after {
    content: "";
    position: absolute;
    display: none;
}

/* Show the checkmark when checked */
.container-checkbox input:checked ~ .checkmark:after {
    display: block;
}

/* Style the checkmark/indicator */
.container-checkbox .checkmark:after {
    left: 5px;
    top: 0px;
    width: 5px;
    height: 10px;
    border: solid white;
    border-width: 0 3px 3px 0;
    -webkit-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    transform: rotate(45deg);
}

</style>

<!--bulk Questions start-->
<div class="ed_single_wrapper">
	<div class="container">
		<div class="row">
			<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
			</div>
			<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
				<div class="ed_sidebar_wrapper" id="upload-box">
					<div class="ed_tabs_left">
						<div class="upload-box file-input">
							<img src="<?=base_url('assets/images/cloud.svg')?>" class="upload-image">
							<p style="font-size:14px;float:inherit;">Allowed extensions docx</p>
							<div class="upload-question-console" id="upload_question_console">
								<div class="file-drop-area">
							  		<span class="file-msg">drag and drop files here<br><br>or</span><br><br>
							  		<button type="button" name="sub" id="browse_file" class="browse-btn">Browse Files</button>
								</div>
							</div>
						</div>	
					</div>
				</div>
				<div class="ed_sidebar_wrapper" style="display: none;" id="uploaded-box">
					<div class="ed_tabs_left">
						<form enctype="multipart/form-data" method="post" action="<?=base_url('upload-questions')?>">
							<div class="upload-box file-input">
								<img src="<?=base_url('assets/images/docx.svg')?>" class="docx-image">
								<p class="docx-text">pg1.docx</p>
								<p class="docx-size">2.8 MB</p>
								<div class="upload-question-console" id="upload_question_console">
									<div class="file-drop-area">	
							  			<input type="file" id="question_file" name="file" class="file-input" style="display: none;">
									</div>
								</div>
								<div class="btn-box">
									<button type="button" name="sub" value="createQuestion" id="delete_file" class=" delete-btn">Delete </button>
									<button type="submit" name="sub" value="createQuestion" id="upload_file" class=" submit-btn">Upload </button>
								</div>
							</div>
						</form>		
					</div>
				</div>
			</div>
<?php
if(!empty($load_question))
{
?>

			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ed_bottompadder20">
				<strong class="questions-head">Preview Questions</strong>
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="ed_sidebar_wrapper">
					<div class="ed_tabs_left">
						<ul class="nav nav-tabs" style="border:none;">
							<li class="">
								<div class="add-form">
									<select data-placeholder="Change Course" name="course_id" id="course_id" class="select course-name">
										<option value="">-Select a Course-</option>
<?php foreach ($active_course as $key => $course) { 
	$selected 	=	(isset($course_id)&&$course_id != ""&&$course['id']==$course_id?"selected":'');
?>
										<option <?=$selected?> value="<?=$course['id']?>"><?=$course['title']?></option>
<?php } ?>
									</select>
								</div>
							</li>
						  	<li class="" style="float: right !important;">
								<a title="Approve Questions" href="#0" id="approve_question" class="approve-btn">Approve</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
<?php
}
?>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
<?php
	foreach ($load_question as $key => $question) {
?>
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<div class="row">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 q-list-container" style="padding: 2px;">	
										<!-- question text -->
										<div class="row q-list-right question-container1">
											<div class="q-list-text">
												<label class="container-checkbox">
  													<input type="checkbox" value="<?=$question['id']?>" name="questions" class="question_check">
  													<input type="hidden" value="<?=$question['text']?>" name="text" class="text">
  													<input type="hidden" value="<?=$question['userId']?>" name="userId" class="userId">
<?php
if(!empty($question['options'])){
		foreach ($question['options'] as $key => $option) {
?>	
 													<input type="hidden" value="<?=$option?>" name="options" class="options">
												
<?php
		}
	}
?>
  													<span class="checkmark"></span>
												</label>
												<a href="#0" style="margin-left:15px;"><?=$question['text']?></a> 
												<span style="float: right;font-size: 22px;">
													<h5 style="float:right;padding: 0px 10px;">Date: <?=$question['date']?></h5>
												</span>
												<div class="q-list-text">
<?php
if(!empty($question['options'])){
		foreach ($question['options'] as $key => $option) {
?>	
													<span class="option">
														<label><?=$key+1?>.&nbsp;&nbsp;<span class="option-text"><?=$option?></span></label>
													</span>
<?php
		}
	}
?>
												</div>
											</div>
										</div>
									</div>
								</div>	
								<hr>
							</div>

<?php
	}
?>							
					
			</div>
		</div>
	</div>
<!--Bulk Questions end-->
