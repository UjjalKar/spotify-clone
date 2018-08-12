<?php  

	include "includes/includedFiles.php";

	if(isset($_GET['id'])) {

		$artistId = $_GET['id'];

	} else {
		header("Location: index.php");
	}

	$artist = new Artist($con, $artistId);

?>

<div class="entityInfo borderbottom">
	<div class="centerSection">
		<div class="artistInfo">
			<h1 class="artistName"><?php echo $artist->getName(); ?></h1>

			<div class="headerButtons">
				<button class="button green" onclick="playFirstSong()">Play</button>
			</div>
		</div>
	</div>
</div>
<div class="tracklistContainer borderbottom">
	<h1><span>&#x266A; </span>SONGS <span>&#x266A;</span></h1>
	<ul class="tracklist">

		<?php 
			$songIdArray = $artist->getSongIds();
			$i = 1;
			foreach ($songIdArray as $songId) {

				if($i > 5) {
					break;
				}

				$albumSong = new Song($con, $songId);

				$albumArtist = $albumSong->getArtist();
				?>

			<li class="tracklistRow">
				<div class="trackCount">
					<img src="assets/images/icons/play-white.svg" class="play" onclick="setTrack(<?php echo $albumSong->getId(); ?>, tempPlaylist, true)">
					<span class="trackNumber"><?php echo $i; ?></span>
				</div>

				<div class="trackInfo">
					<span class="trackName">

						<?php echo $albumSong->getTitle(); ?>

						<i> &#x266A;</i> 
					</span>
					<span class="artistName">
						<?php echo $albumArtist->getName(); ?>
					</span>
				</div>

				<div class="trackOptions">
					<img src="assets/images/icons/more.svg" class="optionButton">
				</div>

				<div class="trackDuration">
					<span class="duration">
						<?php echo $albumSong->getDuration(); ?>
					</span>
				</div>
			</li>


		<?php $i++;	
			}
		?>

		<script>
			
			var tempSongIds = '<?php echo json_encode($songIdArray); ?>';
			tempPlaylist = JSON.parse(tempSongIds);

		</script>

	</ul>
</div>

<div class="gridViewContainer">
	<h1>ALBUMS</h1>
	<?php  
		$albumQuery = mysqli_query($con, "SELECT * FROM albums WHERE artist='$artistId'");
		while($row = mysqli_fetch_array($albumQuery)) { ?>

				<div class="gridViewItem">
					<span role="link" tabindex="0" onclick="openPage('album.php?id=<?php echo $row['id'] ?>')">
						<img src="<?php echo $row['artworkPath'] ?>">

						<div class="gridViewInfo">
							<?php echo $row['title'] ?>
						</div>
						<div class="gridViewInfoHover">
							<h4><?php echo $row['title']; ?></h4>
						</div>
					</span>
				</div>
		

	<?php	}
	?>

</div>