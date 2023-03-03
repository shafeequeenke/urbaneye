<link href="https://cdn.rawgit.com/t4t5/sweetalert/v0.2.0/lib/sweet-alert.css" rel="stylesheet" type="text/css">
<script src="https://cdn.rawgit.com/t4t5/sweetalert/v0.2.0/lib/sweet-alert.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	updateOptionToCkeditor();
	$(document).on("change",".course-name",function() {
		loginRequiredPopup(is_user_logged);
		var elem 		=	$(this);
		var link_url	=	base_url+"course";
		var course_id 	=	elem.val();
		post_data 		=	[];
		post_data.push(
			{name:'AJAX',value:'AJAX'},
			{name:'TYPE',value:'ACTION'},
			{name:'ACTION',value:'COURSE_TAG_LIST'},
			{name:'COURSE_ID',value:course_id}
		);

		$.ajax({
			url:link_url,
			dataType	: 'json',
			type: 'POST',
			data: post_data,
			success: function(response) {
				if(response.STATUS 	== "200") {
					var list 	=	response.AJAX_RES;
					if(list.tags != undefined) {
						var tag_htm 	=	'<optgroup label="Courses">';
						$.each(list.tags, function( index, value ) {
							tag_htm 		=	tag_htm+'<option value="'+value+'" data-icon="wordpress2">'+value+'</option>';
						});
						tag_htm 		=	tag_htm+'</optgroup>';
						$("#question_tag").html(tag_htm);
					}
				} else { 
					console.log('no tag available');
				}
			},
			error: function() {
				console.log("response error");
			},
			beforeSend: function() {
	    		$(document.body).css({'cursor' : 'wait'});
		    }, 
		    complete: function() {
		    	$(document.body).css({'cursor' : 'default'});
		    }
		});

	})
	// fore creating question
 	$(document).on('change','.is-multiple',function() {
 		if(this.checked) {
 			$(".multiple-choice").show();
 		} else {
 			$(".multiple-choice").hide();
 		}
 	}); 

 	$(document).on('click','.add-opt',function() {
 		var total_elem 	=	$(".radio-opt").length;
 		if(total_elem >=6) {
 			return false;
 		}
 		addOptionElement(total_elem);
 	});

 	function addOptionElement(total_elem,optionVal="") {
 		var option_htm	=	'<div class="radio radio-opt rem-'+(parseInt(total_elem)+1)+'"><label><input type="text" name="question_option'+(parseInt(total_elem)+1)+'" class="form-control ans-opt-ck" placeholder="Option '+(parseInt(total_elem)+1)+'"></label>&nbsp;&nbsp;<i title="remove" class="fa fa-trash rem-opt" col-num="'+(parseInt(total_elem)+1)+'"></i></div>';
 		$('.mcq-opt').append(option_htm);
 		var elemName 	=	'question_option'+(parseInt(total_elem)+1);
 		CKEDITOR.replace( elemName
		    , {
			toolbar :
			[
				{ name: 'insert', items: [ 'Image','HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak' ] },
				{ name: 'styles', items: [ 'Format','FontSize' ] },
				{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
			]
		});
		if(optionVal !="") {
			CKEDITOR.instances[elemName].setData(optionVal);
		}		
 	}

 	$(document).on('click','.rem-opt',function() {
 		var col_count 	=	$(this).attr("col-num");
 		$('.rem-'+col_count).remove();
 	});


 	$(document).on('click',"[name='reset-form']",function() {
 		location.reload();
 	});

 		//mark upvote
	$(document).on('submit','#update_question',function(e) {
		e.preventDefault();

		$("#form_success_msg").html('').hide();
		$("#form_submit_msg").html('').hide();
		$(".reset-form").hide();

		var course_id 		=	$(".course-name").val();
		var questionId 		=	$(this).attr('question-id');
		var elem 			=	$(this);
		var link_url		=	base_url+"question";
		var post_data 		=	$( "#update_question" ).serializeArray();
		post_data.push(
			{name:'AJAX',value:'AJAX'},
			{name:'TYPE',value:'ACTION'},
			{name:'ACTION',value:'UPDATE_QUESTION'},
			{name:'QUESTION_ID',value:questionId},
			{name:'question_type',value:"html"},
			{name:'MIME_TYPE',value:'text/html'}
		);

		var isMcq 	=	false;

		if($("#question_is_mcq").prop("checked") == true) {
			var optElemVal 		=	CKEDITOR.instances['question_option1'].getData();
			var options 		=	[];
			var validateErr 	=	false;
			$( ".ans-opt-ck" ).each(function( index ) {
				var optElemName 	=	$(this).attr("name");
				var count 			=	optElemName.replace("question_option", '');
				var optElemVal 		=	CKEDITOR.instances[optElemName].getData();
				// var optVal 			=	optElemVal.replace(/<[^>]+>/g, '');
				var optVal 			=	optElemVal.replace("<p>&nbsp;</p>", '');
				if(optVal == "") {
					$(".cke_editor_question_option"+count).css("border-color", "red");
					validateErr 	=	true;
				} else {
					$(".cke_editor_question_option"+count).css("border-color", "#ccc");
				}
				options.push(optVal);
			});
			if(validateErr == true) {
				return;
			}
			post_data.push({name:'options',value:options});
		}

		$.ajax({
			url:link_url,
			dataType: "json",
			type: "POST",
			data: post_data,
			success: function(response) {
				if(response.STATUS 	== "200") {
					$("#form_success_msg").html(response.MESSAGE).show();
					window.location.href = base_url+"question/"+questionId;
				} else if(response.STATUS 	== "203") {
					swal({
						title: "Multiple choice question?",
						text: "Look like a multiple choice question, please verify.",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: '#DD6B55',
						confirmButtonText: 'Yes, it is!',
						cancelButtonText: "No, sumbit anyway!",
						closeOnConfirm: true,
						closeOnCancel: true
					},
					function(isConfirm){
						if (isConfirm){
							var mcqQuesText 		=	response.AJAX_RES.data.question;
							var mcqOptions 			=	response.AJAX_RES.data.options;
							$("#add_question_console").html("<h3>You submitted a Multiple choice question itseems.</h3><p>Please submit again to save the question.</p>").show();

							CKEDITOR.instances["question_text"].setData("<p>"+mcqQuesText+"</p>");
							$(".is-multiple").attr("checked","checked");
							$(".multiple-choice").show();
							
							var option_elem 	=	"";
							var elem_count		=	0;
							$.each(mcqOptions, function( index, value ) {
								if(value.trim() != "") { 
									elem_count 		=	(parseInt(elem_count)+1);
									option_elem 	=	"question_option"+elem_count;
									
									if(elem_count<4 &&elem_count>0) {
										optionckElement 	=	CKEDITOR.instances[option_elem];
										optionckElement.setData("<p>"+value+"</p>");
									} else if(elem_count>0) {
										addOptionElement(elem_count,value);	
									}
								} else if(elem_count>0){
									elem_count 	=	(parseInt(elem_count)-1);
								}
							});
						} else {
							$("#forceMcqSubmit").val("force");
							$("#update_question").submit();
						}
					});
				} else if(response.STATUS 	== '400') {  
					if(response.AJAX_RES && response.AJAX_RES.subject == 'similar_question') {
						var similar_que_htm 	=	'<h3>Similar Questions</h3><ul>';
						$.each(response.AJAX_RES.data, function( index, value ) {
						  	similar_que_htm 	= 	similar_que_htm+"<br><li class='similar-question-list'>"+value+"</li>";
						});
						similar_que_htm 	= 	similar_que_htm+"</ul>";
						$("#add_question_console").html(similar_que_htm).show();
						$("#form_submit_msg").html(response.MESSAGE).show();
						$(".reset-form").show();
						$("[name='submit_form']").html('Submit Anyway <i class="fa fa-arrow-right"></i>');
						$("[name='submit_question']").attr('value','forceSubmit');
					} else if(response.AJAX_RES && response.AJAX_RES.subject == 'fill_fields') {
						var validation_err_htm 	=	'<h3>Please Resolve Before Submit.</h3>';

						validation_err_htm 		= 	validation_err_htm+response.AJAX_RES.data;
						$("#add_question_console").html(validation_err_htm).show();
						$("#form_submit_msg").html(response.MESSAGE).show();
						$(".reset-form").show();
					}
				} else if( response.STATUS 	== '402' ) {
					location.reload('<?=base_url()?>');
				} else { 
					console.log(response.MESSAGE);
				}
			},
			error: function() {
				console.log("response error");
			},
			beforeSend: function() {
	    		$(document.body).css({'cursor' : 'wait'});
		    }, 
		    complete: function() {
		    	$(document.body).css({'cursor' : 'default'});
		    }
		});
	});
});
</script>
<link href="<?=base_url();?>assets/ltr/css/components.css" rel="stylesheet" type="text/css">
<link href="<?=base_url();?>assets/ltr/css/colors.css" rel="stylesheet" type="text/css">
<script src="https://cdn.ckeditor.com/4.15.1/standard/ckeditor.js"></script>
<!-- Theme JS files -->
<script type="text/javascript" src="<?=base_url();?>assets/ltr/js/plugins/forms/selects/select2.min.js"></script>

<script type="text/javascript" src="<?=base_url();?>assets/ltr/js/pages/form_layouts.js"></script>
<script type="text/javascript" src="<?=base_url();?>assets/ltr/js/plugins/forms/styling/uniform.min.js"></script>
<script type="text/javascript" src="<?=base_url();?>assets/ltr/js/plugins/forms/styling/switchery.min.js"></script>
<script type="text/javascript" src="<?=base_url();?>assets/ltr/js/plugins/forms/styling/switch.min.js"></script>
<!-- /theme JS files -->
<?php 
	$createQuestionForm = [  
	    'class'     =>  '',
	    'id'        =>  'update_question',
	    'name'      =>  'update_question',
	    'enctype'   =>  "multipart/form-data",
	    'question-id'=> 	$question['questionId']
  	];
  	$exam_tags 		=	array('JEE','NEET','CUCET','Chemistry','Physics','Maths','Biology');
  	$questionType 	=	isset($question['type'])?$question['type']:"text";
  	$questionText 	=	$questionType=="text"?$question['text']:"";
  	$questionImage 	=	$questionType!="text"?$question['imageUrl']:"";
  	$questionTags 	=	isset($question['tags'])?$question['tags']:[];
  	$mcq 			=	isset($question['isChoiceQuestion'])?$question['isChoiceQuestion']:false;
  	if($mcq) {
  		$options 	=	isset($question['options'])&&is_array($question['options'])?$question['options']:[];
  	}

?>
<div class="ed_transprentbg">
	<div class="container">
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="ed_heading_top">
				</div>
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="row add-question-wrapper">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
					<?=form_open_multipart('question/update',$createQuestionForm)?>
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
							<textarea class="form-control question-text-area" name="question_text"><?=(isset($complex_question_text)?$complex_question_text:$questionText)?></textarea>
						</div>
						<div class="add-form">
							<div class="switch_box">
								<input <?=$mcq?'checked="true"':''?> type="checkbox" id="question_is_mcq" name="question_is_mcq" class="switch_1 is-multiple">
					      		<label>Multiple Choice Question</label>
							</div>
						</div>
						<div class="add-form multiple-choice" style="<?=$mcq?'display: block':'display: none'?>">
							<div class="mcq-opt">
								<label>Add Options</label>
								<?php 
								if($mcq) {
									$htmlContent 			=	(isset($question_details['htmlContent'])?$question_details['htmlContent']:false);
									foreach ($options as $key => $value) {
										$key++;
										?>
										<div class="radio radio-opt">
											<label>
												<!-- <input type="radio" name="question_ans_option" class="control-success"> -->
												<input type="text" name="question_option<?=$key?>" class="form-control ans-opt-ck" placeholder="Option <?=$key?>" value="<?=$value?>">
												<i class="fa fa-pencil enable-ckedit-opt" aria-hidden="true" elem-name="question_option<?=$key?>" title="Advanced editor"></i>
											</label>
										</div>
									<?php
									}
								} else {
								 ?>
								<div class="radio radio-opt">
									<label>
										<!-- <input type="radio" name="question_ans_option" class="control-success" checked="checked"> -->
										<input type="text" name="question_option1" class="form-control ans-opt-ck" placeholder="Option 1">
										<i class="fa fa-pencil enable-ckedit-opt" aria-hidden="true" elem-name="question_option1" title="Advanced editor"></i>
									</label>
								</div>
								<div class="radio radio-opt">
									<label>
										<!-- <input type="radio" name="question_ans_option" class="control-success"> -->
										<input type="text" name="question_option2" class="form-control ans-opt-ck" placeholder="Option 2">
										<i class="fa fa-pencil enable-ckedit-opt" aria-hidden="true" elem-name="question_option2" title="Advanced editor"></i>
									</label>
								</div>
								<div class="radio radio-opt">
									<label>
										<!-- <input type="radio" name="question_ans_option" class="control-success"> -->
										<input type="text" name="question_option3" class="form-control ans-opt-ck" placeholder="Option 3">
										<i class="fa fa-pencil enable-ckedit-opt" aria-hidden="true" elem-name="question_option3" title="Advanced editor"></i>
									</label>
								</div>
								<?php } ?>
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
										$selectedTagHtm =	'';
										if(isset($questionTags) && in_array($tag, $questionTags)) {
											$selectedTagHtm 	=	'selected="selected"';
										}
									 ?>
										<option <?=$selectedTagHtm?> value="<?=$tag?>" data-icon="wordpress2"><?=$tag?></option>
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
							<input type="hidden" name="submit_question" value="updateQuestion">
							<button type="submit" name="submit_form" value="updateQuestion" class="btn btn-primary">Update <i class="fa fa-arrow-right"></i></button>

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
<script type="text/javascript">
	(function() {
	      var mathElements = [
        'math','maction','maligngroup','malignmark','menclose','merror',
        'mfenced','mfrac','mglyph','mi','mlabeledtr','mlongdiv','mmultiscripts',
        'mn','mo','mover','mpadded','mphantom','mroot','mrow','ms','mscarries',
        'mscarry','msgroup','msline','mspace','msqrt','msrow','mstack','mstyle',
        'msub','msup','msubsup','mtable','mtd','mtext','mtr','munder','munderover','semantics','annotation','annotation-xml'
      ];

      CKEDITOR.plugins.addExternal('ckeditor_wiris', 'https://ckeditor.com/docs/ckeditor4/4.15.1/examples/assets/plugins/ckeditor_wiris/', 'plugin.js');

      CKEDITOR.replace('question_text', {
        extraPlugins: 'ckeditor_wiris',
        // For now, MathType is incompatible with CKEditor file upload plugins.
        removePlugins: 'uploadimage,uploadwidget,filetools,filebrowser',
        height: 320,
        // Update the ACF configuration with MathML syntax.
        extraAllowedContent: mathElements.join(' ') + '(*)[*]{*};img[data-mathml,data-custom-editor,role](Wirisformula)'
      });
    }());

	$(document).ready(function() {
		if($("#question_is_mcq").prop("checked") == true) {
			updateOptionToCkeditor();
		}
	});

	//enable ckeditor for the options
 	$(document).on('click','.enable-ckedit-opt',function() {
 		updateOptionToCkeditor();
 	});

    var e = CKEDITOR.instances['question_text']
	e.on( 'change', function( event ) {
		loginRequiredPopup(is_user_logged);
		var submit_status 	=	$("[name='submit_question']").val();
		if( submit_status == 'forceSubmit') {
			$("[name='submit_form']").html('Submit <i class="fa fa-arrow-right"></i>');
			$("[name='submit_question']").attr('value','updateQuestion');
		}	    
	});

	function updateOptionToCkeditor() {
		//update all option values to ckeditor to avoid conflict
 		$( ".ans-opt-ck" ).each(function( index ) {
			var elemName 	=	$(this).attr('name');
			var elemVal 	=	$(this).val();
			$('[elem-name="'+elemName+'"]').hide();
	 		CKEDITOR.replace( elemName
			    , {
					toolbar :
					[
						{ name: 'insert', items: [ 'Image','HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak' ] },
						{ name: 'styles', items: [ 'Format','FontSize' ] },
						{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
					]
				});
			CKEDITOR.instances[elemName].setData('<p>'+elemVal+'</p>');
		 	
		});
	}

</script>