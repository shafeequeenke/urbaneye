<?php
$questionId 		=	$question['questionId'];

if($question_editor == "edit_image") {
	$active_image 	=	"active";
	$active_latex 	=	"";
	$active_ck 		=	"";
	$active_text 	=	"";
	$image_link 	=	"#image";
	$latex_link 	=	base_url()."question/edit/".$questionId."/latex";
	$ck_link 		=	base_url()."question/edit/".$questionId."/html";
	$plain_link 	=	base_url()."question/edit/".$questionId."/text";
	$load_page 		=	"edit_imagequestion";
} else if($question_editor == "edit_latex") {
	$active_image 	=	"";
	$active_latex 	=	"active";
	$active_ck 		=	"";
	$active_text 	=	"";
	$image_link 	=	base_url()."question/edit/".$questionId."/image";
	$latex_link 	=	"#latex";
	$ck_link 		=	base_url()."question/edit/".$questionId."/html";
	$plain_link 	=	base_url()."question/edit/".$questionId."/text";
	$load_page 		=	"edit_latexquestion";
} else if($question_editor == "edit_ckeditor") {
	$active_image 	=	"";
	$active_latex 	=	"";
	$active_ck 		=	"active";
	$active_text 	=	"";
	$image_link 	=	base_url()."question/edit/".$questionId."/image";
	$ck_link 		=	"#ckeditor";
	$latex_link 	=	base_url()."question/edit/".$questionId."/latex";
	$plain_link 	=	base_url()."question/edit/".$questionId."/text";	
	$load_page 		=	"edit_ckquestion";
} else {
	$active_image 	=	"";
	$active_latex 	=	"";
	$active_ck 		=	"";
	$active_text 	=	"active";
	$image_link 	=	base_url()."question/edit/".$questionId."/image";
	$latex_link 	=	base_url()."question/edit/".$questionId."/latex";
	$ck_link 		=	base_url()."question/edit/".$questionId."/html";
	$plain_link 	=	"#text";
	$load_page 		=	"edit_plainquestion";
}
?>
<div class="ed_single_wrapper">
	<div class="container">
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="ed_dashboard_tab">
					<h2>Update Question</h2>
					<div class="tab-content">
						<div class="tab-pane active" id="course_material">
							<div class="ed_dashboard_inner_tab">
								<div role="tabpanel">
									<!-- Nav tabs -->
									<ul class="nav nav-tabs" role="tablist"> 
										<li role="presentation" class="<?=$active_text;?>"><a href="<?=$plain_link;?>" aria-controls="personal" role="tab" >Text</a></li>
										<li role="presentation" class="<?=$active_ck;?>"><a href="<?=$ck_link;?>" aria-controls="mentions" role="tab">HTML</a></li>
										<li role="presentation" class="<?=$active_latex;?>"><a href="<?=$latex_link;?>" aria-controls="favourites" role="tab" >Latex</a></li>
										<li role="presentation" class="<?=$active_image;?>"><a href="<?=$image_link;?>" aria-controls="createimage" role="tab" >Image</a></li>
									</ul>
									<!-- Tab panes -->
									<div class="tab-content">
										<div role="tabpanel" class="tab-pane active" id="personal">
											<div class="ed_dashboard_inner_tab">
												<div class="row">
													<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
													<?php
													$this->load->view($load_page);
													?>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>