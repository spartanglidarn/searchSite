$(document).ready(function() {
	$('#submit').click(function(e) {
		e.preventDefault();
		var getSearchString = $('#searchfield').val();

		$.ajax({
			type: 'POST',
			url: './search/searchScript.php',
			data: {'searchString' : getSearchString},
			cache: false,
			success: function()
        	{
            	alert("search Submitted");
        	}
		});

	});
});



