<!--single student detail start-->
<div class="ed_dashboard_wrapper">
	<div class="container">
		<div class="row">
			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<div class="ed_sidebar_wrapper">
					<div class="ed_profile_img">
					<img src="<?=$user_details['photo_url'] !=''?$user_details['photo_url']:'http://placehold.it/263X263';?>" alt="<?=$user_details['display_name'];?> Photo" />
					</div>
					<h3><?=$user_details['display_name'];?></h3>
					 <div class="ed_tabs_left">
						<ul class="nav nav-tabs">
						  
						  <li class="active"><a href="#courses" data-toggle="tab">my courses <span><?=count($user_course_list)?></span></a></li>
						  <li><a href="#dashboard" data-toggle="tab">profile</a></li>
						  <!-- <li><a href="#activity" data-toggle="tab">activity</a></li> -->
						  <!-- <li><a href="#notification" data-toggle="tab">notifications <span>0</span></a></li> -->
						  <!-- <li><a href="#profile" data-toggle="tab">profile</a></li> -->
						  <!-- <li><a href="#setting" data-toggle="tab">settings</a></li> -->
						  <!-- <li><a href="#forums" data-toggle="tab">forums</a></li> -->
						</ul>
					</div>
				</div>
			</div>
			<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
				<div class="ed_dashboard_tab">
				<div class="tab-content">
					<div class="tab-pane" id="dashboard">
						<!-- user dashboard append here by dashboard module -->
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin: 0 auto; padding: 10% 40%;">
    						<img src="<?=base_url();?>assets/images/noteszen_spinner.gif" />
						</div>
					</div>
					<div class="tab-pane active" id="courses">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin: 0 auto; padding: 10% 40%;">
    						<img src="<?=base_url();?>assets/images/noteszen_spinner.gif" />
						</div>
					</div>
					<div class="tab-pane" id="activity">
						<div class="ed_dashboard_inner_tab">
							<div role="tabpanel">
								<!-- Nav tabs -->
								<ul class="nav nav-tabs" role="tablist">
									<li role="presentation" class="active"><a href="#personal" aria-controls="personal" role="tab" data-toggle="tab">personal</a></li>
									<li role="presentation"><a href="#mentions" aria-controls="mentions" role="tab" data-toggle="tab">mentions</a></li>
									<li role="presentation"><a href="#favourites" aria-controls="favourites" role="tab" data-toggle="tab">favourites</a></li>
								</ul>
								<!-- Tab panes -->
								<div class="tab-content">
									<div role="tabpanel" class="tab-pane active" id="personal">
										<div class="ed_dashboard_inner_tab">
											<h2>What's new, andrehouse@123 ?</h2>
											<div class="row">
												<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
												<form class="ed_tabpersonal">
													<div class="form-group">
													<textarea name="whats_new" class="form-control" id="whats_new" cols="50" rows="5"></textarea>
													</div>
													<div class="form-group">
													<button class="btn ed_btn ed_green">post update</button>
													</div>
												</form>
												</div>
											</div>
										</div>
									</div>
									<div role="tabpanel" class="tab-pane" id="mentions">
										<div class="ed_dashboard_inner_tab">
											<h2>sorry, there was no mentions event found. please try a different filter</h2>
										</div>
									</div>
									<div role="tabpanel" class="tab-pane" id="favourites">
										<div class="ed_dashboard_inner_tab">
											<h2>sorry, there was no favourites event found. please try a different filter</h2>
										</div>
									</div>
								</div>
							</div><!--tab End-->
						</div>
					</div>
					<div class="tab-pane" id="notification">
						<div class="ed_dashboard_inner_tab">
							<div role="tabpanel">
										<!-- Nav tabs -->
								<ul class="nav nav-tabs" role="tablist">
									<li role="presentation" class="active"><a href="#unread" aria-controls="unread" role="tab" data-toggle="tab">unread</a></li>
									<li role="presentation"><a href="#read" aria-controls="read" role="tab" data-toggle="tab">read</a></li>
								</ul>
								<!-- Tab panes -->
								<div class="tab-content">
									<div role="tabpanel" class="tab-pane active" id="unread">
										<div class="ed_dashboard_inner_tab">
											<h2>you have no unread notifications</h2>
										</div>
									</div>
									<div role="tabpanel" class="tab-pane" id="read">
										<div class="ed_dashboard_inner_tab">
											<h2>you have no notifications</h2>
										</div>
									</div>
								</div>
							</div><!--tab End-->
						</div>
					</div>
					<div class="tab-pane" id="profile">
						<div class="ed_dashboard_inner_tab">
							<div role="tabpanel">
								<!-- Nav tabs -->
								<ul class="nav nav-tabs" role="tablist">
									<li role="presentation" class="active"><a href="#view" aria-controls="view" role="tab" data-toggle="tab">view</a></li>
									<li role="presentation"><a href="#edit" aria-controls="edit" role="tab" data-toggle="tab">edit</a></li>
									<li role="presentation"><a href="#change" aria-controls="change" role="tab" data-toggle="tab">change profile photo</a></li>
								</ul>
								<!-- Tab panes -->
								<div class="tab-content">
									<div role="tabpanel" class="tab-pane active" id="view">
										<div class="ed_dashboard_inner_tab">
											<h2>your profile</h2>
											<table id="profile_view_settings">
												<thead>
													<tr>
														<th>Name</th>
														<th>Id</th>
													</tr>
												</thead>

												<tbody>
													<tr>
														<td>Andre House</td>
														<td><a href="#">andrehouse@123</a></td>
													</tr>												
												</tbody>
											</table>
										</div>
									</div>
									<div role="tabpanel" class="tab-pane" id="edit">
										<div class="ed_dashboard_inner_tab">
											<h2>edit profile</h2>
											<form class="ed_tabpersonal">
												<div class="form-group">
												<input type="text" class="form-control"  placeholder="Your Name">
												</div>
												<div class="form-group">
												<p>This field can be seen by: <strong>Everyone</strong></p>
												</div>
												<div class="form-group">
												<button class="btn ed_btn ed_green">save changes</button>
												</div>
											</form>
										</div>
									</div>
									<div role="tabpanel" class="tab-pane" id="change">
										<div class="ed_dashboard_inner_tab">
											<h2>change photo</h2>
											<form class="ed_tabpersonal">
												<div class="form-group">
												<p>Click below to select a JPG, GIF or PNG format photo from your computer and then click 'Upload Image' to proceed.</p>
												</div>
												<div class="form-group">
												<input type="file" name="photo" accept="image/*">
												</div>
												<div class="form-group">
												<button class="btn ed_btn ed_green">upload image</button>
												</div>
												<div class="form-group">
												<p>If you'd like to delete your current avatar but not upload a new one, please use the delete avatar button.</p>
												</div>
												<div class="form-group">
												<button class="btn ed_btn ed_orange">delete</button>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div>
							<!--tab End-->
						</div>
					</div>
					<div class="tab-pane" id="setting">
						<div class="ed_dashboard_inner_tab">
							<div role="tabpanel">
								<!-- Nav tabs -->
								<ul class="nav nav-tabs" role="tablist">
									<li role="presentation" class="active"><a href="#general" aria-controls="general" role="tab" data-toggle="tab">general</a></li>
									<li role="presentation"><a href="#email" aria-controls="email" role="tab" data-toggle="tab">email</a></li>
									<li role="presentation"><a href="#visibility" aria-controls="visibility" role="tab" data-toggle="tab">profile visibility</a></li>
								</ul>
								<!-- Tab panes -->
								<div class="tab-content">
									<div role="tabpanel" class="tab-pane active" id="general">
										<div class="ed_dashboard_inner_tab">
											<h2>general setting</h2>
											<form class="ed_tabpersonal">
												<div class="form-group">
												<input type="text" class="form-control"  placeholder="Your Account Email">
												</div>
												<div class="form-group">
												<p>Change Password <strong>(leave blank for no change)</strong></p>
												</div>
												<div class="form-group">
												<input type="password" class="form-control"  placeholder="New Password">
												</div>
												<div class="form-group">
												<input type="password" class="form-control"  placeholder="Repeat New Password">
												</div>
												<div class="form-group">
												<button class="btn ed_btn ed_green">save changes</button>
												</div>
											</form>
										</div>
									</div>
									<div role="tabpanel" class="tab-pane" id="email">
										<div class="ed_dashboard_inner_tab">
											<h2>email notification</h2>
											<span>Send an email notice when:</span>
											<table id="notification_settings">
												<thead>
													<tr>
														<th class="title">Activity</th>
														<th class="yes">Yes</th>
														<th class="no">No</th>
													</tr>
												</thead>

												<tbody>
													<tr>
														<td>A member mentions you in an update using "@andrehouse123"</td>
														<td class="yes"><input type="radio" name="activity1" value="yes" checked="checked"></td>
														<td class="no"><input type="radio" name="activity1" value="no"></td>
													</tr>
													
													<tr>
														<td>A member replies to an update or comment you've posted</td>
														<td><input type="radio" name="activity2" value="yes" checked="checked"></td>
														<td><input type="radio" name="activity2" value="no"></td>
													</tr>
												</tbody>
											</table>
											<button class="btn ed_btn ed_green">save changes</button>
										</div>
									</div>
									<div role="tabpanel" class="tab-pane" id="visibility">
										<div class="ed_dashboard_inner_tab">
											<h2>profile visibility</h2>
											<table id="visibility_settings">
												<thead>
													<tr>
														<th class="title">Name</th>
														<th class="yes">Visibility</th>
													</tr>
												</thead>

												<tbody>
													<tr>
														<td>Andre House</td>
														<td>Everyone</td>
													</tr>		
												</tbody>
											</table>
											<button class="btn ed_btn ed_green">save setting</button>
										</div>
									</div>
								</div>
							</div><!--tab End-->
						</div>
					</div>
					<div class="tab-pane" id="forums">
						<div class="ed_dashboard_inner_tab">
						<div role="tabpanel">
						<!-- Nav tabs -->
							<ul class="nav nav-tabs" role="tablist">
								<li role="presentation" class="active"><a href="#started" aria-controls="started" role="tab" data-toggle="tab">topics started</a></li>
								<li role="presentation"><a href="#replies" aria-controls="replies" role="tab" data-toggle="tab">replies created</a></li>
								<li role="presentation"><a href="#favourite" aria-controls="favourite" role="tab" data-toggle="tab">favourite</a></li>
								<li role="presentation"><a href="#subscribed" aria-controls="subscribed" role="tab" data-toggle="tab">subscribed</a></li>
							</ul>
							<!-- Tab panes -->
							<div class="tab-content">
								<div role="tabpanel" class="tab-pane active" id="started">
									<div class="ed_dashboard_inner_tab">
										<h2>forum topics started</h2>
										<span>You have not created any topics.</span>
									</div>
								</div>
								<div role="tabpanel" class="tab-pane" id="replies">
									<div class="ed_dashboard_inner_tab">
										<h2>forum replies created</h2>
										<span>You have not replied to any topics.</span>
									</div>
								</div>
								<div role="tabpanel" class="tab-pane" id="favourite">
									<div class="ed_dashboard_inner_tab">
										<h2>favorite forum topics</h2>
										<span>You currently have no favourite topics.</span>
									</div>
								</div>
								<div role="tabpanel" class="tab-pane" id="subscribed">
									<div class="ed_dashboard_inner_tab">
										<h2>subscribed forums</h2>
										<span>You are not currently subscribed to any forums.</span>
									</div>
								</div>
							</div>
						</div><!--tab End-->
					</div>
					</div>
				</div>
			</div>
			</div>
		</div>
	</div>
</div>
<!--single student detail end-->
