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
 * @revision   $Id: pres-sort.js 598 2011-09-15 20:55:32Z visser $
 */

/**
 * Apply sortable behaviour to presentation list
 * The new list order will be saved with ajax
 * During the saving the sortable will be disabled.
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
$(function() {
    $( "#sortable" ).sortable({
    	placeholder: 'ui-state-highlight',
    	cursor: 'move',
    	stop: function(event, ui) {
    		$(this).sortable('disable');
			order = [];
			$('#sortable').children('li').each(function(idx, elm) {
				order.push(elm.id.split('_')[1]);
			});
			var that = $(this);
			$.get(
				'order/format/json',
				{'session_id': $('#session_id').val(), 'order': order},
				function(data){
					$('#sortable').effect("highlight", {'color':'#B3EBB0'}, 2500, function(){						
    					that.sortable('enable');
					});
				}
			);
		}
    });
    $( "#sortable" ).disableSelection();
});