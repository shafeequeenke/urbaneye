<?php
if(!function_exists('get_course_name')) {
	function get_course_name( $course ) {
		$courseName 		=	"";
		if(is_array($course) && count($course)>0) {
			$title 			=	isset($course['title'])?$course['title']:"";
			$program 		=	isset($course['program'])?$course['program']:"";
			$department 	=	isset($course['department'])?$course['department']:"";
			$courseName 	=	$title." ".$program." ".$department;

			return $courseName;
		}
		return false;
	}
}