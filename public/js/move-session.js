$(function() {
    $( "#programme input.mover" ).click(function(val){
    	if ($('.mover').filter(":checked").length == 2) {
    		$('#programme').submit();
    	}
    });
});