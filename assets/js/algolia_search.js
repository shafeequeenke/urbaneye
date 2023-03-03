	// $(document).ready(function() {
	// 	//search on load
	// 	var searchText 	= 	$(".algolia-search").val();
	// 	if( searchText != '' ) {
	// 		searchSuggetion(searchText);
	// 	}

	// 	$('#keyuptime').val('');
	// 	$(document).on("keyup",".algolia-search",function() {
	// 		var keyuptime 	=	0;
	// 		setInterval(function() {
	// 			keyuptime++;
	// 			if( keyuptime == 3 && $('#keyuptime').val() == '' ) {
	// 				$('#keyuptime').val(keyuptime+1);
	// 				var searchText 	= 	$(".algolia-search").val();
	// 				searchSuggetion(searchText);
	// 			}
	// 	  	}, 1000);

	// 		// setTimeout(function() {
	// 			// if($('#keyuptime').val() == '') {
					
	// 			// }
	// 		// }, 4000);

	// 	});

	// 	$(document).on("click",".algolia-search-btn",function() {
	// 		var searchText 	= 	$(".algolia-search").val();
	// 		searchSuggetion(searchText);
	// 	});
	// });


	// function searchSuggetion(searchText) { 
	// 	link_url 		=	base_url+"question";
	// 	var post_data 		=	[];
	// 	post_data.push(
 //  			{name:'AJAX',value:'AJAX'},
 //  			{name:'TYPE',value:'HTM'},
 //  			{name:'LOAD_PAGE',value:'course_question'},
	// 			{name:'ACTION',value:'ALGOLIA_SEARCH'},
	// 		{name:'SEARCH_KEY',value:searchText}
	// 	);
		
	// 	$.ajax({
	// 	  	url         : link_url,
	// 	  	type        : "POST",
	// 	    dataType    : "html",
	// 	  	data        : post_data,
	// 	  	success     : function(response) {
	// 	  		$('#keyuptime').val('');
	// 	  		// if( response.STATUS == 200 ) {
	// 	  			$('#searchResult').html(response);
	// 	  		// }
	// 	    },
	// 	    error       : function(error_param,error_status) {
	// 	    },
	// 	    beforeSend: function() {
	// 	    }, 
	// 	    complete: function() {
	// 	    }
	// 	});
	// }