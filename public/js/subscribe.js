$(function() {
	$('table.schedule a.subscriber').click(function (event) {
        event.preventDefault();
        var that = $(this);
        $.get(
        	that.attr('href'),
            function (data, status) {
                if (status == 'success') {
                	if (that.hasClass('subscribeon')) {
                		that.removeClass('subscribeon');
                		that.addClass('subscribe');
                		that.attr('href', that.attr('href').replace(/\/unsubscribe/, '/subscribe'));
                		
                	} else {                		
                		that.removeClass('subscribe');
                		that.addClass('subscribeon');
                		that.attr('href', that.attr('href').replace(/\/subscribe/, '/unsubscribe'));
                	}
                }
            }
        );
    });
});