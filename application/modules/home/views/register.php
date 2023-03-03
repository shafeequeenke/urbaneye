<?php
$signUpFormAttr = [  
    'class'     =>  'ed_contact_form ed_toppadder40',
    'id'        =>  'signUpSubmit',
    'name'      =>  'signUpSubmit',
    'enctype'   =>  "multipart/form-data"
  ];
?>
<div class="ed_transprentbg ed_toppadder80 ed_bottompadder80">
	<div class="container">
		<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-lg-offset-3 col-md-offset-3">
				<div class="ed_teacher_div">
					<div class="ed_heading_top">
						<h3>sign up</h3>
					</div>
					<?=form_open('user/register', $signUpFormAttr);?>
						<div class="form-group">
							<label class="control-label">User Name :</label>
							<input type="text" class="form-control">
						</div>
						<div class="form-group">
							<label class="control-label">Email :</label>
							<input type="email" class="form-control">
						</div>
						<div class="form-group">
							<label class="control-label">Password :</label>
							<input type="password" class="form-control">
						</div>
						<div class="form-group">
							<label class="control-label">Confirm Password :</label>
							<input type="password" class="form-control">
						</div>
						<a href="#" class="btn ed_btn ed_orange pull-right">sign up</a>
					<?=form_close()?>
				</div>
			</div>
		</div>
	</div>
</div>