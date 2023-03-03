<script type="text/javascript">
  $(document).ready(function() {
    if($("#question_is_mcq").prop("checked") == true) {
      updateOptionToCkeditor();
    }
  });

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
      link_url      = 'question';
      var post_data     = $( "#create_question" ).serializeArray();
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
    // loginRequiredPopup(is_user_logged);
    var myhtm   = $(this).val();
    loadLatex(myhtm);
  });

  function updateOptionToCkeditor() {
    //update all option values to ckeditor to avoid conflict
    $( ".ans-opt-ck" ).each(function( index ) {
      var elemName  = $(this).attr('name');
      var elemVal   = $(this).val();
      $('[elem-name="'+elemName+'"]').hide();
    //  CKEDITOR.replace( elemName
      //     , {
      //    toolbar :
      //    [
      //      { name: 'insert', items: [ 'Image','HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak' ] },
      //      { name: 'styles', items: [ 'Format','FontSize' ] },
      //      { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
      //    ]
      //  });
      // CKEDITOR.instances[elemName].setData('<p>'+elemVal+'</p>');
    });
  }

</script>
<script type="text/javascript">
  $(document).ready(function() {

    if($("#question_text").val() !="") {
      var myhtm   = $("#question_text").val();
      loadLatex(myhtm);
    }

    // document ready  
    $(document).on('keyup','#question_text',function() {
      var myhtm   = $(this).val();
      loadLatex(myhtm);
    });
  });

  function loadLatex(latexString="") {
    // loginRequiredPopup(is_user_logged);
    var link_url    = base_url+"question";
    var post_data   = [{name: 'AJAX',value:'AJAX'},
        {name:'TYPE',value:'HTM'},
        {name:'ACTION',value:'LOAD_LATEX'},
        {name:'LATEX_STRING',value:latexString},
        {name:'LOAD_PAGE',value:'latex_parser'}
      ];

    $.ajax({
      url:link_url,
      dataType  :"html",
      type: "POST",
      data: post_data,
      success: function(response) {
        $("#add_question_console").html(response);
        $(document.body).css({'cursor' : 'default'});
        MathJax.typeset();
      },
      error: function() {
        
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