<script src="https://cdn.ckeditor.com/4.15.1/standard/ckeditor.js"></script>
<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
<script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
<script>
MathJax = {
  startup: {
    pageReady() {
      MathJax.startup.defaultPageReady()
        .then(() => {
          const svg = MathJax.tex2svg('\\frac{1}{x^2-1}', {display: true});
          console.log(MathJax.startup.adaptor.outerHTML(svg));
        })
        .catch((err) => console.log(err.message));
    }
  }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js"></script>
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <?php
            if(isset($latex_string)) {
            	echo $latex_string;
            }
    		?>
       </div>
    </div>
</div>
