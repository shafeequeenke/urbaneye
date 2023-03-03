<?php
	if( !empty($course_tag_list)) {
		echo '<ul class="search-tags">';
		foreach ($course_tag_list as $key => $course) {
			$course_name 		=	isset($course['department'])?$course['department']:''.isset($course['title'])?$course['title']:'';
			echo '<li class="course-hash-tag" course-id="'.$course['id'].'">'.$course_name.'</li>';
		}
		echo '</ul>';
	}
?>