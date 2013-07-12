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
	
	$("#uploadform").submit(function() {
		$("#userfile, #title, #description").unwrap();
		
		CheckEmpEr($("#userfile"));
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
});