$(function() {
    $('#toggler').click(function (event) {
        event.preventDefault();
		$('#myreviewstodo li a.inactive').parent().toggle();
    });
});