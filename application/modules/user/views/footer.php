<script type="text/javascript">

// on ready function
jQuery(document).ready(function($) {	
	//load dashboard content
	var post_data 		=	[];
	post_data.push(
	  			{name:'AJAX',value:'AJAX'},
	  			{name:'TYPE',value:'HTM'},
	  			{name:'LOAD_PAGE',value:'user_dashboard'}
			);
	loadcontent('dashboard','dashboard',post_data);

	//load my course tab
	var post_data 		=	[];
	post_data.push(
		{name:'AJAX',value:'AJAX'},
		{name:'TYPE',value:'HTM'},
		{name:'ACTION',value:'LIST_USER_COURSE'},
		{name:'LOAD_PAGE',value:'user_course'}
	);
	loadcontent('course','courses',post_data);
});

	/**
  	*user data table ajax
  	**/
  	function loadLocationTable(locationType,parentId = '') {
  		var link_url 		=	"";
  		var post_data 		=	[];
  		post_data.push(
  			{name:'AJAX',value:'AJAX'},{name:'TYPE',value:'DT'}
		);
  		
  		$.ajax({
	      	url         : link_url,
	      	type        : "POST",
		    dataType    : "html",
	      	data        : post_data,
	      	success     : function(response) {
	      		$("#"+htm_id).html(response);
	      		
		    },
		    error       : function(error_param,error_status) {

		    },
		    beforeSend: function() {

		    }, 
		    complete: function() {

		    }
	    });
  	}
</script>