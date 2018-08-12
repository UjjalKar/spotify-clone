<?php  
	
	$query = mysqli_query($con, "SELECT * FROM `songs` ORDER BY RAND() LIMIT 10 ");
	$resultArray = array();

	while($row = mysqli_fetch_array($query)) {
		array_push($resultArray, $row['id']);
	}

	$jsonArray = json_encode($resultArray);

?>

<script>
	

	$(document).ready(function() {

		var newPlaylist = <?php echo $jsonArray; ?>;
		audioElement = new Audio();
		setTrack(newPlaylist[0], newPlaylist, false);
		updateVolumeProgressBar(audioElement.audio);


		// prevent selecting highlight on page 

		$("#nowPlayingBarContainer").on("mousedown touchstart mousemove touchmove", function(e) {

			e.preventDefault();
		});


		// for drag bar 
		$(".playbackBar .progressBar").mousedown(function() {
			mouseDown = true;
		});

		$(".playbackBar .progressBar").mousemove(function(e) {
			if(mouseDown == true) {
				// Set time of song, depending on position of mouse.
				timeFromOffset(e, this);
			}
		});

		$(".playbackBar .progressBar").mouseup(function(e) {	
			timeFromOffset(e, this);
		});

		// for volume control
		$(".volumeBar .progressBar").mousedown(function() {
			mouseDown = true;
		});

		$(".volumeBar .progressBar").mousemove(function(e) {
			if(mouseDown == true) {
				// Set time of song, depending on position of mouse.
				var percentage = e.offsetX / $(this).width();
				if(percentage >= 0 && percentage <= 1) {	
					audioElement.audio.volume = percentage;
				}	
			}
		});

		$(".volumeBar .progressBar").mouseup(function(e) {	
			var percentage = e.offsetX / $(this).width();
			if(percentage >= 0 && percentage <= 1) {	
				audioElement.audio.volume = percentage;
			}	
		});


		$(document).mouseup(function() {
			mouseDown = false;
		});
	});
	

	function timeFromOffset(mouse, progressBar) {
		// $(".playbackBar .progressBar") this refers to $(progressBar)
		var percentage = mouse.offsetX / $(progressBar).width() * 100;
		var sec = audioElement.audio.duration * (percentage / 100);
		audioElement.setTime(sec);
	}

	function prevSong() {
		if(audioElement.audio.currentTime >= 3 || currentIndex == 0) {
			audioElement.setTime(0);
		} else {
			currentIndex--;
			setTrack(currentPlaylist[currentIndex], currentPlaylist, true);
		}
	}


	function nextSong() {

		if(repeat == true) {
			audioElement.setTime(0);
			playSong();
			return;
		}

		if(currentIndex == currentPlaylist.length - 1) {
			currentIndex = 0;
		} else {
			currentIndex++;
		}

		var trackToPlay = shuffle ? shufflePlaylist[currentIndex] : currentPlaylist[currentIndex];
		setTrack(trackToPlay, currentPlaylist, true);
	}

	function setRepeat() {
		repeat = !repeat;
		var imageName = repeat ? "repeat-active.svg" : "repeat.svg";
		$(".controlButton.repeat img").attr("src", "assets/images/icons/" + imageName);
	}

	function setMute() {
		audioElement.audio.muted = !audioElement.audio.muted;
		var imageName = audioElement.audio.muted ? "volume-mute.svg" : "volume.svg";
		$(".controlButton.volume img").attr("src", "assets/images/icons/" + imageName);
	}

	function setSuffle() {
		shuffle = !shuffle;
		var imageName = shuffle ? "shuffle-active.svg" : "shuffle.svg";
		$(".controlButton.shuffle img").attr("src", "assets/images/icons/" + imageName);

		if(shuffle == true) {
			// Random playlist
			shuffleArray(shufflePlaylist);
			console.log(audioElement.currentlyPlaying.id);
			currentIndex = shufflePlaylist.indexOf(audioElement.currentlyPlaying.id);


		} else {
			console.log(audioElement.currentlyPlaying.id);

			currentIndex = currentPlaylist.indexOf(audioElement.currentlyPlaying.id);
		}
	}

	function shuffleArray(a) {
	    var j, x, i;
	    for (i = a.length - 1; i > 0; i--) {
	        j = Math.floor(Math.random() * (i + 1));
	        x = a[i];
	        a[i] = a[j];
	        a[j] = x;
	    }
	    return a;
}



	function setTrack(trackId, newPlaylist, play) {

		if(newPlaylist != currentPlaylist) {
			currentPlaylist = newPlaylist;

			// slice() copy of a array
			shufflePlaylist = currentPlaylist.slice();
			shuffleArray(shufflePlaylist);
		}

		if(shuffle == true) {
			currentIndex = shufflePlaylist.indexOf(trackId);
		} else {
			currentIndex = currentPlaylist.indexOf(trackId);
		}

		pauseSong();
		
		// GETTING SONG DETAILS NAME USING AJAX CALL
		$.post("includes/handlers/ajax/getSongJson.php", { songId: trackId }, function(data) {

			var track = JSON.parse(data);

			$(".trackName span").text(track.title);

			// GETTING ARTIST NAME USING AJAX CALL
			$.post("includes/handlers/ajax/getArtistJson.php", { artistId: track.artist }, function(data) {

				var artist = JSON.parse(data);

				$(".artistName span").text(artist.name);
				$(".artistName span").attr("onclick", "openPage('artist.php?id=" + artist.id + "')");
			});

			// GETTING ALBUM NAME USING AJAX CALL
			$.post("includes/handlers/ajax/getAlbumJson.php", { albumId: track.album }, function(data) {

				var album = JSON.parse(data);
				$(".albumLink img").attr("src", album.artworkPath);
				$(".albumLink img").attr("onclick", "openPage('album.php?id=" + album.id + "')");
				$(".trackName span").attr("onclick", "openPage('album.php?id=" + album.id + "')");

			});

			audioElement.setTrack(track);

			if(play == true) {
				playSong();
			}	
		});

		
	}

	function playSong() {

		if(audioElement.audio.currentTime == 0) {
			$.post("includes/handlers/ajax/updatePlays.php", { songId: audioElement.currentlyPlaying.id });
		}

		$(".controlButton.play").hide();
		$(".controlButton.pause").show();
		audioElement.play();
	}

	function pauseSong() {

		$(".controlButton.play").show();
		$(".controlButton.pause").hide();
		audioElement.pause();
	}


</script>


<div id="nowPlayingBarContainer">
	<div id="nowPlayingBar">

		<div id="nowPlayingLeft">
			<div class="content">
				<span class="albumLink">
					<img role="link" tabindex="0" class="albumArtwork" src="">
				</span>

				<div class="trackInfo">
					<span class="trackName">
						<span role="link" tabindex="0"></span>
					</span>

					<span class="artistName">
						<span role="link" tabindex="0"></span>
					</span>
				</div>

			</div>
		</div>

		<div id="nowPlayingCenter">
			<div class="content playerControls">
				<div class="buttons">

					<button class="controlButton shuffle" title="Shuffle" alt="shuffle" onclick="setSuffle()">
						<img src="assets/images/icons/shuffle.svg">
					</button>

					<button class="controlButton previous" title="Previous" alt="shuffle" onclick="prevSong()">
						<img src="assets/images/icons/previous.svg">
					</button>

					<button class="controlButton play" title="Shuffle" alt="Play" onclick="playSong()">
						<img src="assets/images/icons/play.svg">
					</button>

					<button class="controlButton pause" title="Pause" alt="Pause" style="display: none;" onclick="pauseSong()">
						<img src="assets/images/icons/pause.svg">
					</button>

					<button class="controlButton next" title="Next" alt="Next" onclick="nextSong()">
						<img src="assets/images/icons/next.svg">
					</button>

					<button class="controlButton repeat" title="Repeat" alt="Repeat" onclick="setRepeat()">
						<img src="assets/images/icons/repeat.svg">
					</button>

				</div>

				<div class="playbackBar">

					<span class="progressTime current">0.00</span>

					<div class="progressBar">
						<div class="progressBarBg">
							<div class="progress"></div>
						</div>
					</div>

					<span class="progressTime remaining">0.00</span>

				</div>

			</div>
		</div>

		<div id="nowPlayingRight">
			
			<div class="volumeBar">
				
				<button class="controlButton volume" title="Volume button" onclick="setMute()">
					<img src="assets/images/icons/volume.svg">
				</button>

				<div class="progressBar">
					<div class="progressBarBg">
						<div class="progress"></div>
					</div>
				</div>

			</div>

		</div>
	</div>
</div>