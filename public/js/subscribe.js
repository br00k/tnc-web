/**
 * CORE Conference Manager
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.terena.org/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to webmaster@terena.org so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2011 TERENA (http://www.terena.org)
 * @license    http://www.terena.org/license/new-bsd     New BSD License
 * @revision   $Id: subscribe.js 598 2011-09-15 20:55:32Z visser $
 */
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