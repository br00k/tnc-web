$(function() {
	if ( $('#sessionspeakers tr').length > 1 ) {

		interval = setInterval(function(){
			var cur = $('#sessionspeakers tr.active');
			if (cur.next().length == 0) {
				$('#sessionspeakers tr').first().addClass('active').css('display', 'table-row');
			} else {
				cur.next().addClass('active').css('display', 'table-row');
			}
			cur.removeClass('active').css('display', 'none');
		}, 10000);
	}
});