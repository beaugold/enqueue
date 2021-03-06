<!doctype html>
<html>

<head>
	<meta charset="UTF-8">
	<title>Enqueue - Your List</title>

	<?php include '../resources/externalscripts.php'; ?>

	<!-- Dropdown Stylesheet -->
	<link href="//rawgithub.com/indrimuska/jquery-editable-select/master/dist/jquery-editable-select.min.css" rel="stylesheet">
</head>

<body>
	<?php include '../resources/header.php'; ?>

	<?php
	if (!isset($_SESSION['login_user'])) {
		header("Location: login.php");
	}
	
	// Check user's movie list
	$currentmemberid = $_SESSION['memberid'];
	
	$listcheck = "SELECT * FROM usermovielist WHERE memberid = '$currentmemberid'";
	$planninglist = mysqli_query($conn, $listcheck);
	$planninglistarray = mysqli_fetch_array($planninglist);
	
	$planningstatus = $planninglistarray['planning'];
	
	$watchlistoutput = "";
	
	if ($planninglistarray['planning'] == NULL) {
		$watchlistoutput = "<div><div class='list-group-item flex-column' id='empty-list'><div class='empty-list'>
								<img class='empty-list-egg' src='../../../img/lucky-egg.svg' />
								<p class='empty-list-title'>There are no movies in your list</p>
								<p class='empty-list-text'>List zero you need to ask your friends to intro you to some movies.</p>
							</div></div></div>";
	} else {
		// Get planning JSON
		$obtainjson_sql = "SELECT * FROM usermovielist WHERE memberid = $currentmemberid";
		$obtainjson_dbreturn = mysqli_query($conn, $obtainjson_sql);
		$obtainjson_dbassocarr = mysqli_fetch_array( $obtainjson_dbreturn, MYSQLI_ASSOC );
		$obtainjson_jsonarray = json_decode($obtainjson_dbassocarr['planning'], true);
		
		// Display JSON data
		$jsonsize = count($obtainjson_jsonarray);
		
		
		for ($i = 0; $i < count($obtainjson_jsonarray); $i++){
			
			$currid = $obtainjson_jsonarray[$i]['movieID'];
			$currtitle = $obtainjson_jsonarray[$i]['title'];
			$currposter = 'https://image.tmdb.org/t/p/w300/' . $obtainjson_jsonarray[$i]['poster'];
			$currdesc = $obtainjson_jsonarray[$i]['overview'];
			$curryear = substr($obtainjson_jsonarray[$i]['year_released'],0 , 4);
			$curractors = $obtainjson_jsonarray[$i]['actors'];
			$start_date = new DateTime($obtainjson_jsonarray[$i]['date_added']);
			$since_start = $start_date->diff(new DateTime(date('d-m-Y H:i:s')));
			
			$hourselapsed = $since_start-> h;
			$minselapsed = $since_start->i;
			$secselapsed = $since_start->s;
			
			$timediff = $since_start;
			
			if ($hourselapsed > 23) {
				$timediff = $since_start->d . ' days ago';
			} else if ($hourselapsed > 0) {
				$timediff = $since_start->h . ' hours ago';
			} else if ($minselapsed > 0) {
				$timediff = $since_start->i . ' hours ago';
			} else if ($secselapsed < 61) {
				$timediff = $since_start->s . ' seconds ago';
			}
			
			
			$timediff1 = $since_start->i . ' mins';
			
			$watchlistoutput = $watchlistoutput . "<a class='list-group-item list-group-item-action flex-column align-items-start user-item' id='$currid'>
				<div class='d-flex w-100 justify-content-between'>
					<div class='mb-1'>
						<h5>$currtitle</h5>
						<p>$currdesc</p>
						<small class='description-gap'>Starring: $curractors</small>
						<br><small>Year Released: $curryear</small>
					</div>
					<small style='text-align: right;'>$timediff
					<div class='poster-alignment'>
						<img class='poster-styling' src='$currposter'>
					</div>
					</small>
				</div>
			</a>";
		}
		
	}
	
	
	?>

	<!-- Introductory Paragraph -->
	<section class="introductory-centered introductory">
		<h1 class="col-xs-10 other-header-centered col-centered">There are way too many good movies out there</h1>
		<p class="col-xs-10 subtitle-responsive-text col-centered">Your friends recommend you movies, you tell them you'd catch it when you get the free time. But then that free time comes and you simply have no idea what movie to watch.</p>
		<p class="col-xs-10 subtitle-responsive-text col-centered" style="padding-top: 16px;">Add that movie to Enqueue now while it is fresh in your mind. Then when the time comes, click on the movie to clear it!</p>
	</section>

	<!-- Movie search box -->
	<div class="row col-xs-10 searchbar-centered">
		<select id="editable-select" class="form-control form-control-lg searchbar-child-centered" placeholder="Start typing to add a movie" name="active-search" method="post" data-filter="false" data-effects="slide">
		</select>
		
		<p><?php if (isset($htmlfriendly)) {echo $htmlfriendly;} ?></p>
	</div>

	<!-- Displaying watchlist -->
	<div class="col-xs-10 empty-list-centered">
		<div class="list-group">
		<?php echo $watchlistoutput; ?>
			
		</div>
	</div>
	<?php include '../resources/footer.php'; ?>
</body>

<!-- Dropdown Scripts -->
<!-- https://github.com/indrimuska/jquery-editable-select -->

<script src="//rawgithub.com/indrimuska/jquery-editable-select/master/dist/jquery-editable-select.min.js"></script>

<!-- Plugin for noty -->
<link href="/scripts/noty/noty.css" rel="stylesheet">
<link href="/scripts/noty/themes/mint.css" rel="stylesheet">
<script src="/scripts/noty/noty.min.js" type="text/javascript"></script>

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">



<script>
	
</script>

<script type="text/javascript">
	$( document ).ready( function () {
		$( "#header-list" ).addClass( "active" );
		var movieselect = $( '#editable-select' );
		movieselect.editableSelect();
		
		
		// Global timeout variable for delayed search Reference: https://bit.ly/2qWuEyN
		var globaltimeout = null;
		
		// On keyup start searching
		$('#editable-select').on('keypress', function(e) {
			// Set globaltimeout back to null to reset search delay
			if (globaltimeout != null) {
				clearTimeout(globaltimeout);
			}
			
			// Saves current query into a variable
			var usersearch = $('.es-input').val();
			
			// Starts searching if length > 0
			if (usersearch.length >= 0) {
				// Add a loading indicator
				$('#editable-select').editableSelect('clear');
				$('#editable-select').editableSelect('add', '<img class="search-dropdown-loading" src="../../../img/826.svg">' );
				
				// Get search results after 0.5 seconds to reduce API calls
				globaltimeout = setTimeout(function(){
					globalTimeout = null;
					
					// Send to searchfunction to handle search and list population
					searchfunction(usersearch);
				}, 500);
			} 
			
			// Hide the dropdown when there is no entry
			if (usersearch.length <= 0) {
				$('#editable-select').editableSelect('hide');
				$('#editable-select').editableSelect('clear');
			}
		})
		
		// Search function
		function searchfunction(usersearch){
			// Reference: https://bit.ly/2K4IDKy
				$.ajax({ 
					url: 'tmdbinterface.php',
					data: {action: usersearch},
					type: 'post',
					dataType: 'json',
					success: function(output){
						// Clear loading indicator
						$('#editable-select').editableSelect('clear');
						
						// Add movie suggestions to dropdown
						$.each(output, function(index, value){
							$('#editable-select').editableSelect('add', function(){
								$(this).val(output[index].id);
								$(this).text(output[index].title + ' (' + output[index].release_date.substring(0,4) + ')');
							});
						})
					}
				});
		}
		
		// Submit movie to database Reference: https://bit.ly/2JrOEQG
		movieselect.on('select.editable-select', function(e, li) {
			// Reference: https://bit.ly/2HZ2Sc6
			var userselection = li.val();
			console.log("The user clicked " + userselection);
			
			// Notification to add
			new Noty({
				theme: 'mint',
				type: 'warning',
				text: '<div class="activity-item"><i class="far fa-clock"></i><div class="activity">Please wait while the movie is added to your list.</div> </div>',
				layout: 'topRight',
				open: 'animated bounceInRight',
				close: 'animated bounceOutRight',
				timeout: 3000,
				closeWith: ['click'],
				progressBar: true
			}).show();
			
			
			
			$.ajax({ 
					url: 'tmdbinterface.php',
					data: {addtolist: userselection},
					type: 'post',
					dataType: 'json',
					success: function(output2){
						console.log("The server returned: " + output2);
						appendtolist(output2);
						
						if (output2 != true) {
							new Noty({
								theme: 'mint',
								type: 'success',
								text: '<div class="activity-item"><i class="fas fa-check"></i><div class="activity">The movie was added successfully.</div> </div>',
								timeout: 3000,
								open: 'animated bounceInRight',
								close: 'animated bounceOutRight',
								progressBar: true
							}).on('onClose' , function() {
								//parent.location.reload(true);
								
							}).show();
							
							appendtolist();
							
						} else {
							new Noty({
								theme: 'mint',
								type: 'error',
								text: '<div class="activity-item"><i class="fas fa-exclamation-triangle"></i></i><div class="activity">You have already added this movie to your list!</div> </div>',
								timeout: 5000,
								open: 'animated bounceInRight',
								close: 'animated bounceOutRight',
								progressBar: true
							}).show();
						}
						
						
					}
				});
		});
		
		function appendtolist(jsondata) {
			if(('#empty-list').length != 0) {
				$('#empty-list').parent().remove();
				
			};
			
			var appendhtml = "<a class='list-group-item list-group-item-action flex-column align-items-start user-item' id='" + jsondata['movieID'] + "'>";
			appendhtml += "<div class='d-flex w-100 justify-content-between'>";
			appendhtml += "<div class='mb-1'>";
			appendhtml += "<h5>" + jsondata['title'] + "</h5>";
			appendhtml += "<p>" + jsondata['overview'] + "</p>";
			appendhtml += "<small class='description-gap'>Starring: " + jsondata['actors'] + "</small>";
			appendhtml += "<br><small>Year Released: " + jsondata['year_released'].substring(0, 4) + "</small>";
			appendhtml += "</div>";
			appendhtml += "<small style='text-align: right;'>a moment ago";	
			appendhtml += "<div class='poster-alignment'>";
			appendhtml += "<img class='poster-styling' src='https://image.tmdb.org/t/p/w300/" + jsondata['poster'] + "'>";
			appendhtml += "</div>";
			appendhtml += "</small>";
			appendhtml += "</div>";
			appendhtml += "</a>";

			$(".list-group").prepend(
				$(appendhtml).hide().fadeIn(1000)
			);
		}
		
		$(document).on('click', '.user-item', function(){
		  new Noty({
				theme: 'mint',
				type: 'warning',
				text: '<div class="activity-item"><i class="far fa-clock"></i><div class="activity">Please wait while the movie is removed.</div> </div>',
				layout: 'topRight',
				open: 'animated bounceInRight',
				close: 'animated bounceOutRight',
				timeout: 3000,
				closeWith: ['click'],
				progressBar: true
			}).show();
			
			$(this).fadeOut(1000);
			
			$.ajax({
				url: 'tmdbinterface.php',
				data: {removefromlist: this.id},
				type: 'post',
				dataType: 'json',
				success: function(output3){
					console.log(output3);
					if (output3){
						new Noty({
							theme: 'mint',
							type: 'success',
							text: '<div class="activity-item"><i class="fas fa-check"></i><div class="activity">The movie was removed successfully.</div> </div>',
							timeout: 3000,
							open: 'animated bounceInRight',
							close: 'animated bounceOutRight',
							progressBar: true
						}).on('onClose' , function() {
							//parent.location.reload(true);

						}).show();
					} else {
						new Noty({
							theme: 'mint',
							type: 'error',
							text: '<div class="activity-item"><i class="fas fa-exclamation-triangle"></i></i><div class="activity">We failed to remove the movie!</div> </div>',
							timeout: 5000,
							open: 'animated bounceInRight',
							close: 'animated bounceOutRight',
							progressBar: true
						}).show();
					}
				}
			});
		});
	});
</script>

</html>