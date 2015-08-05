$(document).ready(function() {
	$('#submittest').click(function(){
		$.ajax({ 
		  url: "/core/submit/mail/"+$("#id").val()+"/json", 
		  context: document.body,
		  cache: false,
		  data: $("#mailform").serialize(),
		  type: 'post', 
		  dataType: 'json',
		  success: function(data){
		  	console.log(data.submissions);
		  	$('#placeholder').html(data.submissions);			
		  }
		});
	});
});
