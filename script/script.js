$(document).ready(function() {
	$('#submit').click(function(e) {
		e.preventDefault();
		var getSearchString = $('#searchfield').val();

		$.ajax({
			type: 'POST',
			url: './search/searchScript.php',
			data: {'searchString' : getSearchString},
			cache: false,
			success: function(returnValue)
        	{
            	alert("search Submitted");
            	console.log(returnValue);
            	handleReturn(returnValue);
        	},
      		error: function (returnValue){
				alert("search was not submitted corectly");
				console.log("Ajax post error:");
				console.log(returnValue);
			}
		});

	});

});


	function handleReturn (returnJson){
		$('#allLinks').empty();
		for (var i = 0; i < returnJson.length; i++){
			$('#allLinks').append( '<a target="_blank" href="' + returnJson[i].url +'" class="list-group-item"><b>' + 
			returnJson[i].name + '</b><p>' + returnJson[i].url + '</p></a>' );
		}



	}


		/*<a href="#" class="list-group-item">
			<b>Name</b>
			<p>Url</p>
		</a>*/