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
//var type = ($('div#streams').hasClass('archive')) ? 'vod' : 'live';
//var file = active+'u.stream';

if (active) {
	jwplayer('mediacontent_container').setup({
	  'id': 'tncstreamer',
	  'width': '590',
	  'height': '357',
	  'autostart':true,
	  'controlbar':'bottom',
	  'provider': 'rtmp',
	  'file': 'tnc2011/archives/'+active+'.mp4',
	  'streamer': 'rtmp://media.terena.org/fastplay',
	  'modes': [
	  	  {type: 'flash', src: '/js/player.swf'},
	      {
	        type: 'html5',
	        config: {
			 'file': 'http://media.terena.org:1935/fastplay/video/tnc2011/archives/'+active+'.mov/playlist.m3u8',
	         'provider': 'http'
	        }
	      },
	      {
	        type: 'download',
	        config: {
			'file': 'http://media.terena.org:1935/fastplay/video/tnc2011/archives/'+active+'.mov/playlist.m3u8',
	         'provider': 'http'
	        }
	      }
	  ]
	});
}
}

// add video (interviews etc.)
if ($('#medialist2').length>0) {

var active = $('#activestream').text();

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
}

// add CORE Demo stream
if ($('#coredemo_container').length>0) {

jwplayer('coredemo_container').setup({
  'id': 'corestream',
  'width': '800',
  'height': '623',
  'autostart':true,
  'controlbar':'bottom',
  'provider': 'rtmp',
  'file': 'tnc2011/COREDemo.mp4',
  'streamer': "rtmp://media.terena.org/fastplay",
  'modes': [
  	  {type: 'flash', src: '/js/player.swf'},
      {
        type: 'html5',
        config: {
         'file': 'http://media.terena.org:1935/fastplay/video/tnc2011/COREDemo.mp4/playlist.m3u8',
         'provider': 'http'
        }
      }
  ]
});

}


// add google maps style address completion (@todo: move to CORE)
if ($("#location_element").length>0) {

var geocoder;
var map;
var marker;

var geocoder;
var map;
var marker;

var DEFAULT_LATITUDE = 41.659;
var DEFAULT_LONGITUDE = -4.714;

function initialize(){
	lat = $("#location_element input[name='lat']");
	lng = $("#location_element input[name='lng']");
	var latitude = lat.val();
	var longitude = lng.val();

	// use default location if nothing is saved
    if (!latitude || !longitude) {
        latitude = DEFAULT_LATITUDE;
        longitude = DEFAULT_LONGITUDE;
    }

	var latlng = new google.maps.LatLng(latitude, longitude);

	var options = {
	  zoom: 16,
	  center: latlng,
	  mapTypeId: google.maps.MapTypeId.SATELLITE
	};

	map = new google.maps.Map(document.getElementById("map_canvas"), options);

	geocoder = new google.maps.Geocoder();

	marker = new google.maps.Marker({
	  map: map,
	  draggable: true
	});
}
initialize();

  $(function() {
    $("#address").autocomplete({
      //This bit uses the geocoder to fetch address values
      source: function(request, response) {
        geocoder.geocode( {'address': request.term }, function(results, status) {
          response($.map(results, function(item) {
            return {
              label:  item.formatted_address,
              value: item.formatted_address,
              latitude: item.geometry.location.lat(),
              longitude: item.geometry.location.lng()
            }
          }));
        })
      },
      //This bit is executed upon selection of an address
      select: function(event, ui) {
        lat.val(ui.item.latitude);
        lng.val(ui.item.longitude);
        var location = new google.maps.LatLng(ui.item.latitude, ui.item.longitude);
        marker.setPosition(location);
        map.setCenter(location);
      }
    });
  });

// reverse geocoding
google.maps.event.addListener(marker, 'drag', function() {
  geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {
    if (status == google.maps.GeocoderStatus.OK) {
      if (results[0]) {
        $('#address').val(results[0].formatted_address);
        $('#latitude').val(marker.getPosition().lat());
        $('#longitude').val(marker.getPosition().lng());
      }
    }
  });
});

} // end address completion

});
