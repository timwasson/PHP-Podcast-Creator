// To convert the seconds into human readable h:m:s.
function seconds2time(seconds) {
  seconds = Math.round(seconds);
  var h, m, s, result='';
  // HOURs
  h = Math.floor(seconds/3600);
  seconds -= h*3600;
  if(h){
    result = h<10 ? '0'+h+':' : h+':';
  }
  // MINUTEs
  m = Math.floor(seconds/60);
  seconds -= m*60;
  result += m<10 ? '0'+m+':' : m+':';
  // SECONDs
  s=seconds%60;
  result += s<10 ? '0'+s : s;
  return result;
}

$(document).ready(function() {
  
  // First get the hash
  var ephash = window.location.hash.replace("/","").replace("#","").replace("/","");
  
  // Set some variables for performance
  var audioEl = $("#ep_audio");
  var playButton = $(".jp-play");
  var pauseButton = $(".jp-pause");
  var isPlaying = false;

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
      playitem["pubdate"] = val["pubdate"];
      
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
      
      $("ul#ep-list").append( "<li id='" + key + "' data-epid=\""+eplongid+"\" class=\"track\"><img src=\"/themes/appview/imgszr/resize.php?image=/images/"+shortpath+"&width=250&height=250\" /><p>" + val['title'] + "<br /><span class=\"pubdate\">" + val['pubdate'] + "</span></p></li>" );
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
    
    $(".ep_info h1").text(items[epid].title);
    // This is kind of a hack but it adds the background image to the H1
    var shortpath = items[epid].image.split("/");
      shortpath = shortpath[shortpath.length - 1];
      
    $("#ep_bg img").attr("src","/themes/appview/imgszr/resize.php?image=/images/"+shortpath+"&width=250&height=250");
    
    $(".ep_info #ep_desc").html(items[epid].description);
    $(".ep_info #pubdate").html("Posted: " + items[epid].pubdate);
    $(".ep_info a[data-dtype=download]").attr("href",items[epid].audio);
    
    $(".ep_info a[href=#play]").click(function(e){
      e.preventDefault();
  		// update the player to play the file, fool.
      $(".track-name").text(items[epid].title);
  		audioEl.attr("src", items[epid].audio);
  		
  		// Set duration and time only once loaded.
      audioEl[0].onloadedmetadata = function() {
        $(".jp-current-time").text(seconds2time(audioEl[0].currentTime));
        $(".jp-duration").text(seconds2time(audioEl[0].duration));
      };
      
  		audioEl[0].play();
  		playButton.hide();
  		pauseButton.show();
    });
    if(isPlaying == false) {
      $(".track-name").text(items[epid].title);
  		audioEl.attr("src", items[epid].audio);
  		
  		audioEl[0].onloadedmetadata = function() {
        console.log(audioEl[0].duration);
        $(".jp-current-time").text(seconds2time(audioEl[0].currentTime));
        $(".jp-duration").text(seconds2time(audioEl[0].duration));
      };
        
  		isPlaying = true;
    }
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
  
  audioEl.bind('timeupdate', function() {
    var perDone = (audioEl[0].currentTime / audioEl[0].duration) * 100;
    $(".jp-current-time").text(seconds2time(audioEl[0].currentTime));
    $("#playProc").val(perDone);
  });
  
  $("#playProc").bind("mousedown touchstart", function() {
    audioEl[0].pause();
  });
  
  $("#playProc").bind("mouseup touchend", function() {
    audioEl[0].play();
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
   