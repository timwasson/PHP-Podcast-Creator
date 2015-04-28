$(document).ready(function() {
  
  // First get the hash
  var ephash = window.location.hash.replace("/","").replace("#","").replace("/","");
  
  // Set some variables for performance
  var audioEl = $("#ep_audio");
  var playButton = $(".jp-play");
  var pauseButton = $(".jp-pause");

  var items = [];
  // Handler for .ready() called.
  $.getJSON( "/feed.json", function( data ) {      
    // Create the big data object for all episodes.
    $.each( data['item'], function( key, val ) {
      
      var playitem = {};
      playitem["title"] = val['title'];
      playitem["audio"] = val['enclosure'];
      playitem["image"] = val['image'];
      playitem["description"] = val["description"];
      
      items.push(playitem);
    });
  }).done(function() {
    // Create the list.
    $.each(items, function(key, val){
      //console.log(val['image']);
      var tempimg = val['image'];
      var shortpath = tempimg.split("/");
      shortpath = shortpath[shortpath.length - 1];
      
      // make the ID from the MP3
      eplongid = val['audio'].split("/");
      eplongid = eplongid[eplongid.length - 1];
      eplongid = eplongid.split(".");
      eplongid = eplongid[0];
      
      $("ul#ep-list").append( "<li id='" + key + "' data-epid=\""+eplongid+"\" class=\"track\"><img src=\"/themes/appview/imgszr/resize.php?image=/images/"+shortpath+"&width=250&height=250\" /><p>" + val['title'] + "</p></li>" );
    });
    
    // Load the first episode. Feels like a hack. Works. So whatever.
    // Check for a hash to select the correct episode
    if(ephash.length > 0) {
      $("ul#ep-list li[data-epid="+ephash+"]").click();
  		
    } else if($(window).width() > 600) {
      // Check for a hash to select the correct episode
      $("ul#ep-list li:first-child").click();
    }
  }); 
  
  $("#ep-list").delegate( "li", "click", function() {
    radioOn = "off";
    $("#ep-list .active").removeClass("active");
    $(this).addClass("active");
    
    var epid = $(this).attr("id");
    
    window.location.hash = "/"+$(this).data("epid")+"/";
    
    if($(window).width() < 600) {
      $(".ep_info").animate({"left":0},50);
      $(".episodes").animate({"left":-80},50);
    }
    
    $(".ep_info h1 span").text(items[epid].title);
    // This is kind of a hack but it adds the background image to the H1
    var shortpath = items[epid].image.split("/");
      shortpath = shortpath[shortpath.length - 1];
      
    $("#ep_bg img").attr("src","/themes/appview/imgszr/resize.php?image=/images/"+shortpath+"&width=250&height=250");
    
    $(".ep_info #ep_desc").html(items[epid].description);
    $(".ep_info a[data-dtype=download]").attr("href",items[epid].audio);
    
    $(".ep_info a[href=#play]").click(function(e){
      e.preventDefault();
  		// update the player to play the file, fool.
      $(".track-name").text(items[epid].title);
  		audioEl.attr("src", items[epid].audio);
  		
  		// Set duration and time only once loaded.
      audioEl[0].onloadedmetadata = function() {
        console.log(audioEl[0].duration);
        $(".jp-current-time").text(audioEl[0].currentTime);
        $(".jp-duration").text(audioEl[0].duration);
      };
      
      audioEl.bind('timeupdate', function() {
        var perDone = (audioEl[0].currentTime / audioEl[0].duration) * 100;
        $(".jp-current-time").text(audioEl[0].currentTime);
        $("#playProc").val(perDone);
        console.log("Time updating: "+ perDone);
      });
      
  		audioEl[0].play();
  		playButton.hide();
  		pauseButton.show();
    });
  });
  
  // Hit the pause button
  pauseButton.on("click", function() {
    audioEl[0].pause();
    playButton.show();
    pauseButton.hide();
  });
  
  // Hit the Play button
  playButton.on("click", function() {
    audioEl[0].play();
    playButton.hide();
    pauseButton.show();
  });
  
  $("#playProc").bind("mousedown touchstart", function() {
    audioEl[0].pause();
  });
  
  $("#playProc").on("change",function() {
    var skipTo = parseFloat(audioEl[0].duration) * ($(this).val() / 100);
  
    audioEl[0].currentTime = skipTo;
    audioEl[0].play();
  });
  
  $("#main-menu").click(function() {
    $(".episodes").animate({"left":"80px"},50);
    // Move the menu into focus and scroll it to the top.
    $(".menu").animate({"left":0},50);
    $(".menu section").scrollTop(0);
  });
  $(".ep_info h2 i").click(function() {
    $(".ep_info").animate({"left": "100%"});
    $(".episodes").animate({"left":0},50);
  });
  $(".eplink").click(function(e) {
    e.preventDefault();
    if($(window).width() < 600) {
      $(".menu").animate({"left":"-100%"},50);
      $(".episodes").animate({"left":0},50);
    }
  });
});
   