<?php
$course_id_link		=	(isset($course_id)&&$course_id != ""?"/".$course_id:'');
if($question_editor == "create_image") {
	$this->load->view("image_header");
	$active_image   = 	"active";
	$active_latex 	=	"";
	$active_ck 		=	"";
	$active_text 	=	"";
	$image_link 	=	"#imgae";
	$latex_link 	=	base_url()."question/create/latex".$course_id_link;
	$ck_link 		=	base_url()."question/create/complex".$course_id_link;
	$plain_link 	=	base_url()."question/create/text".$course_id_link;
} else if($question_editor == "create_latex") {
	$this->load->view("latex_header");
	$active_image   = 	"";
	$active_latex 	=	"active";
	$active_ck 		=	"";
	$active_text 	=	"";
	$image_link 	=	base_url()."question/create/image".$course_id_link;
	$latex_link 	=	"#latex";
	$ck_link 		=	base_url()."question/create/complex".$course_id_link;
	$plain_link 	=	base_url()."question/create/text".$course_id_link;
} else if($question_editor == "create_ckeditor") {
	$this->load->view("ck_header");
	$active_image   = 	"";
	$active_latex 	=	"";
	$active_ck 		=	"active";
	$active_text 	=	"";
	$image_link 	=	base_url()."question/create/image".$course_id_link;
	$latex_link 	=	base_url()."question/create/latex".$course_id_link;
	$ck_link 		=	"#complex";
	$plain_link 	=	base_url()."question/create/text".$course_id_link;
} else {
	$this->load->view("text_header");
	$active_image   = 	"";
	$active_latex 	=	"";
	$active_ck 		=	"";
	$active_text 	=	"active";
	$image_link 	=	base_url()."question/create/image".$course_id_link;	
	$latex_link 	=	base_url()."question/create/latex".$course_id_link;
	$ck_link 		=	base_url()."question/create/complex".$course_id_link;
	$plain_link 	=	"#text";
}
?>
<div class="ed_single_wrapper">
	<div class="container">
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="ed_dashboard_tab">
					<h1>Create Question</h1>
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
										<div role="tabpanel" class="tab-pane <?=$active_text;?>" id="personal">
											<div class="ed_dashboard_inner_tab">
												<div class="row">
													<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
													<?php
													if($active_text == "active") {
														$this->load->view("create_plain");
													}
													?>
													</div>
												</div>
											</div>
										</div>
										<div role="tabpanel" class="tab-pane <?=$active_ck;?>" id="mentions">
											<div class="ed_dashboard_inner_tab">
												<div class="row">
													<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
													<?php
													if($active_ck == "active") {
														$this->load->view("create_ckquestion");
													}
													?>
													</div>
												</div>
											</div>
										</div>
										<div role="tabpanel" class="tab-pane <?=$active_latex;?>" id="favourites">
											<div class="ed_dashboard_inner_tab">
												<div class="row">
													<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
													<?php
													if($active_latex == "active") {
														$this->load->view("latex_question");
													}
													?>
													</div>
												</div>
											</div>
										</div>
										<div role="tabpanel" class="tab-pane <?=$active_image;?>" id="createimage">
											<div class="ed_dashboard_inner_tab">
												<div class="row">
													<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
													<?php
													if($active_image == "active") {
														$this->load->view("image_question");
													}
													?>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div> <!--tab End-->
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
if($question_editor == "create_image") {
	$this->load->view("image_footer");
} else if($question_editor == "create_latex") {
	$this->load->view("latex_footer");
} else if($question_editor == "create_ckeditor") {
	$this->load->view("ck_footer");
} else {
	$this->load->view("text_footer");
}
?>