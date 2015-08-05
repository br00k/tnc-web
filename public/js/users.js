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
 * @revision   $Id: users.js 598 2011-09-15 20:55:32Z visser $
 */
$(function(){
	$('.grid tr.collapsible')
		.css("cursor","pointer")
		.attr("title","Click to expand/collapse")
		.click(function(){
			$(this).next('tr.extra').toggle();	
		})
	$('.grid tr.extra').hide();	
	
	var searchElm = $('#search');	
	var defaultVal = searchElm.attr('title');
	searchElm.focus(function(){
		if ($(this).val() == defaultVal) {
			$(this).removeClass('active').val('');
		}
	});
	searchElm.blur(function(){
		if ($(this).val() == ''){
			$(this).addClass('active').val(defaultVal);
		}
	});
	searchElm.blur().addClass('active');
});