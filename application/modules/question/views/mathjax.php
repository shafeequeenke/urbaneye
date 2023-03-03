<script src="https://cdn.ckeditor.com/4.15.1/standard/ckeditor.js"></script>
<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
<script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
  <div class="container">
    <div class="row" style="padding: 20px;">
      <div class="col-md-6 question-container">
        <label>
          <h2>Question Text:</h2>
          <p>Note: Latex code acceptable.</p>
        </label>
        <textarea name="question_text" id="question_text" class="latex-question"></textarea>
      </div>
      <div class="col-md-6" id="equation">
      </div>
    </div>
    <div class="row" style="display: none;">
            <div class="col-md-6">
              <h3>Sample Mathjax parsed equation</h3>
            <p>
            In equation \eqref{eq:sample}, we find the value of an
            interesting integral:
            \begin{equation}
            \ce{CO2 + C -> 2 CO}
            \end{equation}

            \begin{equation}
              \int_0^\infty \frac{x^3}{e^x-1}\,dx = \frac{\pi^4}{15}
              \label{eq:sample}
            \end{equation}

            \begin{equation}
                    \ x = {-b \pm \sqrt{b^2-4ac} \over 2a}
            \end{equation}
            \begin{equation}
                    \ y = {a \pm \sqrt{a^3+b^2} \over 2b}
            \end{equation}
            </p>

            </div>
    </div>
  </div>
        



<script type="text/javascript">
// (function() {
//       var mathElements = [
//         'math','maction','maligngroup','malignmark','menclose','merror',
//         'mfenced','mfrac','mglyph','mi','mlabeledtr','mlongdiv','mmultiscripts',
//         'mn','mo','mover','mpadded','mphantom','mroot','mrow','ms','mscarries',
//         'mscarry','msgroup','msline','mspace','msqrt','msrow','mstack','mstyle',
//         'msub','msup','msubsup','mtable','mtd','mtext','mtr','munder','munderover','semantics','annotation','annotation-xml'
//       ];

//       CKEDITOR.plugins.addExternal('ckeditor_wiris', 'https://ckeditor.com/docs/ckeditor4/4.15.1/examples/assets/plugins/ckeditor_wiris/', 'plugin.js');

//       CKEDITOR.replace('question_text', {
//         extraPlugins: 'ckeditor_wiris',
//         // For now, MathType is incompatible with CKEditor file upload plugins.
//         removePlugins: 'uploadimage,uploadwidget,filetools,filebrowser',
//         height: 320,
//         // Update the ACF configuration with MathML syntax.
//         extraAllowedContent: mathElements.join(' ') + '(*)[*]{*};img[data-mathml,data-custom-editor,role](Wirisformula)'
//       });
//     }());


//   var e           =       CKEDITOR.instances['question_text'];
//   var myhtm       =       "";
//   e.on( 'change', function( event ) {
//           myhtm   =       CKEDITOR.instances['question_text'].getData();
//           myhtm   =       '<div class="row"><div class="col-md-6"><p>'+myhtm+'</p></div></div>';
//           loadLatex(myhtm);
//   });

  $(document).ready(function() {
    // document ready  
    $(document).on('change','#question_text',function() {
      var myhtm   = $(this).val(); alert(myhtm);
      loadLatex(myhtm);
    });
  });

  function loadLatex(latexString="") {
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
        $("#equation").html(response);
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