// Upload Progress
/// Based on work by DannYo.(http://stackoverflow.com/questions/166221/how-can-i-upload-files-asynchronously-with-jquery) (C) 2012 David Moreno. BSD Licensed.


$.fn.uploadProgress = function(options){
	$(this).each(function(){
		var form=$(this)
		var action=form.attr('action') || window.location.href
		
		var setDownloadedContents = function(contents){ 
			if (contents.responseText)
				contents=contents.responseText
			if (!opts.onBeforeComplete(contents))
				return;
			try{ // option 1, try to replace contents
				$('body').html($('<body>').html(contents))
				// Now set the new URL with tricks.. FIXME
			}
			catch(e){ // option 2, reload page with GET. Sorry, two full renderings lost.
				action=action.split('#')[0]
				window.location_=action
			}
		}
		
		
		var opts
		opts = jQuery.extend({
			onProgress:function(p){}, // function to be called with ammount of progress done, from 0 to 1.
			onBeforeSend:function(){},
			onBeforeComplete:function(){ return true; }, // returns whether to replace/reload content or not
			onComplete:setDownloadedContents,
			onError:setDownloadedContents,
			onStart:function(){}
		},options)
		opts.form=form
		
		form.submit(function(ev){
			if (ev.isDefaultPrevented())
				return false
			if (opts.form.find('[type=file]').filter(function(){ return $(this).val()!='' }).length==0)
				return true // go on normally, no files to upload.
			opts.onStart()
			var formData = new FormData(form[0]);
			$.ajax({
				url: action, 
				type: 'POST',
				xhr: function() {  // custom xhr
					myXhr = $.ajaxSettings.xhr();
					if(myXhr.upload){ // check if upload property exists
						myXhr.upload.addEventListener('progress',function(ev){
							opts.onProgress(Math.round(ev.loaded * 10000.0 / ev.total)/100.0, ev)
						}, false); // for handling the progress of the upload
					}
					return myXhr;
				},
				//Ajax events
				beforeSend: opts.onBeforeSend,
				success: opts.onComplete,
				error: opts.onError,
				// Form data
				data: formData,
				//Options to tell JQuery not to process data or worry about content-type
				cache: false,
				contentType: false,
				processData: false
			})
			ev.preventDefault()
		})
	})
}
// End of David Moreno's upload progress code.

// Custom Functions below.


$(function() {
	// Count the description field and update the error if applicable.
	$("#description").keyup(function() {
		var chars = 255 - $(this).val().length;
		$("#countdown").text(chars);
		if (chars <= 20 && chars != 0) {
			$(".cdown").parent("div").removeClass().addClass("alert alert-warning");
		} else if (chars <= 0) {
			$(".cdown").parent("div").removeClass().addClass("alert alert-error");
		} else {
			$(".cdown").parent("div").removeClass().addClass("alert alert-info");
		}
	});
	
	var error = 0;
	
	// Show additional information on the Add Podcast page
	//First slide up the panel
	$("#main").slideUp();
	$('#moreinfo').change(function(){
		//alert(this.checked);
		if(this.checked == true) {
			$("#main").slideDown();
		} else {
			$("#main").slideUp();
		}
	});
	
	// Count the iTunes keywords. Max of 12.
	$("#keywords").keyup(function(){
		commas = $(this).val().match(/,/ig);
		$("#wordcount").text(commas.length + 1);
	});
	
	// Very basic Error Checking on the New Podcast Episode page
	function CheckEmpEr(inputid) {
		if (inputid.val().length == 0) {
			$(inputid).wrap("<div class=\"control-group alert alert-error\">");
			error = 1;
		}
	}
	
	//Progress Bar
	$("#uploadform").uploadProgress( { onProgress:progressupdate } );
	
	function progressupdate(p){
		pr = Math.round(p);
		$("#fileProgress .bar").css({ "width": pr+"%" }).text(pr+"%");
		//console.log("progress"+p);
	}
	// End Progress bar
	
	// Simple error checking 
	$("#uploadform").submit(function() {
		$("#userfile, #title, #description").unwrap();
		error = 0;
		
		// Check for the file input only if the FTP file is empty.
		if($("#ftpfile").val().length == 0) {
			CheckEmpEr($("#userfile"));
		}
		
		CheckEmpEr($("#title"));
		CheckEmpEr($("#description"));
		
		if(error == 1) {
			alert("Looks like you've got some errors you need to fix.");
			return false;
		}
	});

	
	//Update the deletion URL
	$(".delep").on("click",function() {
		url2del = $(this).data("delurl");
		$("#delurl").attr("href",url2del);
	});
	
	//Insert the FTP uploaded files into the site.
	$(".ftpupload").on("click",function(e) {
		e.preventDefault();
		$("#userfile").hide();
		$("#ftpfile").attr("value",$(this).data("ftpurl")).attr("type","input").addClass("uneditable-input");
		$('#uploadFiles').modal('hide');
	});
});