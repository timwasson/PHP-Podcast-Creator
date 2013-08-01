$(document).ready(function(){

	// Local copy of jQuery selectors, for performance.
	var	my_jPlayer = $("#jquery_jplayer"),
		my_trackName = $("#jp_container .track-name"),
		my_playState = $("#jp_container .play-state");

	// Some options
	var	opt_play_first = false, // If true, will attempt to auto-play the default track on page loads. No effect on mobile devices, like iOS.
		opt_auto_play = true, // If true, when a track is selected, it will auto-play.
		opt_text_playing = "", // Text when playing
		opt_text_selected = ""; // Text when not playing

	// A flag to capture the first track
	var first_track = true;

	// Initialize the play state text
	my_playState.text(opt_text_selected);

	// Instance jPlayer
	my_jPlayer.jPlayer({
		ready: function () {
			$("#jp_container .track:first").click();
		},
		play: function(event) {
			my_playState.text(opt_text_playing);
		},
		pause: function(event) {
			my_playState.text(opt_text_selected);
		},
		ended: function(event) {
			my_playState.text(opt_text_selected);
		},
		swfPath: "/components/player",
		cssSelectorAncestor: "#jp_container",
		supplied: "mp3",
		wmode: "window"
	});

	// Create click handlers for the different tracks
	$(".track").click(function(e) {
		// Remove all the previously playing track nonsense.
		$(".now-playing").remove();
		$(".track.active").removeClass("active");
		
		// Add the speaker to this thing.
		$(this).prepend("<span class=\"badge now-playing\"><i class=\"icon-volume-up\"></i></span>");
		$(this).addClass("active");
		
		my_trackName.text($(this).text());
		my_jPlayer.jPlayer("setMedia", {
			mp3: $(this).attr("href")
		});
		if((opt_play_first && first_track) || (opt_auto_play && !first_track)) {
			my_jPlayer.jPlayer("play");
		}
		first_track = false;
		$(this).blur();
		return false;
	});
});