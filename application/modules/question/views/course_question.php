<?php 
	if( !empty($course_question) && isset($is_latex) && $is_latex) { ?>
<style type="text/css">
	mjx-container[jax="CHTML"][display="true"] {
	    text-align: left !important;
	}
	.radio label .MathJax {
		top: -12px;
	}
</style>
<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
<script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
	<?php
	}
?>

<!-- Section eleven start -->
<div class="ed_courses">
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<?php 
					$course_slug 			=	isset($course_slug)?$course_slug:"";
					$load_question 			=	isset($load_question)?$load_question:"public";
					$load_param 			=	"question=".$load_question;
					$page_link 				=	base_url()."course/".$course_slug."?".$load_param."&tag=";	
							if(is_array($course_tags)) {
										echo "<div class='tags_list'><ul>";
										echo '<li class="active"><a href="'.$page_link.'">All Questions</a></li>';
										foreach ($course_tags as $tagkey => $tagvalue) {
												echo '<li><a href="'.$page_link.$tagvalue.'">'.$tagvalue.'</a></li>';
										}
										echo "</ul></div>";
									}
							 ?>
			</div>
			<?php 
			if( !empty($course_question)) {
				foreach ($course_question as $key => $question) {
					$answerText				=	'';
					$questionId 			=	$question['questionId'];
					$questionType 			=	$question['type'];
					$mcq 					=	isset($question['isChoiceQuestion'])&&$question['isChoiceQuestion']?true:false;
					$options 				=	$mcq?(isset($question['options'])?$question['options']:''):'';
					$tags 					=	isset($question['tags'])?$question['tags']:'';
					$questionText 			=	$question['text'];
					$questionSlug 			=	isset($question['slug'])&&$question['slug']!=""?$question['slug']:$questionId;
					$questionLink 			=	base_url('question/'.$questionSlug);
					$questionImage 			=	isset($question['imageUrl'])?$question['imageUrl']:'';
					$questionCreated 		=	$question['modified']/1000;
					$createdDate 			=	date('d-M-Y h:i a',$questionCreated);
					$createdDate 			=	time_elapsed_string($createdDate);
					$answer 				=	isset($question['answer'])?$question['answer']:[];
					$answerCount 			=	count($answer);
					if(array_key_exists($question['userId'], $user_list)) {
						$questionUser 			=	$user_list[$question['userId']];
					} else {
						$questionUser			=	[];
					}

					$upVote 				=	isset($question['countInfo']['upVote'])?$question['countInfo']['upVote']:0;
					$downVote 				=	isset($question['countInfo']['downVote'])?$question['countInfo']['downVote']:0;
					$vote 					=	$upVote-$downVote;

					$isUpvoted 				=	isset($question['userVote'])&&$question['userVote']['upVote']>=1?true:false;
					$isDownvoted 			=	isset($question['userVote'])&&$question['userVote']['downVote']>=1?true:false;
					$isFavorite 			=	$question['isFavourite']?$question['isFavourite']:false;
					$answeredUsers 			=	$question['answeredUsers'];

					$canUserAddAnswer 		=	true;
					$can_add_ans_htm 		=	'can-add-answer="yes"';

					if( isset($user_details['uid']) && in_array($user_details['uid'], $answeredUsers) ) {
						$canUserAddAnswer 	=	false;
						$can_add_ans_htm 	=	$canUserAddAnswer?'can-add-answer="yes"':'title="You already added Answer"';
					}
					$answer_page_htm 		=	'page="courseView"';
					$htmlContent 			=	(isset($question['htmlContent'])?$question['htmlContent']:false);
					$qMimeType 					=	isset($question['mimeType'])?$question['mimeType']:"text/plain";			
			?>

			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="width: 99%;">
				<div class="row q-list-item">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 q-list-container" style="padding: 2px;">
						<!-- question user dispaly name -->
						<span class="q-list-right align-left q-list-profile" style="">
							<?php
							$userImg 	=	isset($questionUser['imageUrl'])?$questionUser['imageUrl']:base_url().'assets\images\default_user_profile.jpg';
							?>
							<img src="<?=$userImg?>" width="25" height="25" style="float: left; border-radius: 15px;" alt="<?=isset($questionUser['username'])?substr($questionUser['username'],0,1):'U'?>"/>
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
									echo '<div class="q-list-text">'?><a href="<?php echo $questionLink;?>" ><?= $questionText?></a><?php '</p>' ;
									// echo '<div class="q-list-text">'.$questionText;
									if($mcq&&is_array($options)) {
										echo "<ul>";
										
										foreach ($options as $optkey => $optvalue) {
											$optVal 		=	$htmlContent?"":$optvalue;
											$radio_htm 	=	'<div class="radio">
													<label>
														<input id="mcq-'.$optkey.$key.'" type="radio" name="mcq-'.$key.'" class="mcq-opt control-success" value="'.$questionId.'" opt-value="'.$optVal.'" '.$answer_page_htm.'>
														'.$optvalue.'
													</label>
												</div>';

											// $radio_htm 	=	'<input id="mcq-'.$optkey.$key.'" class="mcq-opt" type="radio" name="mcq-'.$key.'" value="'.$questionId.'"/>';
											// $radio_htm 	.= '<label class="mcq-'.$key.'" for="mcq-'.$optkey.$key.'">'.$optvalue.'</label>';
											echo '<li '.$can_add_ans_htm.' class="mcq-options">'.$radio_htm.'</li>';
										}

										echo '<li class="option-save-btn"><i class="fa fa-save mcq-ans-submit ans-submit" btn-question-id="'.$questionId.'" question-mime-type="'.$qMimeType.'" title="Click to save"></i></li>';
										echo "</ul>";
										echo '<input type="hidden" id="saved-mcq-'.$questionId.'" value="" />';
									}
									echo '</div>';
								} else {
								?>
								<a href="<?php echo base_url('question/'.$questionId)?>" title="Question details">
									<label class="q-list-img" for="zoomQuestion_<?=$questionId?>">
								    	<img src="<?=$questionImage?>" style="max-width:25%;" alt="<?=isset($question['text'])?$question['text']:'Question'?>" />
									</label>
								</a>
								
							<?php }
							if(is_array($tags)) {
										echo "<div class='tags_list'><ul>";
										
										foreach ($tags as $tagkey => $tagvalue) {
												echo '<li>'.$tagvalue.'</li>';
										}
										echo "</ul></div>";
									}
							 ?>

						</div>
						<!-- question actions -->
						<div class="row q-list-right q-list-tags" >
							<label class="q-list-left <?=$isUpvoted?'':'question-vote-up'?>" title="<?=$isUpvoted?'Already Voted':'Up Vote'?>" question-id="<?=$questionId?>">
								<i class="fa fa-thumbs-up" style="font-size:24px"></i>
							</label>
							<label class="q-list-left" vote-count="<?=$questionId?>" style="text-align: center;"><?=$vote?></label>
							<label class="q-list-left <?=$isDownvoted?'':'question-vote-down'?>" question-id="<?=$questionId?>" title="<?=$isDownvoted?'Already Voted':'Down Vote'?>">
								<i class="fa fa-thumbs-down" style="font-size:24px"></i>
							</label>
							<!-- <label style="margin: 10px;" class="q-list-left <?=$isFavorite?'':'question-favourite';?>"  <?=$isFavorite?'':'title="Add to Favourite"';?> question-id="<?=$questionId?>">
								<i class="fa fa-heart" style="font-size: 22px;<?=$isFavorite?'color: red;':'';?>"></i>
							</label> -->
							<!-- answer count -->
							<label class="q-list-left" style="margin-left: 10px;">
								<i class="fa fa-sticky-note" style="font-size: 22px;"></i>
							</label>
							<label class="q-list-left" style="text-align: center;"><?=$answerCount?></label>
						</div>
					</div>
				</div>
			</div>	
			<?php
				}
			?>
			<?php if(isset($total_page)) { ?>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<ul class="pagination" style="float: right;">
					<?php 
					
					$nextPage 		=	$current_page+1;
					$prevPage 		=	$current_page-1;

					$next5 			=	$current_page+5;
					$prev5 			=	$current_page-5;

					$course_attr 			=	isset($course_id)?' course-id="'.$course_id.'" ':'';
					$course_slug 			=	isset($course_slug)?$course_slug:"";
					$load_question 			=	isset($load_question)?$load_question:"public";
					$load_param 			=	"question=".$load_question;
					$page_link 				=	base_url()."course/".$course_slug.($total_page>0?"?".$load_param."&page=":"");	


					if( $prev5 >0 ) {
						echo '<li><a href="'.$page_link.$prev5.'" title="Go to page '.$prev5.'" '.$course_attr.' class="change_page_changed" page="'.$prev5.'" style="padding: 8px 16px; text-decoration: none; font-weight: bold;"><i class="fa fa-arrow-left"></i></a></li>';
					}

					if($current_page >1) {
						echo '<li><a href="'.$page_link.$prevPage.'" title="Go to page '.$prevPage.'" '.$course_attr.' class="change_page_changed" page="'.$prevPage.'" style="padding: 8px 16px; text-decoration: none; font-weight: bold;">Prev</a></li>';
					}
					for($i=1; $i<=$total_page;$i++) {
						if($total_page > 9 && ($i>$current_page+3 || $i<$current_page-3) && $i!=$current_page && $i!=$total_page && $i!=1 ) {
							continue;
						}

						$pagination_attr 		=	$i==$current_page?'title="Current page"':'class="change_page_changed" title="Go to page '.$i.'" page="'.$i.'"';
						$active 				=	$i==$current_page?'active':'';
						
						$label 					=	(($total_page > 9 && $i>3 && $i < $total_page-2 && $i!=$current_page)?".":$i);
						
						echo '<li class="'.$active.'"><a href="'.$page_link.$i.'" '.$course_attr.$pagination_attr.' style="padding: 8px 16px; text-decoration: none; font-weight: bold;">'.$label.'</a></li>';
					}
					if($current_page < $total_page) {
						echo '<li><a href="'.$page_link.$nextPage.'" title="Go to page '.$nextPage.'" '.$course_attr.' class="change_page_changed" page="'.$nextPage.'" style="padding: 8px 16px; text-decoration: none; font-weight: bold;">Next</a></li>';
					}
					if( $next5 <= $total_page ) {
						echo '<li><a href="'.$page_link.$next5.'" title="Go to page '.$next5.'" '.$course_attr.' class="change_page_changed" page="'.$next5.'" style="padding: 8px 16px; text-decoration: none; font-weight: bold;"><i class="fa fa-arrow-right"></i></a></li>';
					}
					?>
				</ul>
			</div>
			<?php 
			}
			} else {
			?>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="width: 99%;">
				No Question Found.
			</div>
		<?php } ?>
	</div>
</div>