<?php

	include "includes/includedFiles.php";

	if(isset($_GET['term'])) {
		$term = urldecode($_GET['term']);
	} else {
		$term = "";
	}
?>

<div class="searchContainer">
	<h4>Search for an artist, album or song</h4>
	<input type="text" class="searchInput" value="<?php echo $term; ?>" autocomplete="off" placeholder="Start Typing....." onfocus="this.value = this.value" onkeyup="search(this.value)">
</div>

<script>

	$(".searchInput").focus();
/*
	var beforeload = (new Date()).getTime();


	function getPageLoadTime(){
				//calculate the current time in afterload
				var afterload = (new Date()).getTime();
				// now use the beforeload and afterload to calculate the seconds
				seconds = (afterload-beforeload) / 1000;
				// Place the seconds in the innerHTML to show the results
				// $("#load_time").text('Page load time ::  ' + seconds + ' sec(s).');
				return seconds;
	}

window.onload = getPageLoadTime;
*/



function search(str) {
	var timer;
	var str = str;

	clearTimeout();
	timer = setTimeout(function() {
		openPage('search.php?term=' + str);
	});
}


	// $(function() {
	// 	var timer;
	//
	// 	$(".searchInput").keyup(function() {
	// 		clearTimeout(timer);
	//
	// 		timer = setTimeout(function() {
	// 			var val = $(".searchInput").val();
	// 			openPage("search.php?term=" + val);
	//
	// 		}, 2000);
	// 	});
	// });




</script>
