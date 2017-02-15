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
            	//alert("search Submitted");
            	console.log(returnValue);
            	handleReturn(returnValue);
        	},
      		error: function (returnValue){
				//alert("search was not submitted corectly");
				console.log(returnValue);
				handleError();

			}
		});

	});

	$('.linkButton').click(function(){
		$('.linkButton').removeClass('active');
		$(this).addClass('active');
		$('.list-group').addClass('hidden');
	});

	$('#allButton').click(function(){
		$('#allLinks').removeClass('hidden');
	});

	$('#googleButton').click(function(){
		$('#googleLinks').removeClass('hidden');
	});

	$('#bingButton').click(function(){
		$('#bingLinks').removeClass('hidden');
	});


});


function handleReturn (returnJson){
	$('.linkList').empty();
	
	for (var i = 0; i < returnJson.length; i++){
		var urlString = returnJson[i].url;
		var nameString = returnJson[i].name;
		/*if (urlString.length >= 30){
			urlString = urlString.substring(0, 27);
			urlString = urlString + "...";
		}*/
		$('#allLinks').append( '<a target="_blank" href="' + returnJson[i].url +'" class="list-group-item"><b>' + 
		returnJson[i].name + '</b><br>' + urlString + '<br> Search engine: ' +  returnJson[i].searchEngine + '<br> Rank: ' + returnJson[i].rank + '</a>' );

		if (returnJson[i].searchEngine == "Google"){
			$('#googleLinks').append( '<a target="_blank" href="' + returnJson[i].url +'" class="list-group-item"><b>' + 
			returnJson[i].name + '</b><br>' + urlString + '</a>' );
		}
		if (returnJson[i].searchEngine == "Bing"){
			$('#bingLinks').append( '<a target="_blank" href="' + returnJson[i].url +'" class="list-group-item"><b>' + 
			returnJson[i].name + '</b><br>' + urlString + '</a>' );
		}

	}
}

function handleError(){
	$('.linkList').empty();
	$('.linkList').append( '<a class="list-group-item"><b>Something went wrong with the search, please try again</b></a>');
}


		/*<a href="#" class="list-group-item">
			<b>Name</b>
			<p>Url</p>
		</a>*/