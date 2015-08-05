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
 * @revision   $Id: terena.js 598 2011-09-15 20:55:32Z visser $
 */
$(function() {
var counter = 1;

// add newsticker
$.ajax({
	url: "/web/index/announcement/format/json",
	context: document.body,
	cache: false,
	type: 'post',
	dataType: 'json',
	success: function(data){
		$('#newsticker .news_text').html(data.announcements[0].header);
		$('#newsticker .news_date').html(data.announcements[0].created_at);
		if (data.announcements.length > 1) {
			interval = setInterval(function(){
				$('#newsticker .news_text').html(data.announcements[counter].header);
				$('#newsticker .news_date').html(data.announcements[counter].created_at);
				if (counter+1 == data.announcements.length) {
					counter = 0;
				} else {
					counter++;
				}
			}, 10000);
		}
	}
});

// add rating widgets on schedule page only if feedback toggler
// is available and selected
if ($('li#feedbacktoggler a.feedbackon').length>0) {

	// callback
	function set_votes(widget) {
		var rating = $(widget).data('fsr').rating;

		$(widget).find('.star_' + rating).prevAll().andSelf().addClass('ratings_vote');
		$(widget).find('.star_' + rating).nextAll().removeClass('ratings_vote');
    }

	// define hover states - show/hide stars
	$('#programme table.schedule td .ratings_stars').hover(
	    // Handles the mouseover
	    function() {
	    	if ( $(this).hasClass('delete') ) {
				$(this).prevAll().removeClass('ratings_vote');
	    	} else {
	        	$(this).prevAll().andSelf().addClass('ratings_over');
	        	$(this).nextAll().removeClass('ratings_vote');
	        }
	    },
	    // Handles the mouseout
	    function() {
	        $(this).prevAll().andSelf().removeClass('ratings_over');
	        set_votes($(this).parent());
	    }
	);

	// get default ratings for all presentations
	$.post(
	    "/core/feedback/ratings/format/json",
	    function(data) {
			$.each(data.defaults, function(i, val) {
				var widget = $('#programme table.schedule #r'+i);
				// If DOM node is found, link rating data to DOM object
				if (widget.length > 0) {
					$(widget).data('fsr', data.defaults[i]);
					set_votes(widget);
				}
			});
	    },
	    'json'
	);

	// onclick handler (to perform actual voting)
	$('#programme table.schedule td .ratings_stars').bind('click', function() {
	    var star = this;
	    var widget = $(this).parent();

	    var clicked_data = {
	        feedback: $(star).attr('id')
	    };
	    $.post(
	        "/core/feedback/ratepres/format/json",
	        clicked_data,
	        function(INFO) {
	            widget.data('fsr', INFO);
	            set_votes(widget);
	            // animate widget
	            widget.fadeTo("fast", 0.1, function(){
            		widget.fadeTo("fast", 1, function(){
            			// remove hover states (if user is still hovering over star when done voting)
            			widget.children().prevAll().removeClass('ratings_over')
            		});
            	});
	        },
	        'json'
	    );
	});
}

// add onchange handler to snapshots day dropdown
if ($('#mediadropdown').length > 0) {
    $('#mediadropdown').change(function(){
    	$(this).parents('#mediaselect').submit();
    });
}

// add live and archived video stream
if ($('div#streams').length>0) {

// toggler for hi/lo/mo quality
$('#streamquality').click(function(){
	$('#streamquality').submit();
});

var active = $('#activestream').text();
var quality = $('#quality').text();
var type = ($('div#streams').hasClass('archive')) ? 'archive' : 'live';
var file = 'stream'+active;
var file = 'mp4:11647267-f3efd410d735fad9612b.mbr.mp4?a682f8a5050d0f22ff2e7f3227392f6b2eb85c58';

if (active) {
	var setup = {
	  'id': 'tncstreamer',
	  'width': '537',
	  'height': '326',
	  'autostart':true,
	  'controlbar':'bottom',
	  'provider': 'rtmp',
	  'file': file,
	  'streamer': 'rtmp://live.visualplatform.net/live/',
	  //'streamer': 'rtmp://nordunetlivefs.fplive.net/nordunetlive-'+type+'/',
	  'modes': [
	  	  {type: 'flash', src: '/js/player.swf'},
	      {type: 'html5', config: {
		       'file': 'http://mps-ios-live.nordu.net:1935/'+type+'/'+file+'.sdp/playlist.m3u8',
	         'provider': 'http'
	        }
	      }
	  ]
	};
	if (type == 'archive') {	
		var mapping = {
			'1A': "http://youtu.be/5IlGRSlfoJs"
		};	
		var setup = {
		  'flashplayer': '/js/player.swf',
		  'id': 'tncstreamer',
		  'width': '537',
		  'height': '326',
		  'autostart':true,
		  'controlbar':'bottom',
		  'file': mapping[active]
		};

	}

	jwplayer('mediacontent_container').setup(setup);
}
}


// add video (interviews etc.)
if ($('#medialist2').length>0) {

var active = $('#activestream').text();

/*
if (active) {
	jwplayer('mediacontent_container').setup({
	  'id': 'tncstream',
	  'width': '768',
	  'height': '451',
	  'autostart':true,
	  'controlbar':'bottom',
	  'provider': 'rtmp',
	  'file': 'tnc2011/interviews/'+active+'.mp4',
	  'streamer': "rtmp://media.terena.org/fastplay",
	  'modes': [
	  	  {type: 'flash', src: '/js/player.swf'},
	      {
	        type: 'html5',
	        config: {
	         'file': 'http://media.terena.org:1935/fastplay/video/tnc2011/interviews/'+active+'.mp4/playlist.m3u8',
	         'provider': 'http'
	        }
	      }
	  ]
	});
}
*/

}


//  Promo video!!!
if ($('#coredemo_container').length>0) {

	jwplayer('coredemo_container').setup({
		'id': 'corestream',
		'flashplayer': '/js/player.swf',
		'file': 'https://youtu.be/wy04YVC0dI8',
		'width': '900',
		'height': '533',
		'autostart': 'false'
	});
	
	jwplayer('coredemo_container2').setup({
		'id': 'corestream',
		'flashplayer': '/js/player.swf',
		'file': 'https://youtu.be/S6QnY3Vm90g',
		'width': '900',
		'height': '533',
		'autostart': 'true'
	});	
	

}

});
