<link href="https://cdn.rawgit.com/t4t5/sweetalert/v0.2.0/lib/sweet-alert.css" rel="stylesheet" type="text/css">
<script src="https://cdn.rawgit.com/t4t5/sweetalert/v0.2.0/lib/sweet-alert.min.js"></script>
<script src="https://cdn.ckeditor.com/4.15.1/standard-all/ckeditor.js"></script>
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
	$(document).on('submit','#create_question',function(e) {
		e.preventDefault();
		var course_id 		=	$(".course-name").val();
		var submit_question = $("[name='submit_question']").val();
		if(submit_question == "imageSubmit") {
			var imgElem 		=	$(".question_file");
			var imageUrl 		=	imgElem.attr("src");
			var fileName 		=	imgElem.attr("file_name");
			var questionObj 	=	[];
			questionObj['image_url'] 	=	imageUrl;
			questionObj['file_name'] 	=	fileName;
			questionObj['course_id'] 	=	course_id;
			questionObj['question_tags']=	[];
			addImageQuestion(questionObj);
			return;
		}
		$("#form_success_msg").html('').hide();
		$("#form_submit_msg").html('').hide();
		$(".reset-form").hide();

		var questionId 		=	$(this).attr('question-id');
		var elem 			=	$(this);
		var link_url		=	base_url+"question";
		var post_data 		=	$( "#create_question" ).serializeArray();
		post_data.push(
			{name:'AJAX',value:'AJAX'},
			{name:'TYPE',value:'ACTION'},
			{name:'COURSE_ID',value:'course_id'},
			{name:'ACTION',value:'ADD_QUESTION'},
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
				$('.submitBtn').attr('disabled',false);
				if(response.STATUS 	== "200") {
					$("#form_success_msg").html(response.MESSAGE).show();
					window.location.href = base_url+"course/"+course_id;
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
							$("#create_question").submit();
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
				$('.submitBtn').attr('disabled',true);
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