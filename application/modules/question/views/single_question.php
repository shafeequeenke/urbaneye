<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
<script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js"></script>
<link href="https://cdn.rawgit.com/t4t5/sweetalert/v0.2.0/lib/sweet-alert.css" rel="stylesheet" type="text/css">
<script src="https://cdn.rawgit.com/t4t5/sweetalert/v0.2.0/lib/sweet-alert.min.js"></script>
<script src="https://cdn.ckeditor.com/4.15.1/standard/ckeditor.js"></script>
<div class="ed_single_wrapper">
	<?php 
	// if( !empty($question_details) && isset($question_details['mimeType']) && $question_details['mimeType'] == 'latex') { 
		?>
<style type="text/css">
	mjx-container[jax="CHTML"][display="true"] {
	    text-align: left !important;
	}
	mjx-container[jax="SVG"][display="true"] {
		text-align: left !important;	
	}
	.radio label .MathJax {
		top: -12px;
	}
</style>
<script type="text/javascript">
	
$(document).ready(function() {
	// document ready  
	$(document).on('click','.single-ans-submit',function() {
 		loginRequiredPopup(is_user_logged);
 		var link_url		=	base_url+"question";
 		var questionId 		=	$(this).attr('btn-question-id');
 		var answerText 		=	CKEDITOR.instances['answer_text'].getData();
 		var post_data		=	[
	 		{name: 'AJAX',value: 'AJAX'},
	 		{name: 'TYPE',value: 'ACTION'},
	 		{name: 'ACTION',value: 'ADD_ANSWER'},
	 		{name: 'QUESTION_ID',value: questionId},
	 		{name: 'answer_text',value:answerText}
 		];

 		$.ajax({
 			url: link_url,
 			dataType : "json",
 			type : "POST",
 			data : post_data,
 			success: function(response) { 
 				if(response.STATUS == "200") { 
 					location.reload(); 
 				}
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
	<?php
	// }
	?>
	<div class="container">
		<div class="ed_courses">
			<div>
					<?php 
					if( !empty($question_details)) {
						
							$answerText				=	'';
							$questionId 			=	$question_details['questionId'];
							$questionType 			=	$question_details['type'];
							$mcq 					=	isset($question_details['isChoiceQuestion'])&&$question_details['isChoiceQuestion']?true:false;
							$options 				=	$mcq?(isset($question_details['options'])?$question_details['options']:''):'';

							$questionText 			=	$question_details['text'];
							$questionImage 			=	isset($question_details['imageUrl'])?$question_details['imageUrl']:'';
							$questionCreated 		=	$question_details['modified']/1000;
							$createdDate 			=	date('d-M-Y h:i a',$questionCreated);
							$createdDate 			=	time_elapsed_string($createdDate);
							$answer 				=	isset($question_details['answer'])?$question_details['answer']:[];
							$answerCount 			=	count($answer);
							if(array_key_exists($question_details['userId'], $user_list)) {
								$questionUser 			=	$user_list[$question_details['userId']];
							} else {
								$questionUser			=	[];
							}

							$upVote 				=	isset($question_details['countInfo']['upVote'])?$question_details['countInfo']['upVote']:0;
							$downVote 				=	isset($question_details['countInfo']['downVote'])?$question_details['countInfo']['downVote']:0;
							$vote 					=	$upVote-$downVote;

							$isUpvoted 				=	isset($question_details['userVote'])&&$question_details['userVote']['upVote']>=1?true:false;
							$isDownvoted 			=	isset($question_details['userVote'])&&$question_details['userVote']['downVote']>=1?true:false;
							$isFavorite 			=isset($question_details['isFavourite'])?$question_details['isFavourite']:false;

							$answeredUsers 			=	$question_details['answeredUsers'];
							$canUserAddAnswer 		=	true;
							$can_add_ans_htm 		=	'can-add-answer="yes"';

							if( isset($user_details['uid']) && in_array($user_details['uid'], $answeredUsers) ) {
								$canUserAddAnswer 	=	false;
								$can_add_ans_htm 	=	$canUserAddAnswer?'can-add-answer="no"':'title="You already added Answer"';
							}

							if($mcq) {
								$answer_page_htm 		=	'page="singleView"';
							} else {
								$answer_page_htm 		=	'page="singleView"';
							}

							$htmlContent 			=	(isset($question_details['htmlContent'])?$question_details['htmlContent']:false);
							
							$ownQuestion 				=	false;
							if(isset($user_details['uid']) && $user_details['uid'] == $question_details['userId']) {
								$ownQuestion 			=	true;
							}
					?>


					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="width: 99%;">
						<div class="row" id="courseTagList">
						<?php
						if( !empty($course_details)) {
							echo '<ul class="col-md-8 search-tags">';
							foreach ($course_details as $key => $course) {
								$course_name 		=	isset($course['department'])?$course['department']:''.isset($course['title'])?$course['title']:'';
								echo '<a target="_blank" title="Go to'.$course_name.'" href="'.base_url().'course/'.$course["id"].'"><li class="course-hash-tag" course-id="'.$course['id'].'">'.$course_name.'</li></a>';
							}
							echo '</ul>';
						} ?>
						<div class="col-md-2" style="margin: 0px;float: right;"><a href="<?=base_url().'question/create/'.$course['id'] ?>"><button class="primary" title="Create New Question" style="background: #0364b3;color: #fff;height: 40px;border-radius: 10px;">New Question <i class="fa fa-plus"></i></button></a></div>
						</div>
						<div class="row q-list-item">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 q-list-container" style="padding: 2px;">
								<!-- question user dispaly name -->
								<span class="q-list-right align-left q-list-profile" style="">
									<img src="<?=isset($questionUser['imageUrl'])?$questionUser['imageUrl']:base_url().'assets\images\default_user_profile.jpg'?>" width="25" height="25" style="float: left; border-radius: 15px;" alt="<?=isset($questionUser['username'])?substr($questionUser['username'],0,1):'U'?>"/>
									<h5 style="float: left;margin: 5px 10px;">
										<?=isset($questionUser['username'])?$questionUser['username']:'No Name'?>
									</h5>
									<span style="float: right;font-size: 22px;">
										<i style="float:right;padding: 5px 2px;" class=" <?=$isFavorite?'fa fa-bookmark':'fa fa-bookmark-o question-favourite';?>"  <?=$isFavorite?'':'title="Bookmark"';?> question-id="<?=$questionId?>" ></i>
										<h5 style="float:right;padding: 0px 10px;"><?=$createdDate?></h5>
									</span>
								</span>
								<!-- question text -->
								<div class="row q-list-right question-container">
									<?php 
										if($questionType == 'text') {
											echo '<div class="q-list-text">'?><?= $questionText?><?php '</p>' ;

											if($mcq&&is_array($options)) {
												echo "<ul>";
												foreach ($options as $optkey => $optvalue) {
													$optVal 	=	str_replace("<p>&nbsp;</p>", "", $optvalue);
													$optValue 		=	$htmlContent?"":$optVal;
													$radio_htm 	=	'<div class="radio">
															<label>
																<input id="mcq-'.$optkey.'" type="radio" name="mcq-" class="mcq-opt control-success" value="'.$questionId.'" opt-value="'.$optValue.'" '.$answer_page_htm.'/>
																'.$optVal.'
															</label>
														</div>';

													echo '<li '.$can_add_ans_htm.' class="mcq-options">'.$radio_htm.'</li>';
												}
											
												echo '<li class="option-save-btn"><i class="fa fa-save ans-submit mcq-ans-submit" btn-question-id="'.$questionId.'" title="Click to save"></i></li>';
												echo "</ul>";
											}
											echo '<input type="hidden" id="saved-mcq-'.$questionId.'" value="" />';
											echo '</div>';
										} else {
										?>
										<input type="checkbox" id="zoomQuestion_<?=$questionId?>">
										<label class="q-list-img" for="zoomQuestion_<?=$questionId?>">
										    <img src="<?=$questionImage?>" style="max-width:100%;" alt="<?=isset($question_details['text'])?$question_details['text']:'Question'?>" />
										</label>
										
									<?php } ?>
								</div>
								<!-- question actions -->
								<div class="row q-list-right q-list-tags-single" >
									<label class="q-list-left <?=$isUpvoted||$ownQuestion?'':'question-vote-up'?>" title="<?=$isUpvoted?'Already Voted':'Up Vote'?>" question-id="<?=$questionId?>">
										<i class="fa fa-thumbs-up" style="font-size:24px"></i>
									</label>
									<label class="q-list-left" vote-count="<?=$questionId?>" style="text-align: center;"><?=$vote?></label>
									<label class="q-list-left <?=$isDownvoted||$ownQuestion?'':'question-vote-down'?>" question-id="<?=$questionId?>" title="<?=$isDownvoted?'Already Voted':'Down Vote'?>">
										<i class="fa fa-thumbs-down" style="font-size:24px"></i>
									</label>
									<!-- answer count -->
									<label class="q-list-left" style="margin-left: 10px;">
										<i class="fa fa-sticky-note" style="font-size: 22px;"></i>
									</label>
									<label class="q-list-left" style="text-align: center;"><?=$answerCount?>
									</label>
									<?php if(isset($user_details['uid']) && $user_details['uid'] == $question_details['userId']) { ?>
									<a title="Edit question" href="<?=base_url()."question/edit/".$questionId?>">
										<label class="q-list-left" question-id="<?=$questionId?>">
											<i class="fa fa-pencil"></i>
										</label>
									</a>
									<?php } ?>
									<?php if(isset($user_details['uid']) && $user_details['uid'] == $question_details['userId']) { ?>
									<label class="q-list-left" question-id="<?=$questionId?>">
										<i del-item="question" course-id="<?=$course['id']?>" question-id="<?=$questionId?>" class="fa fa-trash trash-elem"></i>
									</label>
									<?php } ?>
								</div>
								<!-- available answers -->
								<div class="row q-list-right">
									<?php
									// question answer section
									if($answer && !empty($answer)) {
										$answerText 		=	'';
									?>
									<ul class="q-single-answer" id="ans_ul-<?=$questionId?>">
										<input type="hidden" name="ans_submitted" id="ans_submitted">
										<h3>Solution</h3>
										<?php 

										foreach ($answer as $k => $ans) {
											$ansCreated 		=	$ans['modified']/1000;
											$ansCreated 			=	date('d-M-Y h:i a',$ansCreated);
											$ansCreated 			=	time_elapsed_string($ansCreated);
											$answerType 	=	isset($ans['type'])?$ans['type']:'text';
											$answerText 	=	isset($ans['text'])?$ans['text']:'';
											$answerImage 	=	isset($ans['imageUrl'])?$ans['imageUrl']:'';
											if(array_key_exists($ans['userId'], $user_list)) {
												$answerUser 	=	$user_list[$ans['userId']];
											} else {
												$answerUser			=	[];
											}
											$ansUpVote 				=	isset($ans['countInfo']['upVote'])?$ans['countInfo']['upVote']:0;
											$ansDownVote 				=	isset($ans['countInfo']['downVote'])?$ans['countInfo']['downVote']:0;
											$ansVote 					=	$ansUpVote-$ansDownVote;
											$ownAnswer 	=	false;
											if(isset($user_details['uid'])&&$user_details['uid']==$ans['userId']) {
												$ownAnswer 	=	true;
											}
										?>

										<li>
											<div class="ans-item">
												<div class="profile-content">
													<img src="<?=isset($answerUser['imageUrl'])&&$answerUser['imageUrl']!=''?$answerUser['imageUrl']:base_url().'assets\images\default_user_profile.jpg'?>" title="<?=isset($answerUser['username'])?$answerUser['username']:'User'?>" width="25" height="25" style="float: left; border-radius: 15px;" alt="<?=isset($answerUser['username'])?substr($answerUser['username'],0,1):'U'?>" />
													<h5 style="float: left;margin: 5px 10px;"><?=isset($answerUser['username'])?$answerUser['username']:"User"?>
													</h5>
												</div>
												<div class="profile-content-left">
													<?=$ansCreated?>
												</div>
												<div class="text-content">
												<p style="padding-left: 10px;font-size: 16px;"><?=$answerText?></p>
												</div>
												<div class="answer-action">
													<label class="q-list-left" title="<?=$ownAnswer?'':'Up Vote'?>">
														<i class="fa fa-thumbs-up <?=$ownAnswer?'':'ans-vote'?>" ans-id="<?=$ans['answerId']?>" vote-type="UP" style="font-size:24px"></i>
													</label>
													<label class="q-list-left" vote-count="<?=$ans['answerId']?>" style="text-align: center;"><?=$ansVote?></label>
													<label class="q-list-left" title="">
														<i class="fa fa-thumbs-down <?=$ownAnswer?'':'ans-vote'?>" vote-type="DOWN" ans-id="<?=$ans['answerId']?>" style="font-size:24px"></i>
													</label>
													<?php if(isset($user_details['uid']) && $user_details['uid'] == $ans['userId'] && $canUserAddAnswer == false) { ?>
													<label class="q-list-left" ans-id="<?=$ans['answerId']?>" title="">
														<i question-id="<?=$questionId?>" ans-id="<?=$ans['answerId']?>" title="Delete Answer" del-item="answer" class="fa fa-trash trash-elem"></i>
													</label>
													<?php } ?>
												</div>
												
											</div>
											<?= $answerImage !=''?'<div class="ans-item"><img src="'.$answerImage.'" alt="answer" /></div>':''?>				
										</li>
										
									
										<?php } ?>
									</ul>
									<?php } ?>
								</div> 
								<?php if(!$mcq && $canUserAddAnswer == true) { ?>
								<div class="row q-list-right add-answer">
									<div class="q-list-text">
										<div class="row">
											<div class="col-md-6">
												<div class="add-form">
													<input type="hidden" name="questionType" id="questionType" value="noMcq">
													<label>Answer:</label>
													<textarea class="form-control question-text-area" id="answer_text" name="answer_text"></textarea>
													<button class="btn btn-primary single-ans-submit" btn-question-id="<?=$questionId?>" style="float: right;margin: 10px;">Add answer</button>
													<br>
												</div>
											</div>
											<div class="col-md-6">
												<div class="user-answer-dup">
													<div class="row" id="user_answer_console">

													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<?php } ?>
							</div>
						</div>
					</div>	
					<?php }
					?>
			</div>
		</div>
	</div>
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

      CKEDITOR.replace('answer_text', {
        extraPlugins: 'ckeditor_wiris',
        // For now, MathType is incompatible with CKEditor file upload plugins.
        removePlugins: 'uploadimage,uploadwidget,filetools,filebrowser',
        height: 320,
        // Update the ACF configuration with MathML syntax.
        extraAllowedContent: mathElements.join(' ') + '(*)[*]{*};img[data-mathml,data-custom-editor,role](Wirisformula)'
      });
    }());

    var e = CKEDITOR.instances['answer_text']
	e.on( 'change', function( event ) {
		var answerText 	=	CKEDITOR.instances['answer_text'].getData();
		// answerText.replace(/<[^>]+>/g, '');
		loadLatex(answerText);
		// $("#user_answer_console").html(answerText);
		// MathJax.typeset(["#user_answer_console"]);
	});

	function loadLatex(latexString="") {
	    var link_url    = base_url+"question";
	    var post_data   = [{name: 'AJAX',value:'AJAX'},
	        {name:'TYPE',value:'HTM'},
	        {name:'ACTION',value:'LOAD_LATEX'},
	        {name:'LATEX_STRING',value:latexString},
	        {name:'LOAD_PAGE',value:'latex_parser'}
	      ];

	    $.ajax({
	      	url:link_url,
	      	dataType  :"html",
	      	type: "POST",
	      	data: post_data,
	      	success: function(response) {
	      		$(".user-answer-dup").show();
	        	$("#user_answer_console").html(response);
	        	// MathJax.typeset();
	      	},
	      	error: function() {
	      	},
	      	beforeSend: function() {
	          	// $(document.body).css({'cursor' : 'wait'});
	        }, 
	        complete: function() {
	          	// $(document.body).css({'cursor' : 'default'});
	        }
	    });
	  }
</script>