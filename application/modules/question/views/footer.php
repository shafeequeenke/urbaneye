<script type="text/javascript">
/**
login required popup
**/
function loginRequiredPopup(is_user_logged) {
	if(is_user_logged != "logged") {
		$("#modal_force_signin").modal();
		return;
	}
}

$(document).ready(function() {
	// document ready  
	var question_editor 	=	"<?php echo isset($question_editor)?$question_editor:'no'?>";

	if(question_editor != "no") {
		setTimeout(function(){
			loginRequiredPopup(is_user_logged);
		}, 1500);
	}		
	//mark upvote
	$(document).on('click','.question-vote-up',function() {
		loginRequiredPopup(is_user_logged);
		var questionId 		=	$(this).attr('question-id');
		var elem 			=	$(this);
		var link_url			=	base_url+"question";
		var post_data 			=	[{name:'AJAX',value:'AJAX'},
		{name:'TYPE',value:'ACTION'},
		{name:'ACTION',value:'QUESTION_UPVOTE'},
		{name:'QUESTION_ID',value:questionId}
		];
		$.ajax({
			url:link_url,
			dataType	: "json",
			type: "POST",
			data: post_data,
			success: function(response) {
				if(response.STATUS 	== 200) {
					var countLabel = $('[vote-count="'+questionId+'"]');
  					var vote  =   countLabel.text();  
  					countLabel.text(parseInt(vote) + 1);
  					elem.removeClass('question-vote-up');
  					elem.attr('title','Already Upvoted');
				} else if(response.STATUS 	== 400) {
					console.log(response.MESSAGE);
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
  
  	//mark down vote
	$(document).on('click','.question-vote-down',function() {
		loginRequiredPopup(is_user_logged);
		var questionId 		=	$(this).attr('question-id');
		var elem 			=	$(this);
		var link_url		=	base_url+"question";
		var post_data		=	[{name: 'AJAX',value:'AJAX'},
		{name:'TYPE',value:'ACTION'},
		{name:'ACTION',value:'QUESTION_DOWNVOTE'},
		{name:'QUESTION_ID',value:questionId}
		];

		$.ajax({
	      url:link_url,
	      dataType	:"json",
	      type: "POST",
	      data: post_data,
	      success: function(response) {
				if(response.STATUS 	== 200) {
					var countLabel = $('[vote-count="'+questionId+'"]');
  					var vote  =   countLabel.text();  
  					countLabel.text(parseInt(vote) - 1);
  					elem.removeClass('question-vote-down');
  					elem.attr('title','Already voted');
				} else if(response.STATUS 	== 400) {
					console.log(response.MESSAGE);
				} else {
					console.log(response.MESSAGE);
				}
			},
			error: function() {
				
			},
			beforeSend: function() {
	    		$(document.body).css({'cursor' : 'wait'});
		    }, 
		    complete: function() {
		    	$(document.body).css({'cursor' : 'default'});
		    }
	 	});
 	});

 	$(document).on('click','.question-favourite',function() {
 		loginRequiredPopup(is_user_logged);
 		var questionId		=	$(this).attr('question-id');
 		var elem 			=	$(this);
 		var link_url		=	base_url+"question";
 		var post_data		=	[{name: 'AJAX',value: 'AJAX'},
 		{name: 'TYPE',value: 'ACTION'},
 		{name: 'ACTION',value: 'QUESTION_FAVORITE'},
 		{name: 'QUESTION_ID',value: questionId}

 		];

 		$.ajax({
 			url: link_url,
 			dataType : "json",
 			type : "POST",
 			data : post_data,
 			success: function(response) {
 				if(response.STATUS == 200) {
 					elem.addClass('fa-bookmark');
 					elem.removeClass('fa-bookmark-o');
 				}else if (response.STATUS == 400) {

 				}else {
 					
 				}
 			},
 			error: function() {
 				
 			},
			beforeSend: function() {
	    		$(document.body).css({'cursor' : 'wait'});
		    }, 
		    complete: function() {
		    	$(document.body).css({'cursor' : 'default'});
		    }

 		});
 	});

 	//VOTE ANSWER
 	$(document).on("click",".ans-vote",function() {
 		loginRequiredPopup(is_user_logged);
 		var elem 		=	$(this);
 		var vote_type 	=	elem.attr("vote-type");
 		var ans_id 		=	elem.attr("ans-id");
 		var link_url		=	base_url+"question";
 		var post_data		=	[{name: 'AJAX',value: 'AJAX'},
	 		{name: 'TYPE',value: 'ACTION'},
	 		{name: 'ACTION',value: 'ANSWER_VOTE'},
	 		{name: 'ANSWER_ID',value: ans_id},
	 		{name: 'VOTE_TYPE',value:vote_type}
 		];

 		$.ajax({
 			url: link_url,
 			dataType : "json",
 			type : "POST",
 			data : post_data,
 			success: function(response) {
 				if(response.STATUS == 200) {
 					location.reload();
 				}else if (response.STATUS == 400) {

 				}else {
 					
 				}
 			},
 			error: function() {
 				
 			},
			beforeSend: function() {
	    		$(document.body).css({'cursor' : 'wait'});
		    }, 
		    complete: function() {
		    	$(document.body).css({'cursor' : 'default'});
		    }

 		});
 	})

 	//delete answer
	$(document).on("click",".trash-ans",function() {
		var position  	= 	$( this ).offset();
		var del_top 	=	parseInt(position.top)-105;
		var del_left 	=	parseInt(position.left)-120;

		var elem 		=	$(this);
		var questionId 	=	elem.attr('question-id');
		var answerId 	=	elem.attr('ans-id');
		$(".delete-popup").show();
		$(".del-que-confirm").attr("del-item","answer");
		$(".del-que-confirm").attr("question-id",questionId);
		$(".del-que-confirm").attr("ans-id",answerId);
		// deleteBox confirm
		$(".delete-popup").css("top",del_top);
		$(".delete-popup").css("left",del_left);
		return;
	});

 	//delete question
	$(document).on("click",".trash-ques",function() {
		var position  	= 	$( this ).offset();
		var del_top 	=	parseInt(position.top)-105;
		var del_left 	=	parseInt(position.left)-120;

		var elem 		=	$(this);
		var questionId 	=	elem.attr('question-id');
		var courseId 	=	elem.attr('course-id');
		$(".delete-popup").show();
		$(".del-que-confirm").attr("del-item","question");
		$(".del-que-confirm").attr("question-id",questionId);
		$(".del-que-confirm").attr("course-id",courseId);
		// deleteBox confirm
		$(".delete-popup").css("top",del_top);
		$(".delete-popup").css("left",del_left);
		return;
	});

	// $(document).on("click","body",function() {
	// 	$(".delete-popup").hide();
	// });

	$(document).on("click",".trash-elem",function() {
		var elem 		=	$(this);
		var delItem 	=	elem.attr('del-item');
		if(delItem == "question") {
			var title 		=	"Are you sure?";
			var text 		=	'Will not able to recover question!';
			var questionId 	=	elem.attr('question-id');
			var courseId 	=	elem.attr('course-id');
		} else {
			var title 		=	"Are you sure?";
			var text 		=	'Will not able to undo this operation!';
			var questionId 	=	elem.attr('question-id');
			var answerId 	=	elem.attr('ans-id');
		}

		swal({
			title: title,
			text: text,
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: '#DD6B55',
			confirmButtonText: 'Yes, delete it!',
			closeOnConfirm: false,
			//closeOnCancel: false
		},
		function(){
			if(delItem == "question") {
				var link_url		=	base_url+"question";
				var post_data		=	[
		 			{name: 'AJAX',value: 'AJAX'},
			 		{name: 'TYPE',value: 'ACTION'},
			 		{name: 'ACTION',value: 'DELETE_QUESTION'},
			 		{name: 'QUESTION_ID',value: questionId}
		 		];
			} else if(delItem == "answer") {
				var link_url		=	base_url+"question";
				var post_data		=	[
		 			{name: 'AJAX',value: 'AJAX'},
			 		{name: 'TYPE',value: 'ACTION'},
			 		{name: 'ACTION',value: 'DELETE_ANSWER'},
			 		{name: 'ANSWER_ID',value: answerId},
			 		{name: 'QUESTION_ID',value: questionId}
		 		];
			} else {
				return false;
			}

	 		$.ajax({
	 			url: link_url,
	 			dataType : "json",
	 			type : "POST",
	 			data : post_data,
	 			success: function(response) {
	 				if(response.STATUS == "200") {
	 					if(delItem == "question") {
	 						swal("Success", "Question has been deleted", "success");
	 						window.location.href = base_url+"course/"+courseId;
	 					} else {
	 						swal("Success", "Answer has been deleted", "success");
	 						window.location.href = base_url+"question/"+questionId;
	 					}
	 				} else if (response.STATUS == 203) {
	 					// location.reload();
	 				} else {
	 					// location.reload();
	 				}
	 			},
	 			error: function() {
	 				// location.reload();
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

 	//delete answer 
	$(document).on("click",".trash-ans",function() {
		var elem 		=	$(this);
		var answerId 	=	elem.attr('ans-id');
	});

 	$(document).on('click','.mcq-options',function() {
 		var questionId 		=	$(this).find('.mcq-opt').val();
 		var can_add_answer 	=	$(this).attr('can-add-answer');
 		if(can_add_answer == 'yes') {
 			$("[btn-question-id="+questionId+"]").show();	
 		}
 		
 	});

 	$(document).on('click','.ans-submit',function() {
 		loginRequiredPopup(is_user_logged);
 		var questionType 	=	$("#questionType").val();
 		var elem 			=	$(this);
 		var questionId		=	$(this).attr('btn-question-id');
 		var answerBy 		=	"<?=isset($user_details['display_name'])?$user_details['display_name']:'User'?>";
 		var answerByImg 	=	"<?=isset($user_details['photo_url'])?$user_details['photo_url']:base_url().'assets\images\default_user_profile.jpg'?>";
 		var mimeType 		=	elem.attr("question-mime-type");

 		if($("#ans_submitted").val() == questionId) {
 			return;
 		} else {
 			$("#ans_submitted").val(questionId);
 		}

 		var saved_mcq 		=	$('#saved-mcq-'+questionId).val();
 		if(saved_mcq != '') {
 			return false;
 		} 
 		$('#saved-mcq-'+questionId).val(questionId);
 		var reload_page =	false;
 		if(questionType == "noMcq") {
			var answerText 		=	CKEDITOR.instances['answer_text'].getData(); 
	 	} else {
	 		var optionName 		=	$("[value="+questionId+"]:checked").attr('id');
	 		var answerText 		=	$("[value="+questionId+"]:checked").attr('opt-value');
	 		if(answerText == "") {
	 			answerElem 		=	$("[value="+questionId+"]:checked").parent();
	 			$("[value="+questionId+"]:checked").remove();
	 			answerText 		=	answerElem.html();
	 			var reload_page =	true;
	 		}
	 		var answerPage 		=	$("[value="+questionId+"]:checked").attr('page');
	 	}

 		var elem 			=	$(this);
 		var link_url		=	base_url+"question";
 		var answer 			=	$(this)
 		var post_data		=	[
	 		{name: 'AJAX',value: 'AJAX'},
	 		{name: 'TYPE',value: 'ACTION'},
	 		{name: 'ACTION',value: 'ADD_ANSWER'},
	 		{name: 'QUESTION_ID',value: questionId},
	 		{name: 'answer_text',value:answerText},
	 		{name: 'mimeType',value:mimeType}
 		];

 		//check whether answering from single question page or not(only applicable for mcq wuestion)
 		if(answerPage == 'singleView') {
 			var user_prof_htm 	=	'<img title="'+answerBy+'" src="'+answerByImg+'" width="20" height="20" style="float: left; border-radius: 15px;" alt="U"/>';
	 		var user_ans_htm 	= 	'<div class="text-content"><p style="padding-left: 10px;">'+answerText+'</p></div>';

	 		var ans_htm 	=	'<li><div class="ans-item"><div class="profile-content">'+user_prof_htm+'</div>'+user_ans_htm+'</li>';
 			
 		} else {
 			var user_prof_htm 	=	'<img title="'+answerBy+'" src="'+answerByImg+'" width="20" height="20" style="float: left; border-radius: 15px;" alt="U"/>';
	 		var user_prof_htm 	= 	'<p>'+answerText+'</p>';

	 		var ans_htm 	=	'<li><div class="ans-item">'+user_prof_htm+'</div></li>';	
 		}
 		
 		var mcq_ans_len	=	$('#ans_ul-'+questionId).length;

 		$.ajax({
 			url: link_url,
 			dataType : "json",
 			type : "POST",
 			data : post_data,
 			success: function(response) { 
 				if(response.STATUS == "200") { 
 					$("[btn-question-id="+questionId+"]").hide();
 					if(mcq_ans_len == 0) {
			 			location.reload(); 
			 		} else {
			 			if(answerPage == 'singleView') {
			 				location.reload(); 
			 			} else {
			 				if(reload_page == true) {
			 					location.reload();
			 				}
			 				$('#ans_ul-'+questionId).append(ans_htm);
			 			}
			 		}
			 		elem.removeClass('mcq-ans-submit');
			 		elem.attr('title','Already added answer');
 				} else if (response.STATUS == 400) {

 				} else {
 					
 				}
 				location.reload(); 
 			},
 			error: function() {
 				location.reload();
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
<!-- Warning modal -->
<!-- <div class="delete-popup" style="">
	<span class="deleteBox"><p>Are you sure you want to delete?</p><span class="cancel cancel-que">Cancel</span><span class="confirm del-que-confirm">Yes</span></span>
</div> -->
<!-- /warning modal -->