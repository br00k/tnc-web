$(function(){
	$('.grid tr.collapsible')
		.css("cursor","pointer")
		.attr("title","Click to expand/collapse")
		.click(function(){
			$(this).next('tr.extra').toggle();	
		})
	$('.grid tr.extra').hide();	
});