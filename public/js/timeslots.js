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
 * @revision   $Id: timeslots.js 598 2011-09-15 20:55:32Z visser $
 */


/**
 * This script is responsible for dynamically adding timeslot form fields.
 *
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */

$(document).ready(function() {
	$('li a.delete').click(function(){
		$(this).parents('li').remove();
	});
	$('#add').click(function(){
		// clone element
		var origElm = $('li.timeslot:last');
		var cloneElm = origElm.clone();
		origElm.removeClass('hidden');
		
		var rand = Math.floor(Math.random()*11);
		// reset value and increment timeslot number in name attribute
		$(cloneElm).children('span')
			.children('input, select')
			.val('')
			.attr("name", function(){
		    	return this.name.replace(/timeslot_(new)?(\d+)/, function ($0, $1, $2) {
		        	return "timeslot_new" + (+$2 + rand);
		    	});
			});

		// insert element before add button
		$('ol').find('li.button:first').before(cloneElm);
	});
});