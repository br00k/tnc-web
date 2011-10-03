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
 * @revision   $Id: coverage.js 598 2011-09-15 20:55:32Z visser $
 */
$(document).ready(function() {
	$('#help').css('display', 'none');
	$('#nav-section').css('height', '');

	$('#container .helpbutton').click(function(e){
		e.preventDefault();
		$("#help").toggle('blind',{},200, function(){
		});
	});

    $(".column").sortable({
    	connectWith: '.column'
    });

    $(".portlet").addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all")
    	.find(".portlet-header")
    		.addClass("ui-widget-header ui-corner-all")
    		.prepend('<span class="ui-icon ui-icon-circle-minus"></span>')
    		.end()
    	.find(".portlet-content");

    $(".portlet-header .ui-icon").click(function() {
    	$(this).toggleClass("ui-icon-circle-plus").toggleClass("ui-icon-circle-minus");
    	$(this).parents(".portlet:first").find(".portlet-content").toggle();
    });

    $(".column").disableSelection();

    $.ajaxSetup ({
        cache: false
    });
    var ajax_load = "<img src='gfx/loader.gif' alt='loading...' />";

    var loadUrl = "/web/media/coverage/format/json";
    
	// Ajax content loader
	var ajaxRefresh = function(){
	    $("#ajaxcont").html(ajax_load);
	    $.ajax({
	        url: loadUrl,
        	dataType: 'json',
        	type: "post",
	        data: {action: "all"},
	        success: function(responseText){
	        	if (responseText == 'nothing') {
	        		return;
	        	}
	        	var old = $('ul#blog a').attr('href');
	        	$('#content-blogs').html(responseText.blog);
	        	if ($('ul#blog a').attr('href') != old) {
	        		$('#cont-blogs .portlet-header').effect("highlight", {}, 5000);
	        	}
	        	var old = $('ul#tweets div.tweet-text:first span').text();
        		$('#content-twitter').html(responseText.twitter);
	        	if ($('ul#tweets div.tweet-text:first span').text() != old) {
	        		$('#cont-tweets .portlet-header').effect("highlight", {}, 5000);
	        	}
	        	var old = $('ul#flickr img:first').attr('src');
        		$('#content-flickr').html(responseText.flickr);
  	        	if ($('ul#flickr img:first').attr('src') != old) {
	        		$('#cont-pictures .portlet-header').effect("highlight", {}, 5000);
	        	}
 	        	var old = $('ul#youtube img:first').attr('src');
        		$('#content-youtube').html(responseText.youtube);
  	        	if ($('ul#youtube img:first').attr('src') != old) {
	        		$('#cont-video .portlet-header').effect("highlight", {}, 5000);
	        	}
        		$('#content-external').html(responseText.morepics);
	        	tweetHover();
	        	lightbox();
	        	tweetPager();
	        }
	    });
	};

	// refresh content every now and then
	var setTimer = function() {	
		//ajaxRefresh();
    	intervalId = setInterval(ajaxRefresh, 60000);
    }

	var tweetHover = function(){
	    // Tweet hover
	    $('#tweets li').mouseenter(function(e){
	    	$(this).find("span.viewlink").toggle();
	    }).mouseleave(function(){
	    	$(this).find("span.viewlink").toggle();
	    });
    };
    
    var lightbox = function(){
		$("#flickr:first a[rel^='prettyPhoto']").prettyPhoto({animationSpeed:'fast',theme:'dark_rounded',slideshow:2000,showTitle:false});
		$("#youtube:first a[rel^='prettyPhoto']").prettyPhoto({animationSpeed:'fast',theme:'dark_rounded',slideshow:2000,showTitle:false});    
    }
    
    var genericPager = function(){
    	$('div.paginationControl a').live('click', function(e){
    		e.preventDefault();    		
    		$(this).empty().html('<img src="/gfx/icons/ajax-loader.gif" />');
    		$.ajax({
    			url:"index.php",
    			type: "post",
    			dataType: "json",
    			data: {action:'page', params:$(this).attr('href')},
    			success: function(responseText){
    				$(e.target).parent().parent().html(responseText);
    			}
    		});
    	});
    };
    
    genericPager();
    lightbox();
    tweetHover();
    setTimer();
});
