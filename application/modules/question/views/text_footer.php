<script type="text/javascript">
	$(document).ready(function() {
		if($("#question_is_mcq").prop("checked") == true) {
			updateOptionToCkeditor();
		}
//////////////drag and drop file upload////////////////////
		var $fileInput = $('.file-input');
		var $droparea = $('.file-drop-area');

		// highlight drag area
		$fileInput.on('dragenter focus click', function() {
		  $droparea.addClass('is-active');
		});

		// back to normal state
		$fileInput.on('dragleave blur drop', function() {
		  $droparea.removeClass('is-active');
		});

		// change inner text
		$fileInput.on('change', function() {
		  var filesCount = $(this)[0].files.length;
		  var $textContainer = $(this).prev();

		  if (filesCount === 1) {
		    // if single file is selected, show file name
		    var fileName = $(this).val().split('\\').pop();
		    $textContainer.text(fileName);
		  } else {
		    // otherwise show number of files
		    $textContainer.text(filesCount + ' files selected');
		  }
		});
	});
//////////////drag and drop file upload////////////////////

	//enable ckeditor for the options
 	$(document).on('click','.enable-ckedit-opt',function() {
 		updateOptionToCkeditor();
 	});

    window.setInterval(function(){
    	if($("#pasted_value").val() == "done") {
    		// parseQuestionOnPaste();
    	}
    }, 1000);

    function parseQuestionOnPaste() {
    	$("#pasted_value").val("");
    	link_url 			=	'question';
    	var post_data 		=	$( "#create_question" ).serializeArray();
		post_data.push(
			{name:'AJAX',value:'AJAX'},
			{name:'TYPE',value:'ACTION'},
			{name:'ACTION',value:'PARSE_QUESTION_TEXT'}
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

    $(document).on('change','#question_text',function(event) {
		loginRequiredPopup(is_user_logged);
		var submit_status 	=	$("[name='submit_question']").val();
		if( submit_status == 'forceSubmit') {
			$("[name='submit_form']").html('Submit <i class="fa fa-arrow-right"></i>');
			$("[name='submit_question']").attr('value','updateQuestion');
		}
	});
	
	function updateOptionToCkeditor() {
		//update all option values to ckeditor to avoid conflict
 		$( ".ans-opt-ck" ).each(function( index ) {
			var elemName 	=	$(this).attr('name');
			var elemVal 	=	$(this).val();
			$('[elem-name="'+elemName+'"]').hide();

		});
	}

</script>