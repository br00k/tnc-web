$(function() {
	var total = $('ul.toobig li').length;
	var items = $('ul.toobig li');
	var counter = 0;
	items.slice(5, total).css('display', 'none');
	
	var presLoop = function() {
		++counter;
	    if (counter*5 < total) {
	    	items.slice((counter*5)-5, counter*5).css('display', 'none');
	    	items.slice(counter*5, counter*5*2).css('display', 'block');
	    } else {
	    	items.slice(5, total).css('display', 'none'); 
	    	items.slice(0, 5).css('display', 'block');
	    	counter = 0;
	    }
	    console.log(counter);	
	}
	
	myInterval = setInterval(presLoop, 10000)
});