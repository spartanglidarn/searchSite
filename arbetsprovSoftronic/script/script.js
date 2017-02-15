$(document).ready(function() {
	$('#submit').click(function(e) {
		e.preventDefault();
		var getSearchString = $('#searchfield').val();
		/*
		ajax anrop som skickar söksträngen via en POST metod till searchScript.php
		när sök scriptet sedan har sök och retunerat sina resultat så svarar det med en json fil
		till success metoden.
		*/ 
		$.ajax({
			type: 'POST',
			url: './search/searchScript.php',
			data: {'searchString' : getSearchString},
			cache: false,
			success: function(returnValue)
        	{
            	console.log(returnValue);
            	handleReturn(returnValue);
        	},
      		error: function (returnValue){
				console.log(returnValue);
				handleError();

			}
		});

	});

	//aktiverar flikarna för respektive sökmotor
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

//funktion som körs om ajax-anropet retunerar ett ok svar från searchScript
function handleReturn (returnJson){
	//tömmer listan för att fylla på med nya resultat
	$('.linkList').empty();
	
	//loopar igenom Json filen och lägger in resultaten i varje flik,
	for (var i = 0; i < returnJson.length; i++){
		var urlString = returnJson[i].url;
		var nameString = returnJson[i].name;
		//för att det inte ska bli för plottrigt visar vi max 30 tecken av URLen.
		if (urlString.length >= 30){
			urlString = urlString.substring(0, 27);
			urlString = urlString + "...";
		}
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

//Ifall ajax anropet skulle retunera ett error så publiceras nedan text på sidan.
function handleError(){
	$('.linkList').empty();
	$('.linkList').append( '<a class="list-group-item"><b>Something went wrong with the search, please try again</b></a>');
}
