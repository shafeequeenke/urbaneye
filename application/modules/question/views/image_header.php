<link href="https://cdn.rawgit.com/t4t5/sweetalert/v0.2.0/lib/sweet-alert.css" rel="stylesheet" type="text/css">
<script src="https://cdn.rawgit.com/t4t5/sweetalert/v0.2.0/lib/sweet-alert.min.js"></script>
<script src="https://cdn.ckeditor.com/4.15.1/standard-all/ckeditor.js"></script>
<script type="text/javascript">
$(document).ready(function() {
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
				location.reload('<?=base_url()?>');
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
		loginRequiredPopup(is_user_logged);
		var questionObj 	=	[];
		var course_id 		=	$(".course-name").val();
		var submit_question = $("[name='submit_question']").val();
		$("#form_success_msg").html('').hide();
		$("#form_submit_msg").html('').hide();
		$(".reset-form").hide();

		var isMcq 	=	false;

		if($("#question_is_mcq").prop("checked") == true) {
			var options 		=	[];
			var validateErr 	=	false;
			$( ".ans-opt-ck" ).each(function( index ) {
				var optElemName 	=	$(this).attr("name");
				var optElemVal 		=	$(this).val();
				if(optElemVal == "") {
					$(this).css("border-color", "red");
					validateErr 	=	true;
				} else {
					$(this).css("border-color", "#ccc");
				}
				options.push(optElemVal);
			});
			questionObj['options'] 		=	options;
			if(validateErr == true) {
				return;
			}
		}
		if(submit_question == "imageSubmit") {
			var imgElem 		=	$(".question_file");
			var imageUrl 		=	imgElem.attr("src");
			var fileName 		=	imgElem.attr("file_name");
			
			questionObj['image_url'] 	=	imageUrl;
			questionObj['file_name'] 	=	fileName;
			questionObj['course_id'] 	=	course_id;
			questionObj['question_tags']=	[];
			addImageQuestion(questionObj);
			return;
		}
	});
});

/**
* upload image question
**/
function addImageQuestion(questionObj) {
	var link_url 		=	base_url+"question";
	
	var course_id 		=	questionObj['course_id'];
	var question_image 	=	questionObj['image_url'];
	var question_tags 	=	questionObj['question_tags'];
	var file_name 		=	questionObj['file_name'];
	var options 		=	questionObj['options'];
	var post_data 		=	[];

	post_data.push(
		{name:'AJAX',value:'AJAX'},
		{name:'TYPE',value:'ACTION'},
		{name:'ACTION',value:'ADD_IMAGE_QUESTION'},
		{name:'course_id',value:course_id},
		{name:'question_image',value:question_image},
		{name:'options',value:options},
		{name:'question_tag',value:question_tags},
		{name:'question_type',value:"image"},
		{name:'image_id',value:file_name},
	);
	$.ajax({
		url: link_url,
		dataType : "json",
		type : "POST",
		data : post_data,
		success: function(response) {
		$('.submitBtn').attr('disabled',false); 
			if(response.STATUS == "200") {
	 			window.location 	=	base_url+"course/"+course_id;
	 		}
		},
		error: function(error) {
			console.log("error:"+error);
		},
		beforeSend: function() {
			$('.submitBtn').attr('disabled',true);
			$(document.body).css({'cursor' : 'wait'});
	    }, 
	    complete: function() {
	    	$(document.body).css({'cursor' : 'default'});
	    }
	});
}
</script>
<link href="<?=base_url();?>assets/ltr/css/components.css" rel="stylesheet" type="text/css">
<link href="<?=base_url();?>assets/ltr/css/colors.css" rel="stylesheet" type="text/css">
<!-- <script src="https://cdn.ckeditor.com/4.15.1/standard/ckeditor.js"></script> -->
<!-- Theme JS files -->
<script type="text/javascript" src="<?=base_url();?>assets/ltr/js/plugins/forms/selects/select2.min.js"></script>

<script type="text/javascript" src="<?=base_url();?>assets/ltr/js/pages/form_layouts.js"></script>
<script type="text/javascript" src="<?=base_url();?>assets/ltr/js/plugins/forms/styling/uniform.min.js"></script>
<script type="text/javascript" src="<?=base_url();?>assets/ltr/js/plugins/forms/styling/switchery.min.js"></script>
<script type="text/javascript" src="<?=base_url();?>assets/ltr/js/plugins/forms/styling/switch.min.js"></script>
<!-- /theme JS files -->