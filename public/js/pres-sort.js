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