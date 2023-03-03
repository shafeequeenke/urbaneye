<?php
if(!function_exists('set_header_contents')) {
	function set_header_contents( $seoData ) {
		$headerArr 						=	[];
		$seoDefault 					=	default_seo_content();

		$headerArr['title'] 			= 	isset($seoData['title'])?$seoData['title']:$seoDefault['title'];
		$headerArr['meta_description']	=	isset($seoData['meta_description'])?$seoData['meta_description']:$seoDefault['meta_description'];
		$headerArr['meta_keywords']		=	isset($seoData['meta_keywords'])?$seoData['meta_keywords']:$seoDefault['meta_keywords'];
		$headerArr['author']	=	isset($seoData['author'])?$seoData['author']:$seoDefault['author'];
		$headerArr['meta_image']	=	isset($seoData['meta_image'])?$seoData['meta_image']:$seoDefault['meta_image'];
		$headerArr['og_title']			=	'Doubt Help';
		$headerArr['og_description']	=	'Doubt Help Online Learning Platform whoever like to share and learn.';
		

		return $headerArr;
	}
}

if(!function_exists('default_seo_content')) {
	function default_seo_content($key = '') {
		$headerArr 						=	[];
		$headerArr['title'] 			=	'Doubt Help Home || Learn';
		$headerArr['meta_description']	=	'Doubt Help Online Learning Platform whoever like to share and learn.';
		$headerArr['meta_keywords'] 	=	'Be your course Hero || Learn Online || Share and Learn || Online Learning Platform';
		$headerArr['author'] 			=	'Asterbyte Software Systems';
		$headerArr['meta_image'] 		=	'http://doubthelp.com/assets/images/notezen_logo.png';
		$headerArr['og_title']			=	'Doubt Help';
		$headerArr['og_description']	=	'Doubt Help Online Learning Platform whoever like to share and learn.';


		return $headerArr;
	}
}

if(!function_exists('seo_content_list')) {
	function seo_content_list($pageName,$customData = array()) {
		$contentArr 					=	[];

		switch ($pageName) {
			case 'Home':
				//home page
				$contentArr['title'] 			=	'Doubt Help - Online learning Platform For JEE and NEET';
				$contentArr['meta_description']	=	'Doubt Help Online Learning Programs for IIT JEE and NEET.';
				$contentArr['meta_keywords'] 	=	'Be your course Hero || Learn Online || Share and Learn || Online Learning Platform';
				$contentArr['author'] 			=	'Asterbyte Software Systems';
				$contentArr['meta_image'] 		=	'http://doubthelp.com/assets/images/noteszen_logo.png';
				$contentArr['og_title']			=	'Doubt Help';
				$contentArr['og_description']	=	'Doubt Help Online Learning Platform whoever like to share and learn.';


				break;
			case 'About':
				//home page
				$contentArr['title'] 			=	'About Doubt Help - Solutions For IIT JEE and NEET';
				$contentArr['meta_description']	=	'Doubt Help answers and solutions for maths, chemistry, physics, biology.';
				$contentArr['meta_keywords'] 	=	'Be your course Hero || Learn Online || Share and Learn || Online Learning Platform';
				$contentArr['author'] 			=	'Asterbyte Software Systems';
				$contentArr['meta_image'] 		=	'http://doubthelp.com/assets/images/notezen_logo.png';
				$contentArr['og_title']			=	'Doubt Help';
				$contentArr['og_description']	=	'Doubt Help Online Learning Platform whoever like to share and learn.';

				break;
			case 'FAQ':
				//home page
				$contentArr['title'] 			=	'Doubt Help - Learn Online';
				$contentArr['meta_description']	=	'Doubt Help Online Learning Platform whoever like to share and learn.';
				$contentArr['meta_keywords'] 	=	'Be your course Hero || Learn Online || Share and Learn || Online Learning Platform';
				$contentArr['author'] 			=	'Asterbyte Software Systems';
				$contentArr['meta_image'] 		=	'http://doubthelp.com/assets/images/notezen_logo.png';
				$contentArr['og_title']			=	'Doubt Help';
				$contentArr['og_description']	=	'Doubt Help Online Learning Platform whoever like to share and learn.';
				break;
			case 'custom':
				//load default seo content
				$seoDefault 					=	default_seo_content();
				$contentArr['title'] 			= 	isset($customData['title'])?$customData['title']:$seoDefault['title'];
				$contentArr['meta_description']	=	isset($customData['meta_description'])?$customData['meta_description']:$seoDefault['meta_description'];
				$contentArr['meta_keywords']	=	isset($customData['meta_keywords'])?$customData['meta_keywords']:$seoDefault['meta_keywords'];
				$contentArr['author']		=	isset($customData['author'])?$customData['author']:$seoDefault['author'];
				$contentArr['meta_image'] 		=	isset($customData['meta_image'])&&$customData['meta_image']!=''?$customData['meta_image']:$seoDefault['meta_image'];

				$contentArr['page_name']	=	isset($customData['page_name'])?$customData['page_name']:'Home';
				$contentArr['og_title']		=	'Doubt Help';
				$contentArr['og_description']	=	isset($customData['og_description'])?$customData['og_description']:$seoDefault['og_description'];
	
				break;
			default:
				//home page
				$contentArr['title'] 			=	'Doubt Help - Online learning Platform For JEE and NEET';
				$contentArr['meta_description']	=	'Doubt Help Online Learning Programs for IIT JEE and NEET.';
				$contentArr['meta_keywords'] 	=	'Be your course Hero || Learn Online || Share and Learn || Online Learning Platform';
				$contentArr['author'] 			=	'Asterbyte Software Systems';
				$contentArr['meta_image'] 		=	'http://doubthelp.com/assets/images/notezen_logo.png';
				$contentArr['og_title']			=	'Doubt Help';
				$contentArr['og_description']	=	'Doubt Help Online Learning Platform whoever like to share and learn.';
				break;
		}
		return $contentArr;
	}
}
?>