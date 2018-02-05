
$(function() {
	$("div.menulink").hover(
		function() { $(this).addClass("linkover"); },
		function() { $(this).removeClass("linkover"); }
	);
	
	$("table.zebra")
		.find("tr:even")
		.css("background-color","#eeeeee");
		
});

