<script type="text/javascript">
	(function() {
	      var mathElements = [
        'math','maction','maligngroup','malignmark','menclose','merror',
        'mfenced','mfrac','mglyph','mi','mlabeledtr','mlongdiv','mmultiscripts',
        'mn','mo','mover','mpadded','mphantom','mroot','mrow','ms','mscarries',
        'mscarry','msgroup','msline','mspace','msqrt','msrow','mstack','mstyle',
        'msub','msup','msubsup','mtable','mtd','mtext','mtr','munder','munderover','semantics','annotation','annotation-xml'
      ];

      CKEDITOR.plugins.addExternal('ckeditor_wiris', 'https://ckeditor.com/docs/ckeditor4/4.15.1/examples/assets/plugins/ckeditor_wiris/', 'plugin.js');

      CKEDITOR.replace('question_text', {
        extraPlugins: 'ckeditor_wiris',
        // For now, MathType is incompatible with CKEditor file upload plugins.
        removePlugins: 'uploadimage,uploadwidget,filetools,filebrowser',
        height: 320,
        // Update the ACF configuration with MathML syntax.
        extraAllowedContent: mathElements.join(' ') + '(*)[*]{*};img[data-mathml,data-custom-editor,role](Wirisformula)'
      });
    }());

	$(document).ready(function() {
		if($("#question_is_mcq").prop("checked") == true) {
			updateOptionToCkeditor();
		}
	});


 	//enable ckeditor for the options
 	$(document).on('click','.enable-ckedit-opt',function() {
 		updateOptionToCkeditor();
 	});

    var e = CKEDITOR.instances['question_text'];
    e.on("paste",function( event ) {
    	$("#pasted_value").val("done");
    });
    var text = CKEDITOR.instances['question_text'].getData();
    if(text!='')
    {
    	checkQuestionTextMCQ(text);
    }

    window.setInterval(function(){
    	if($("#pasted_value").val() == "done") {
    		// parseQuestionOnPaste();
    	}
    }, 1000);

	function checkQuestionTextMCQ(text) {
		var link_url	=	base_url+"question";
    	var post_data 		=	[];
		post_data.push(
			{name:'TEXT',value:text},
			{name:'AJAX',value:'AJAX'},
			{name:'TYPE',value:'ACTION'},
			{name:'ACTION',value:'CHECK_QUESTION_TEXT_MCQ'}
		);
		console.log(post_data);
		$.ajax({
			url:link_url,
			dataType: "json",
			type: "POST",
			data: post_data,
			success: function(response) {
				options = response.OPTIONS;
				if(options.length>0)
				{
					swal({
							title: "Multiple choice question?",
							text: "Look like a multiple choice question, please verify.",
							type: "warning",
							showCancelButton: true,
							confirmButtonColor: '#DD6B55',
							confirmButtonText: 'Yes, it is!',
							cancelButtonText: "No, sumbit anyway!",
							closeOnConfirm: true,
							closeOnCancel: true
						},
						function(isConfirm){
							if (isConfirm){
								var mcqQuesText 		=	response.TEXT;
								var mcqOptions 			=	response.OPTIONS;

								$("#add_question_console").html("<h3>You submitted a Multiple choice question itseems.</h3><p>Please submit again to save the question.</p>").show();

								CKEDITOR.instances["question_text"].setData("<p>"+mcqQuesText+"</p>");
								$(".is-multiple").attr("checked","checked");
								$(".multiple-choice").show();
								
								var option_elem 	=	"";
								var elem_count		=	0;
								$.each(mcqOptions, function( index, value ) {
									if(value.trim() != "") { 
										elem_count 		=	(parseInt(elem_count)+1);
										option_elem 	=	"question_option"+elem_count;
										
										if(elem_count<4 &&elem_count>0) {
											optionckElement 	=	CKEDITOR.instances[option_elem];
											optionckElement.setData("<p>"+value+"</p>");
										} else if(elem_count>0) {
											addOptionElement(elem_count,value);	
										}
									} else if(elem_count>0){
										elem_count 	=	(parseInt(elem_count)-1);
									}
								});
							} else {
								$("#forceMcqSubmit").val("force");
								$("#create_question").submit();
							}
						});
				}
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

	function addOptionElement(total_elem,optionVal="") {
 		var option_htm	=	'<div class="radio radio-opt rem-'+(parseInt(total_elem)+1)+'"><label><input type="text" name="question_option'+(parseInt(total_elem)+1)+'" class="form-control ans-opt-ck" placeholder="Option '+(parseInt(total_elem)+1)+'"></label>&nbsp;&nbsp;<i title="remove" class="fa fa-trash rem-opt" col-num="'+(parseInt(total_elem)+1)+'"></i></div>';
 		$('.mcq-opt').append(option_htm);
 		var elemName 	=	'question_option'+(parseInt(total_elem)+1);
 		CKEDITOR.replace( elemName
		    , {
			toolbar :
			[
				{ name: 'insert', items: [ 'Image','HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak' ] },
				{ name: 'styles', items: [ 'Format','FontSize' ] },
				{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
			]
		});
		if(optionVal !="") {
			CKEDITOR.instances[elemName].setData(optionVal);
		}		
 	}

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

	e.on( 'change', function( event ) {
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
	 		CKEDITOR.replace( elemName
			    , {
					toolbar :
					[
						{ name: 'insert', items: [ 'Image','HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak' ] },
						{ name: 'styles', items: [ 'Format','FontSize' ] },
						{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
					]
				});
			CKEDITOR.instances[elemName].setData('<p>'+elemVal+'</p>');
		 	
		});
	}

	// fore creating question
 	$(document).on('change','.is-multiple',function() {
 		if(this.checked) {
 			$(".multiple-choice").show();
 		} else {
 			$(".multiple-choice").hide();
 		}
 	}); 
</script>