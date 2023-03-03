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
		$fileInput.on('change', function(e) {
		  	var filesCount = $(this)[0].files.length;
		  	var $textContainer = $(this).prev();

		  	var validFileExtensions = ['jpeg', 'jpg', 'png'];
			var extension = $(this).val().split('.').pop().toLowerCase();
			if ($.inArray(extension, validFileExtensions) >=0) {

			} else {
				$textContainer.html("<span style='color:red;'>Extension ."+extension+ " not allowed.</span>");
				$("#add_question_console").html('');
				return false;
			}

		  	if (filesCount === 1) {
		    	// if single file is selected, show file name
		    	var fileName = $(this).val().split('\\').pop();
		    	$textContainer.text(fileName);
		  	} else {
		    	// otherwise show number of files
		    	return false;
		    	$textContainer.text('Single file allowed');
		  	}
		});
	});
//////////////drag and drop file upload////////////////////

  ////////change image on drop files/////////////
  $(document).on("change","#question_image",function (e) {
    e.preventDefault();
    link_url    = base_url+'user';
    var post_data     = [   
            {name:'AJAX',value:'AJAX'},
            {name:'TYPE',value:'ACTION'},
            {name:'ACTION',value:'FIREBASE_CREDS'}
          ];
    $.ajax({
        url         : link_url,
        type        : "POST",
        dataType    : "json",
        data        : post_data,
        async     : false,
        success     : function(response) {
          if( response.STATUS == 200 ) {
            uploadQuestionFile(response.fireCreds);
          }
        },
        error       : function(error_param,error_status) {

        },
        beforeSend: function() {

        }, 
        complete: function() {

        }
    });
  });



function uploadQuestionFile(fireCreds) {
	// TODO: Replace the following with your app's Firebase project configuration
	var firebaseConfig = {
	    apiKey: fireCreds.fire_apiKey,
	    authDomain: fireCreds.fire_authDomain,
	    databaseURL: fireCreds.fire_databaseURL,
	    projectId: fireCreds.fire_projectId,
	    storageBucket: fireCreds.fire_storageBucket,
	    messagingSenderId: fireCreds.fire_messagingSenderId,
	    appId: fireCreds.fire_appId
  	};

  	if (!firebase.apps.length) {
  	// Initialize Firebase
		firebase.initializeApp(firebaseConfig);
	}

	var timestamp = Number(new Date());
	var storage = firebase.storage().ref();
	var fileName =	timestamp.toString();
	var storageRef    = storage.child("questions/"+fileName);
	var file_data = $("#question_image").prop("files")[0];
	var storeRef = storageRef.put(file_data);

	storeRef.then(snapshot => snapshot.ref.getDownloadURL())
  		.then((url) => {
		parseQuestionUpload(url,fileName);
	});
}

function parseQuestionUpload(url,fileName) {
	// start ajax for parse image
   	link_url 	=	'<?=base_url()?>'+'question';
   	var post_data 		=	[		
			{name:'AJAX',value:'AJAX'},
			{name:'TYPE',value:'ACTION'},
			{name:'ACTION',value:'PARSE_QUESTION_IMAGE'},
			{name:'FILE_NAME',value:url}
		];		
	
	var question_obj 	=	"";
	var question_htm 	=	"";
	var question_tex 	=	"";
	var question_lat 	=	"";
    $.ajax({
 		url:link_url,
		dataType: "json",
		type: "POST",
		data: post_data,
		success: function(response) { 
			if(response.STATUS == "200") {
				question_obj 	=	response.AJAX_RES;
				updateQuestionField(question_obj,url,fileName);					
			}
		},
		error: function(error) {
			console.log(error);
		},
		beforeSend: function() {
    		$(document.body).css({'cursor' : 'wait'});
	    }, 
	    complete: function() {
	    	$(document.body).css({'cursor' : 'default'});
	    }
	});
}

	//update question field from image
	function updateQuestionField(questionObj,imageUrl,fileName) {
		if(questionObj.request_id != "" && questionObj.confidence_rate > 0.06) {
			var questionText 	=	"";
			if( questionObj.data.length >=1 ) {
				var data 			=	questionObj.data;
				var questionText 	=	questionObj.text;
				if(data[0].type=="latex" || data[1].type=="latex") {
					var latex_ans 	=	"";
					var ascci_ans 	=	"";
					$.each(data,function(key,val) {
						if(val.type == "latex") {
							latex_ans = latex_ans+val.value;
						} else if (val.type == "asciimath") {
							ascci_ans = ascci_ans+" "+val.value;
						}
					});
					if(latex_ans !="") {
						questionTxt 	=	latex_ans;
					} else if(ascci_ans !="") {
						questionTxt 	=	ascci_ans;
					}
					//alert for image question render
					swal({
			            title: "Seems image contains complex equation.",
			            // text: "You can use it as complex equation with question editor!",
			            text:"Content: "+questionTxt,
			            type: "warning",
			            showCancelButton: true,
			            confirmButtonColor: "#EF5350",
			            confirmButtonText: "Yes, use it!",
			            cancelButtonText: "No, Use as image!",
			            closeOnConfirm: true,
			            closeOnCancel: true
			        },
			        function(isConfirm){
			            if (isConfirm) {
			                var course_id 		=	$("#course_id").val();
							var url = base_url + "question/create/latex/"+course_id;
							var form = $('<form action="' + url + '" method="post">' +
							  '<textarea id="latex_question_text" name="latex_question_text" >'+questionText+'</textarea>' +
							  '</form>');
							$('body').append(form);
							form.submit();
			            }
			            else {
                        	var image_htm 	=	'<img class="question_file" file_name="'+fileName+'" src="'+imageUrl+'" width="100%" height="">';
				        	$("#add_question_console").html(image_htm);
							$("[name='submit_form']").html('Submit as image <i class="fa fa-image"></i>');
							$("[name='submit_question']").attr('value','imageSubmit');
			            }
			        });					
				}
			} else {
				//update question text from image
				questionTxt 		=	questionObj.text;
				swal({
		            title: "Seems image contains text.",
		            text: "Content: "+questionTxt,
		            // html:"Content: "+questionTxt,
		            type: "warning",
		            showCancelButton: true,
		            confirmButtonColor: "#EF5350",
		            confirmButtonText: "Yes, use it!",
		            cancelButtonText: "No, Use as image!",
		            closeOnConfirm: true,
		            closeOnCancel: true
		        },
		        function(isConfirm){
		            if (isConfirm) {
		                var course_id 		=	$("#course_id").val();
		                questionTxt 		=	questionObj.text;
						var url = base_url + "question/create/complex/"+course_id;
						var form = $('<form action="' + url + '" method="post">' +
						  '<textarea id="complex_question_text" name="complex_question_text" >'+questionTxt+'</textarea>' +
						  '</form>');
						$('body').append(form);
						form.submit();
		            } else {
                    	var image_htm 	=	'<img class="question_file" file_name="'+fileName+'" src="'+imageUrl+'" width="100%" height="">';
			        	$("#add_question_console").html(image_htm);
						$("[name='submit_form']").html('Submit as image <i class="fa fa-image"></i>');
						$("[name='submit_question']").attr('value','imageSubmit');
		            }
		        });
			}
		} else if(questionObj.error_info != undefined && (questionObj.error_info.id == "image_no_content" || questionObj.error_info.id == 'sys_exception' || questionObj.error_info.id == 'image_max_size')) {
        	var image_htm 	=	'<img class="question_file" file_name="'+fileName+'" src="'+imageUrl+'" width="100%" height="">';
        	$("#add_question_console").html(image_htm);
			$("[name='submit_form']").html('Submit as image <i class="fa fa-image"></i>');
			$("[name='submit_question']").attr('value','imageSubmit');
		} else {
        	var image_htm 	=	'<img class="question_file" file_name="'+fileName+'" src="'+imageUrl+'" width="100%" height="">';
        	$("#add_question_console").html(image_htm);
			$("[name='submit_form']").html('Submit as image <i class="fa fa-image"></i>');
			$("[name='submit_question']").attr('value','imageSubmit');
		}
	}
////////////////update images ondrop image end/////////////

	//enable ckeditor for the options
 	$(document).on('click','.enable-ckedit-opt',function() {
 		updateOptionToCkeditor();
 	});

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