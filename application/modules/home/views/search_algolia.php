<style type="text/css">
	.search-tags li {
		list-style: none;
		float: left;
		margin: 5px 5px;
		background: #e7e7e7;
		line-height: 30px;
		padding: 5px 8px;
		cursor: pointer;
	}
	.search-result {
		min-height: 250px;
	}
</style>
<!--Our expertise section one start -->
<div class="ed_transprentbg">
	<div class="container">
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<!-- <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
					<div class="form-group">
						<select class="form-control" id="course_name" name="course_name">
				        	<option>Select Course</option>
					        <option>IIT/JE Mat</option>
				      	</select>
					</div>
				</div> -->
				<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
					<div class="form-group search-text">
						<input type="text" placeholder="Search Question" name="search" class="algolia-search form-control" id="search" value="<?=(isset($search_text)?$search_text:'')?>" style="min-height: 53px;border: 1px solid #ccc;width: 100%;border-radius: 10px !important; margin:6px;"/>
					</div>
				</div>
				<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
					<div class="form-group search-btn">
						<input class="algolia-search-btn form-control button" value="Search" type="button" name="searchAlgolia">
						<input type="hidden" id="keyuptime" name="">
					</div>
				</div>
				<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 search_load" >
					<img src="<?=base_url();?>assets/images/question_search.webp">
				</div>
			</div>
		</div>
		<div class="row" id="courseTagList">
		</div>
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="tab-content search-result" id="searchResult">	
				</div>
			</div>
		</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$(".search_load").hide();
		$('#keyuptime').val('');
			//search on load
		var searchText 	= 	$(".algolia-search").val();
		if( searchText != '' ) { 
			searchSuggetion(searchText);
		}
		$(document).on("keyup",".algolia-search",function() {
			var keyuptime 	=	0;
			setInterval(function() {
				keyuptime++;
				if( keyuptime == 3 && $('#keyuptime').val() == '' ) {
					$('#keyuptime').val(keyuptime+1);
					var searchText 	= 	$(".algolia-search").val();
					searchSuggetion(searchText);
				}
		  	}, 1000);

			// setTimeout(function() {
				// if($('#keyuptime').val() == '') {
					
				// }
			// }, 4000);

		});

		$(document).on("click",".algolia-search-btn",function() {
			var searchText 	= 	$(".algolia-search").val();
			searchSuggetion(searchText);
		});

		$(document).on('click','.course-hash-tag',function() { 
			var post_data 		=	[];
			var course_id 		=	$(this).attr('course-id');
			var elem 			=	$(this);
			var course_name 	=	elem.text();
			post_data.push( 
			  			{name:'AJAX',value:'AJAX'},
			  			{name:'TYPE',value:'HTM'},
			  			{name:'ACTION',value:'LIST_COURSE_QUESTION'},
			  			{name:'LOAD_PAGE',value:'course_question'},
			  			{name:'COURSE_ID',value:course_id}
					);

			$.ajax({
		      	url         : base_url+'question',
		      	type        : "POST",
		      	dataType    : "html",
		      	data        : post_data,
		      	success     : function(response){
		        	$("#searchResult").html(response);
		        	$(document).prop('title', 'Doubt help ask question on '+course_name);
		      	},
		      	error       : function(error_param,error_status) {
		      	},
		      	beforeSend: function() {
		    		$(".search_load").show();
			    }, 
			    complete: function() {
			    	$(".search_load").hide();
			    }
		    });
		});

		$(document).on('click','.change_page',function() {
			var post_data 		=	[];
			var course_id 		=	$(this).attr('course-id');
			post_data.push( 
			  			{name:'AJAX',value:'AJAX'},
			  			{name:'TYPE',value:'HTM'},
			  			{name:'ACTION',value:'LIST_COURSE_QUESTION'},
			  			{name:'LOAD_PAGE',value:'course_question'},
			  			{name:'COURSE_ID',value:course_id}
					);
			var page 	=	$(this).attr('page');
			post_data.push({name:'page',value:page});

			$.ajax({
		      	url         : base_url+'question',
		      	type        : "POST",
		      	dataType    : "html",
		      	data        : post_data,
		      	success     : function(response){
		        	$("#searchResult").html(response);
		      	},
		      	error       : function(error_param,error_status) {
		      	},
		      	beforeSend: function () {
		          	$(".search_load").show();
		      	}, complete: function () {
		          	$(".search_load").hide();
		      	}
		    });
		});
			
	});

	function courseTagList() { 
		link_url 		=	base_url+"course";
		var post_data 		=	[];
		post_data.push(
  			{name:'AJAX',value:'AJAX'},
  			{name:'TYPE',value:'HTM'},
  			{name:'LOAD_PAGE',value:'course_tag_list'},
			{name:'ACTION',value:'COURSE_TAG_LIST'}
		);

		$.ajax({
		  	url         : link_url,
		  	type        : "POST",
		    dataType    : "html",
		  	data        : post_data,
		  	success     : function(response) {
		  		$('#keyuptime').val('');
	  			$('#courseTagList').html(response);
	  			
		    },
		    error       : function(error_param,error_status) {
		    },
		    beforeSend: function() {
		    	$(".search_load").show();
		    }, 
		    complete: function() {
		    	$(".search_load").hide();
		    }
		});
	}

	function searchSuggetion(searchText) { 
		link_url 		=	base_url+"question";
		var post_data 		=	[];
		post_data.push(
  			{name:'AJAX',value:'AJAX'},
  			{name:'TYPE',value:'HTM'},
  			{name:'LOAD_PAGE',value:'course_question'},
				{name:'ACTION',value:'ALGOLIA_SEARCH'},
			{name:'SEARCH_KEY',value:searchText}
		);

		$.ajax({
		  	url         : link_url,
		  	type        : "POST",
		    dataType    : "html",
		  	data        : post_data,
		  	success     : function(response) {
		  		$('#keyuptime').val('');
	  			$('#searchResult').html(response);
	  			$(document).prop('title', 'Doubt help ask question on '+searchText);
	  			courseTagList();
	    	},
		    error       : function(error_param,error_status) {
		    },
		    beforeSend: function() {
		    	$(".search_load").show();
		    }, 
		    complete: function() {
		    	$(".search_load").hide();
		    }
		});
	}
</script>