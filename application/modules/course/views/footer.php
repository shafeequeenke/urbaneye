
<script type="text/javascript">
jQuery(document).ready(function($) {
	//load course content
	var post_data 		=	[];
	var course_id 		=	'<?=$course_id ?>'; 
	var page_active 	=	'<?=$page_number ?>';
	var course_slug 	=	'<?=$course_slug ?>';
	var load_question 	= 	'<?=$load_question?>';
	if(load_question == "user") {
		var ajax_action 	=	"LIST_USER_QUESTION";
		var load_content 	=	"my_question";
	} else if(load_question == "public") {
		var ajax_action 	=	"LIST_COURSE_QUESTION";
		var load_content 	=	"course_question";
	} else {
		var ajax_action 	=	"LIST_COURSE_QUESTION";
		var load_content 	=	"course_question";
	}
	post_data.push(
	  			{name:'AJAX',value:'AJAX'},
	  			{name:'TYPE',value:'HTM'},
	  			{name:'ACTION',value:ajax_action},
	  			{name:'LOAD_PAGE',value:'course_question'},
	  			{name:'COURSE_ID',value:course_id},
	  			{name:'page',value:page_active},
	  			{name:'course_slug',value:course_slug},
	  			{name:'load_question',value:load_question},
			);
	
	loadcontent('question',load_content,post_data);

	$(document).on('click','.change_page',function() {
			var post_data 		=	[];
			var course_id 		=	$(this).attr('course-id');
			post_data.push( 
			  			{name:'AJAX',value:'AJAX'},
			  			{name:'TYPE',value:'HTM'},
			  			{name:'ACTION',value:ajax_action},
			  			{name:'LOAD_PAGE',value:'course_question'},
			  			{name:'COURSE_ID',value:course_id}
					);
			var page 	=	$(this).attr('page');
			post_data.push({name:'page',value:page});

			$.ajax({
		      	url         : base_url+'question',
		      	type        : "POST",
		      	dataType    : "html",
		      	data        : post_data,
		      	success     : function(response) {
		        	$("#"+load_content).html(response);
		        	MathJax.typeset();
		      	},
		      	error       : function(error_param,error_status) {
		      	},
		      	beforeSend: function () {
		          	$(document.body).css({'cursor' : 'wait'});
		      	}, complete: function () {
		          	$(document.body).css({'cursor' : 'default'});
		      	}
		    });
		});
});

</script>