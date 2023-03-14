<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>CodePen - Bootstrap slider with Text Animation</title>
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css'>
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.0/animate.css'>

</head>
<body>
<!-- partial:index.partial.html -->
<header>
  <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
    <!-- Indicators -->
    <ol class="carousel-indicators">
      <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
      <li data-target="#carousel-example-generic" data-slide-to="1"></li>
      <li data-target="#carousel-example-generic" data-slide-to="2"></li>
    </ol>

    <!-- Wrapper for slides -->
    <div class="carousel-inner" role="listbox">
      <div class="item active">
        <img src="https://i.postimg.cc/wTBDN2JW/1.jpg" alt="...">
        <div class="carousel-caption">
          <h2 class="animated bounceInRight" style="animation-delay: 1s">We Are Reliable</h2>
          <h3 class="animated bounceInLeft" style="animation-delay: 2s">Lorem ipsum dolor sit amet.</h3>
        </div>
      </div>
      <div class="item">
        <img src="https://i.postimg.cc/GhHwf0Gv/2.jpg" alt="...">
        <div class="carousel-caption">
          <h2 class="animated slideInDown" style="animation-delay: 1s">We Deliver On Time</h2>
          <h3 class="animated slideInRight" style="animation-delay: 2s">Lorem ipsum dolor sit amet.</h3>
        </div>
      </div>
      <div class="item">
        <img src="https://i.postimg.cc/ncsgk4fk/3.jpg" alt="...">
        <div class="carousel-caption">
          <h2 class="animated zoomIn" style="animation-delay: 1s">Best Customer Support</h2>
          <h3 class="animated zoomIn" style="animation-delay: 2s">Lorem ipsum dolor sit amet.</h3>
        </div>
      </div>

    </div>

    <!-- Controls -->
    <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
      <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
      <span class="sr-only">Previous</span>
    </a>
    <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
      <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
      <span class="sr-only">Next</span>
    </a>
  </div>

</header>



<!--- ignore the code below-->

<div class="link-area">
  <a href="https://www.youtube.com/channel/UCki4IDK86E6_pDtptmsslow" target="_blank">Click for More</a>
</div>
<!-- partial -->
  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js'></script>
</body>
</html>
