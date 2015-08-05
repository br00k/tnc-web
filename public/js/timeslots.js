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