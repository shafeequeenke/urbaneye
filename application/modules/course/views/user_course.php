<div class="ed_dashboard_inner_tab">
	<div role="tabpanel">
		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active">
				<a href="#result" aria-controls="result" role="tab" data-toggle="tab">courses</a>
			</li>
			<li role="presentation">
				<a href="#my" aria-controls="my" role="tab" data-toggle="tab">my courses</a>
			</li>
			<!-- <li role="presentation">
				<a href="#status" aria-controls="status" role="tab" data-toggle="tab">status</a>
			</li>
			<li role="presentation">
				<a href="#instructing" aria-controls="instructing" role="tab" data-toggle="tab">instructing courses</a>
			</li> -->
		</ul>
		<!-- Tab panes -->
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane" id="my">
				<div class="ed_inner_dashboard_info">
					<h2>You have <?=count($user_course_list)?> subscribed courses</h2>
					<div class="row">
						<div class="ed_mostrecomeded_course_slider">
						<?php
						if( !empty($user_course_list) ) {
							foreach ($user_course_list as $key => $course) {
								if(!isset($course['id']))
									continue;
							// p($user_course_list,"ccpp");
						?>
							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 ed_bottompadder20">
								<!-- <div class="ed_item_img">
									<img src="http://placehold.it/248X248" alt="item1" class="img-responsive">
								</div> -->
								<div class="ed_item_description ed_most_recomended_data">
									<h4>
										<a href="course_single.html">
											<?=$course['program']?$course['program']:''." ".$course['department']?> 
										</a>
										<!-- <span>£25</span> -->
									</h4>
									<div class="row">
										<div class="ed_rating">
											<div class="col-lg-6 col-md-7 col-sm-6 col-xs-6">
												<div class="row">
													<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
														<div class="ed_stardiv"></div>
													</div>
													<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
														<div class="row"></div>
													</div>
												</div>
											</div>
											<div class="col-lg-6 col-md-5 col-sm-6 col-xs-6">
												<div class="ed_views">
													<i class="fa fa-file-o"></i>
													<span>Questions:<?=$course['question_count']?></span>
												</div>
											</div>
										</div>
									</div>
									<p><?=$course['title']?$course['title']:''?></p>
									<a target="_blank" href="<?=base_url().'index.php/course?course='.$course['id']?>" class="ed_getinvolved">Go to Course<i class="fa fa-long-arrow-right"></i></a>
								</div>
							</div>
							<?php 
							} 
								} 
							?>
						</div>
					</div>
				</div>
			</div>
			<div role="tabpanel" class="tab-pane active" id="result">
				<div class="ed_inner_dashboard_info">
					<h2>You have <?=count($course_list)?> free courses</h2>
					<div class="row">
						<div class="ed_mostrecomeded_course_slider">
						<?php
						if( !empty($course_list) ) {
							foreach ($course_list as $key => $course) {
								if(!isset($course['id']))
									continue;
							// p($user_course_list,"ccpp");
						?>
							<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 ed_bottompadder20">
								<!-- <div class="ed_item_img">
									<img src="http://placehold.it/248X248" alt="item1" class="img-responsive">
								</div> -->
								<div class="ed_item_description ed_most_recomended_data">
									<h4>
										<a href="course_single.html">
											<?=$course['program']?$course['program']:''." ".$course['department']?> 
										</a>
										<!-- <span>£25</span> -->
									</h4>
									<div class="row">
										<div class="ed_rating">
											<div class="col-lg-6 col-md-7 col-sm-6 col-xs-6">
												<div class="row">
													<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
														<div class="ed_stardiv"></div>
													</div>
													<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
														<div class="row"></div>
													</div>
												</div>
											</div>
											<div class="col-lg-6 col-md-5 col-sm-6 col-xs-6">
												<div class="ed_views">
													<i class="fa fa-file-o"></i>
													<i class="fa fa-document"></i>
													<span>Questions:<?=$course['question_count']?></span>
												</div>
											</div>
										</div>
									</div>
									<p><?=isset($course['title'])?$course['title']:''?></p>
									<a target="_blank" href="<?=base_url().'index.php/course?course='.$course['id']?>" class="ed_getinvolved course-subscribe" course-id="<?=isset($course['id'])&& $course['id']?$course['id']:'';?>" >Go to Course<i class="fa fa-long-arrow-right"></i></a>
								</div>
							</div>
							<?php 
							} 
							} 
							?>
						</div>
					</div>
				</div>
			</div>
			<div role="tabpanel" class="tab-pane" id="status">
							<div class="ed_dashboard_inner_tab">
								<h2>some recent status about this course</h2>
								<p>Sed ultricies posuere magna elementum laoreet. Suspendisse elementum sagittis nisl, id pellentesque purus auctor finibus. Donec elementum quam est, a condimentum diam tempor ac. Sed quis magna lobortis, pulvinar est at, commodo mauris. Nunc in mollis erat. Integer aliquet orci non auctor pretium. Pellentesque eu nisl augue. Curabitur vitae est ut sem luctus tristique. Suspendisse euismod sapien facilisis tellus aliquam pellentesque.</p>
							</div>
						</div>
			<div role="tabpanel" class="tab-pane" id="instructing">
				<div class="ed_dashboard_inner_tab">
								<h2>you have not created any course</h2>
								<p>Sed ultricies posuere magna elementum laoreet. Suspendisse elementum sagittis nisl, id pellentesque purus auctor finibus. Donec elementum quam est, a condimentum diam tempor ac. Sed quis magna lobortis, pulvinar est at, commodo mauris. Nunc in mollis erat. Integer aliquet orci non auctor pretium. Pellentesque eu nisl augue. Curabitur vitae est ut sem luctus tristique. Suspendisse euismod sapien facilisis tellus aliquam pellentesque.</p>
				</div>
			</div>
		</div>
		
	</div><!--tab End-->
</div>