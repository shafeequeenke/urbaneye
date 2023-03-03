<link href="https://cdn.rawgit.com/t4t5/sweetalert/v0.2.0/lib/sweet-alert.css" rel="stylesheet" type="text/css">
<script src="https://cdn.rawgit.com/t4t5/sweetalert/v0.2.0/lib/sweet-alert.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	updateOptionToCkeditor();
	$(document).on("change",".course-name",function() {
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
 		if(optionVal != "") {
 			$('[name="'+elemName+'"]').val(optionVal);
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
	$(document).on('submit','#update_image_question',function(e) {
		e.preventDefault();

		$("#form_success_msg").html('').hide();
		$("#form_submit_msg").html('').hide();
		$(".reset-form").hide();

		var imgElem 		=	$(".question_file");
		var course_id 		=	$(".course-name").val();
		var question_image 	=	imgElem.attr("src");
		var file_name 		=	imgElem.attr("file_name");
		var questionId 		=	$(this).attr('question-id');
		var elem 			=	$(this);
		var link_url		=	base_url+"question";
		var question_tags 	=	[];
		var post_data 		=	$( "#update_image_question" ).serializeArray();
		post_data.push(
			{name:'AJAX',value:'AJAX'},
			{name:'TYPE',value:'ACTION'},
			{name:'ACTION',value:'UPDATE_IMAGE_QUESTION'},
			{name:'QUESTION_ID',value:questionId},
			{name:'MIME_TYPE',value:'text/plain'},
			{name:'course_id',value:course_id},
			{name:'question_image',value:question_image},
			{name:'question_tag',value:question_tags},
			{name:'question_type',value:"image"},
			{name:'image_id',value:file_name},
		);

		var isMcq 	=	false;

		if($("#question_is_mcq").prop("checked") == true) {
			var options 		=	[];
			var validateErr 	=	false;
			$( ".ans-opt-ck" ).each(function( index ) {
				var optElemName 	=	$(this).attr("name");
				var optElemVal 		=	$('[name="'+optElemName+'"]').val();
				if(optElemVal == "") {
					$(this).css("border-color", "red");
					validateErr 	=	true;
				} else {
					$(this).css("border-color", "#ccc");
				}
				options.push(optElemVal);
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

							$('[name="question_text"]').val(mcqQuesText);
							$(".is-multiple").attr("checked","checked");
							$(".multiple-choice").show();
							
							var option_elem 	=	"";
							var elem_count		=	0;
							$.each(mcqOptions, function( index, value ) {
								if(value.trim() != "") { 
									elem_count 		=	(parseInt(elem_count)+1);
									option_elem 	=	"question_option"+elem_count;
									
									if(elem_count<4 &&elem_count>0) {
										optionElement 	=	$('[name="'+option_elem+']');
										optionElement.val(value);
									} else if(elem_count>0) {
										addOptionElement(elem_count,value);	
									}
								} else if(elem_count>0){
									elem_count 	=	(parseInt(elem_count)-1);
								}
							});
						} else {
							$("#forceMcqSubmit").val("force");
							$("#update_image_question").submit();
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
						$("[name='submit_form']").html('Update anyway <i class="fa fa-arrow-right"></i>');
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
	    'id'        =>  'update_image_question',
	    'name'      =>  'update_image_question',
	    'enctype'   =>  "multipart/form-data",
	    'question-id'=> 	$question['questionId']
  	];
  	$exam_tags 		=	array('JEE','NEET','CUCET','Chemistry','Physics','Maths','Biology');
  	$questionType 	=	isset($question['type'])?$question['type']:"text";
  	$questionText 	=	$questionType=="text"?$question['text']:"";
  	$questionImage 	=	$questionType!="text"?$question['imageUrl']:"";
  	$questionImageName 	=	$questionType!="text"?$question['image']:"";
  	$questionTags 	=	isset($question['tags'])?$question['tags']:[];
  	$questionId 	=	isset($question['questionId'])?$question['questionId']:"";
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
								<input <?=$mcq?'checked="true"':''?> type="checkbox" id="question_is_mcq" name="question_is_mcq" class="switch_1 is-multiple">
					      		<label>Multiple Choice Question</label>
							</div>
						</div> -->
						<div class="add-form multiple-choice" style="<?=$mcq?'display: block':'display: none'?>">
							<div class="mcq-opt">
								<label>Add Options</label>
								<?php 
								if($mcq) {
									$htmlContent 	=	(isset($question_details['htmlContent'])?$question_details['htmlContent']:false);
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
							<input type="hidden" name="question_id" id="question_id" value="<?=$questionId?>">
							<input type="hidden" name="submit_question" value="updateQuestion">
							<button type="submit" name="submit_form" value="updateQuestion" class="btn btn-primary">Update <i class="fa fa-arrow-right"></i></button>

							<button type="button" name="reset-form" class="btn btn-primary reset-form">Cancel</button>
						</div>
					<?=form_close()?>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<div class="row add-question-console" id="add_question_console">
<img class="question_file" file_name="<?=$questionImageName?>" src="<?=$questionImage?>" width="100%" height="">
						</div>
					</div>
				</div>
			</div>
		</div>
    </div><!-- /.container -->
</div>
<script type="text/javascript">

    $(document).ready(function() {
		if($("#question_is_mcq").prop("checked") == true) {
			updateOptionToCkeditor();
		}
	});

	//enable ckeditor for the options
 	$(document).on('click','.enable-ckedit-opt',function() {
 		updateOptionToCkeditor();
 	});

    
	$(document).on('change','[name="question_text"]',function( event ) {
		var submit_status 	=	$("[name='submit_question']").val();
		if( submit_status == 'forceSubmit') {
			$("[name='submit_form']").html('Update <i class="fa fa-arrow-right"></i>');
			$("[name='submit_question']").attr('value','updateQuestion');
		}	    
	});

	function updateOptionToCkeditor() {
		//update all option values to ckeditor to avoid conflict
 		$( ".ans-opt-ck" ).each(function( index ) {
			var elemName 	=	$(this).attr('name');
			var elemVal 	=	$(this).val();
			$('[elem-name="'+elemName+'"]').hide();
		});
	}



//////////////drag and drop file upload////////////////////
 $(document).ready(function() {
		var $fileInput = $('.file-input');
		var $droparea = $('.file-drop-area');

		// highlight drag area
		$fileInput.on('dragenter focus click', function() {
		  $droparea.addClass('is-active');
		});

		// back to normal state
		$fileInput.on('dragleave blur drop', function() {
		  $droparea.removeClass('is-active');
		});

		// change inner text
		$fileInput.on('change', function(e) {
		  	var filesCount = $(this)[0].files.length;
		  	var $textContainer = $(this).prev();

		  	var validFileExtensions = ['jpeg', 'jpg', 'png'];
			var extension = $(this).val().split('.').pop().toLowerCase();
			if ($.inArray(extension, validFileExtensions) >0) {

			} else {
				$textContainer.html("<span style='color:red;'>Extension ."+extension+ " not allowed.</span>");
				$("#add_question_console").html('');
				return false;
			}

		  	if (filesCount === 1) {
		    	// if single file is selected, show file name
		    	var fileName = $(this).val().split('\\').pop();
		    	$textContainer.text(fileName);
		  	} else {
		    	// otherwise show number of files
		    	return false;
		    	$textContainer.text('Single file allowed');
		  	}
		});
	});
//////////////drag and drop file upload////////////////////

  ////////change image on drop files/////////////
  $(document).on("change","#question_image",function (e) {
    e.preventDefault();
    link_url    = base_url+'user';
    var post_data     = [   
            {name:'AJAX',value:'AJAX'},
            {name:'TYPE',value:'ACTION'},
            {name:'ACTION',value:'FIREBASE_CREDS'}
          ];
    $.ajax({
        url         : link_url,
        type        : "POST",
        dataType    : "json",
        data        : post_data,
        async     : false,
        success     : function(response) {
          if( response.STATUS == 200 ) {
            uploadQuestionFile(response.fireCreds);
          }
        },
        error       : function(error_param,error_status) {

        },
        beforeSend: function() {

        }, 
        complete: function() {

        }
    });
  });



function uploadQuestionFile(fireCreds) {
	// TODO: Replace the following with your app's Firebase project configuration
	var firebaseConfig = {
	    apiKey: fireCreds.fire_apiKey,
	    authDomain: fireCreds.fire_authDomain,
	    databaseURL: fireCreds.fire_databaseURL,
	    projectId: fireCreds.fire_projectId,
	    storageBucket: fireCreds.fire_storageBucket,
	    messagingSenderId: fireCreds.fire_messagingSenderId,
	    appId: fireCreds.fire_appId
  	};

  	if (!firebase.apps.length) {
  	// Initialize Firebase
		firebase.initializeApp(firebaseConfig);
	}

	var timestamp = Number(new Date());
	var storage = firebase.storage().ref();
	var fileName =	timestamp.toString();
	var storageRef    = storage.child("questions/"+fileName);
	var file_data = $("#question_image").prop("files")[0];
	var storeRef = storageRef.put(file_data);

	storeRef.then(snapshot => snapshot.ref.getDownloadURL())
  		.then((url) => {
		parseQuestionUpload(url,fileName);
	});
}

function parseQuestionUpload(url,fileName) {
	// start ajax for parse image
   	link_url 	=	'<?=base_url()?>'+'question';
   	var post_data 		=	[		
			{name:'AJAX',value:'AJAX'},
			{name:'TYPE',value:'ACTION'},
			{name:'ACTION',value:'PARSE_QUESTION_IMAGE'},
			{name:'FILE_NAME',value:url}
		];		
	
	var question_obj 	=	"";
	var question_htm 	=	"";
	var question_tex 	=	"";
	var question_lat 	=	"";
    $.ajax({
 		url:link_url,
		dataType: "json",
		type: "POST",
		data: post_data,
		success: function(response) { 
			if(response.STATUS == "200") {
				question_obj 	=	response.AJAX_RES;
				updateQuestionField(question_obj,url,fileName);					
			}
		},
		error: function(error) {
			console.log(error);
		},
		beforeSend: function() {
    		$(document.body).css({'cursor' : 'wait'});
	    }, 
	    complete: function() {
	    	$(document.body).css({'cursor' : 'default'});
	    }
	});
}

	//update question field from image
	function updateQuestionField(questionObj,imageUrl,fileName) {
		if(questionObj.request_id != "" && questionObj.confidence_rate > 0.06) {
			var questionText 	=	"";
			if( questionObj.data.length >=1 ) {
				var data 			=	questionObj.data;
				var questionText 	=	questionObj.text;
				if(data[0].type=="latex" || data[1].type=="latex") {
					var latex_ans 	=	"";
					var ascci_ans 	=	"";
					$.each(data,function(key,val) {
						if(val.type == "latex") {
							latex_ans = latex_ans+val.value;
						} else if (val.type == "asciimath") {
							ascci_ans = ascci_ans+" "+val.value;
						}
					});
					if(latex_ans !="") {
						questionTxt 	=	latex_ans;
					} else if(ascci_ans !="") {
						questionTxt 	=	ascci_ans;
					}
					//alert for image question render
					swal({
			            title: "Seems image contains complex equation.",
			            // text: "You can use it as complex equation with question editor!",
			            text:"Content: "+questionTxt,
			            type: "warning",
			            showCancelButton: true,
			            confirmButtonColor: "#EF5350",
			            confirmButtonText: "Yes, use it!",
			            cancelButtonText: "No, Use as image!",
			            closeOnConfirm: true,
			            closeOnCancel: true
			        },
			        function(isConfirm){
			            if (isConfirm) {
			                var question_id 		=	$("#question_id").val();
							var url = base_url + "question/edit/"+question_id+"/latex";
							var form = $('<form action="' + url + '" method="post">' +
							  '<textarea id="latex_question_text" name="latex_question_text" >'+questionText+'</textarea>' +
							  '</form>');
							$('body').append(form);
							form.submit();
			            }
			            else {
                        	var image_htm 	=	'<img class="question_file" file_name="'+fileName+'" src="'+imageUrl+'" width="100%" height="">';
				        	$("#add_question_console").html(image_htm);
							$("[name='submit_form']").html('Submit as image <i class="fa fa-image"></i>');
							$("[name='submit_question']").attr('value','imageSubmit');
			            }
			        });					
				}
			} else {
				//update question text from image
				questionTxt 		=	questionObj.text;
				swal({
		            title: "Seems image contains text.",
		            text: "Content: "+questionTxt,
		            // html:"Content: "+questionTxt,
		            type: "warning",
		            showCancelButton: true,
		            confirmButtonColor: "#EF5350",
		            confirmButtonText: "Yes, use it!",
		            cancelButtonText: "No, Use as image!",
		            closeOnConfirm: true,
		            closeOnCancel: true
		        },
		        function(isConfirm){
		            if (isConfirm) {
		                var question_id 		=	$("#question_id").val();
		                questionTxt 		=	questionObj.text;
						var url = base_url + "question/edit/"+question_id+"/html";
						var form = $('<form action="' + url + '" method="post">' +
						  '<textarea id="complex_question_text" name="complex_question_text" >'+questionTxt+'</textarea>' +
						  '</form>');
						$('body').append(form);
						form.submit();
		            } else {
                    	var image_htm 	=	'<img class="question_file" file_name="'+fileName+'" src="'+imageUrl+'" width="100%" height="">';
			        	$("#add_question_console").html(image_htm);
						$("[name='submit_form']").html('Submit as image <i class="fa fa-image"></i>');
						$("[name='submit_question']").attr('value','imageSubmit');
		            }
		        });
			}
		} else if(questionObj.error_info != undefined && (questionObj.error_info.id == "image_no_content" || questionObj.error_info.id == 'sys_exception' || questionObj.error_info.id == 'image_max_size')) {
        	var image_htm 	=	'<img class="question_file" file_name="'+fileName+'" src="'+imageUrl+'" width="100%" height="">';
        	$("#add_question_console").html(image_htm);
			$("[name='submit_form']").html('Submit as image <i class="fa fa-image"></i>');
			$("[name='submit_question']").attr('value','imageSubmit');
		} else {
        	var image_htm 	=	'<img class="question_file" file_name="'+fileName+'" src="'+imageUrl+'" width="100%" height="">';
        	$("#add_question_console").html(image_htm);
			$("[name='submit_form']").html('Submit as image <i class="fa fa-image"></i>');
			$("[name='submit_question']").attr('value','imageSubmit');
		}
	}
////////////////update images ondrop image end/////////////


</script>