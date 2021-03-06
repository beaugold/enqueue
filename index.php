<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Enqueue</title>

<?php 
	// Includes externalscripts.php with common CSS and JS scripts for every page
	include 'src/templates/resources/externalscripts.php'; 
?>

<!-- Add the slick-theme.css if you want default styling -->
<link rel="stylesheet" type="text/css" href="scripts/slick/slick.css"/>
<!-- Add the slick-theme.css if you want default styling -->
<link rel="stylesheet" type="text/css" href="scripts/slick/slick-theme.css"/>
</head>

				

<body>
<?php 
	// Includes common site wide header template
	include 'src/templates/resources/header.php'; 
?>

<?php
	// For debugging purposes
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	
	// To populate homepage carousel
	// Query database for array of recently added movies
	$recentquery = "SELECT * FROM appdatastore WHERE datapoint = 1";
	$recentquery_dbreturn = mysqli_query($conn, $recentquery);
	
	$row = mysqli_fetch_array( $recentquery_dbreturn, MYSQLI_ASSOC );
	
	$recentquery_array = unserialize($row['appdata']);
	
	// Poster code variable
	$outputposters = "";
	
	foreach ($recentquery_array as $key=>$value) {
		$outputposters = $outputposters . "<div><img class='poster-slides' src='https://image.tmdb.org/t/p/w300/$recentquery_array[$key]'></div>";
	}
	
?>

<!-- Introductory Paragraph -->
<section class="introductory">
  <h1 class="col-xs-11 index-header-centered col-centered">Enqueue</h1>
  <div class="subtitle-responsive-text col-centered">
  	<p class="col-xs-11">This is an experimental website created to help you keep track of movies you want to watch in the most minimalist way possible.</p>
  	<p class="col-xs-11">Create an account today and start keeping track of those films that are important to you.</p>
  </div>
</section>

<!-- User movies scroller -->
<div style="padding-bottom: 328px;">
  <h1 class="col-xs-10 col-sm-10 col-centered index-subheader">What others have recently added to their lists...</h1>
  <div>
  	<div class="poster-carousel centering-carousel">
  	  <?php echo $outputposters; ?>
    </div>
  </div>
  
</div>

<!-- Displaying results -->

<?php 
	// Includes common site wide footer template
	include 'src/templates/resources/footer.php'; 
?>


<!-- Scripts for Slick carousel-->

<script type="text/javascript" src="scripts/slick/slick.min.js"></script>

<script>
	$(document).ready(function(){
		$('.poster-carousel').slick({
			speed: 500,
			slidesToScroll: 1,
			infinite: true,
			autoplay: true,
			autoplaySpeed: 2500,
			slidesToShow: 4,
			arrows: true,
			centerMode: true,
			centerPadding: '120px'
		});
	});
	
	$(document).ready(function(){
        $("#header-index").addClass("active");
    });
</script>


</body>


</html>