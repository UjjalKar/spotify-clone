var currentPlaylist = [];
var shufflePlaylist = [];
var tempPlaylist = [];
var audioElement;
var mouseDown = false;
var currentIndex = 0;
var repeat = false;
var shuffle = false;
var userLoggedIn;


function openPage(url) {

	if(url.indexOf("?") == -1) {
		url = url + "?";
	}

	var encodedUrl = encodeURI(url + "&userLoggedIn=" + userLoggedIn);
	console.log(encodedUrl);
	$("#mainContent").load(encodedUrl);
	$("body").scrollTop(0);
	history.pushState(null, null, url);
}


function formatTime(sec) {
	var time = Math.round(sec);
	var min = Math.floor(time / 60); // Rounds Down.
	var sec = time - (min * 60);

	var extraZero;
	if(sec < 10) {
		extraZero = "0";
	} else {
		extraZero = "";
	}

	// var extraZero = (sec < 10) ? "0" : "";

	return min + ":" + extraZero + sec;
}

function updateTimeProgressBar(audio) {
	$(".progressTime.current").text(formatTime(audio.currentTime));
	$(".progressTime.remaining").text(formatTime(audio.duration - audio.currentTime));

	var progress = (audio.currentTime / audio.duration) * 100;
	$(".playbackBar .progress").css("width", progress + "%");
}

function updateVolumeProgressBar(audio) {
	var volume = audio.volume * 100;
	$(".volumeBar .progress").css("width", volume + "%");
}

function playFirstSong() {
	setTrack(tempPlaylist[0], tempPlaylist, true);
}

function Audio() {

	// javascript class property
	this.currentlyPlaying;
	this.audio = document.createElement('audio');

	this.audio.addEventListener("ended", function() {
		nextSong();
	});

	// this event add the total song lenth/duration
	this.audio.addEventListener("canplay", function() {

		// "this" refers to the objects that the event was called on
		var duration = formatTime(this.duration);
		$(".progressTime.remaining").text(duration);

	});

	// this event update the remaing time.
	this.audio.addEventListener("timeupdate", function() {
		if(this.duration) {
			updateTimeProgressBar(this);
		}
	});

	this.audio.addEventListener("volumechange", function() {
		updateVolumeProgressBar(this);
	});

    // javascript class methods/functions
	this.setTrack = function(track) {
		this.currentlyPlaying = track;
		this.audio.src = track.path;
	}

	this.play = function() {
		this.audio.play();
	}

	this.pause = function() {
		this.audio.pause();
	}

	this.setTime = function(sec) {
		this.audio.currentTime = sec;
	}
}
