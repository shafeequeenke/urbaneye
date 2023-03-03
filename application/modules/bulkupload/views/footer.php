<link href="https://cdn.rawgit.com/t4t5/sweetalert/v0.2.0/lib/sweet-alert.css" rel="stylesheet" type="text/css">
<script src="https://cdn.rawgit.com/t4t5/sweetalert/v0.2.0/lib/sweet-alert.min.js"></script>
<script type="text/javascript" src="<?=base_url();?>assets/ltr/js/plugins/forms/selects/select2.min.js"></script>
<?php
if($this->session->flashdata('success')){
?>
<script type="text/javascript">
		swal({
						title: "Upload Questions",
						text: "<?=$this->session->flashdata('success')?>!",
						type: "success",
						confirmButtonColor: '#DD6B55',
						confirmButtonText: 'OK',
						closeOnConfirm: true,
						closeOnCancel: true
					},
					function(isConfirm){
						//location.reload();
					});
</script>
<?php
}
if($this->session->flashdata('error')){
?>
<script type="text/javascript">

		swal({
						title: "Upload Questions",
						text: "<?=$this->session->flashdata('error')?>!",
						type: "error",
						confirmButtonColor: '#DD6B55',
						confirmButtonText: 'OK',
						closeOnConfirm: true,
						closeOnCancel: true
					},
					function(isConfirm){
						//location.reload();
					});

</script>	
<?php
}
?>
<script type="text/javascript">
jQuery(document).ready(function($) {

	$(document).on('click','#browse_file',function() {
		$('#question_file').click();
	});

	$(document).on('change','#question_file',function() {
		$('#uploaded-box').show();
		$('#upload-box').hide();
		var filename = $('#question_file').val().split('\\').pop();
		var filesize = this.files[0].size;
		var filesize = (this.files[0].size / 1024); 
		filesize = (Math.round(filesize * 100) / 100)
		$(".docx-size").text( filesize  + " kb"); 
		$('.docx-text').text(filename);
	});

	$(document).on('click','#delete_file',function() {
		$('#uploaded-box').hide();
		$('#upload-box').show();
		$('#question_file').val('');
	});
	$(document).on('click','#approve_question',function() {
		var post_data 		=	[];
		var questionIdList 	= 	[];
		var courseId	   	=   $('#course_id').val();
		var question_text	= 	'';
		var userId			=	'';
		var choiceQuestion	=	'';			
		var options			=	[];
		$.each($("input[name='questions']:checked"), function(){
		 	questionIdList.push($(this).val());
		});
        if(questionIdList.length == 0) {
        	alert('Please Select Any Of The Questions');
        }
        else if(courseId=='') {
        	alert('Please Select Any Of The Courses');
        }  
        else {	
        	post_data.push(
        		{name:'AJAX',value:'AJAX'},
				{name:'TYPE',value:'ACTION'},
				{name:'ACTION',value:'APPROVE_QUESTION'},
				{name:'questionIdList',value:questionIdList},
				{name:'courseId',value:courseId}
			);	
			$.ajax({
		      	url         : base_url+'bulkupload',
		      	type        : "POST",
		      	dataType    : "json",
		      	data        : post_data,
		      	success     : function(response) {
		      		swal({
						title: "Questions Approval",
						text: "Successfull!",
						type: "success",
						confirmButtonColor: '#DD6B55',
						confirmButtonText: 'OK',
						closeOnConfirm: true,
						closeOnCancel: true
					},
					function(isConfirm){
						location.reload();
					});
		      	},
		      	error: function(error_param,error_status) {

		      	},
		      	beforeSend: function () {
		          	$(document.body).css({'cursor' : 'wait'});
		      	}, 
		      	complete: function () {
		          	$(document.body).css({'cursor' : 'default'});
		      	}
		    });
		}	
	});
});





function submitQuestions(question_obj) {
    	link_url 	=	'<?=base_url()?>'+'bulkupload';
		post_data.push(
			{name:'TEXT',value:question_obj.text},
			{name:'OPTIONS',value:question_obj.options},
			{name:'AJAX',value:'AJAX'},
			{name:'TYPE',value:'ACTION'},
			{name:'ACTION',value:'ADD_QUESTION'}
		);

		$.ajax({
			url:link_url,
			dataType: "json",
			type: "POST",
			data: post_data,
			success: function(response) {
				console.log(response);
			},
			error: function() {
				console.log("response error");
			},
			beforeSend: function() {
	    		$(document.body).css({'cursor' : 'wait'});
		    }, 
		    complete: function() {
		    	$(document.body).css({'cursor' : 'default'});
		    }
		});
    }
</script>