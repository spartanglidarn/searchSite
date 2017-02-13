<?php
include ('searchScript.php');
//header('Content-Type: text/plain');
$testSearchString = "for honor";
$stripSearchString = str_replace(" ", "+", $testSearchString);
$encodedSearchString = urlencode($searchString);

$google = "http://www.google.com/search?q=". $encodedSearchString;
$bing = "http://www.bing.com";
$searchEngine = "Google";

$sitesToSearch = array($google);

//hämtar hem googlesidan med hjälp av cUrl
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $google);
curl_setopt($ch, CURLOPT_USERAGENT, "Google Bot");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);


$output = curl_exec($ch);
curl_close($ch);

//letar reda på alla div taggar med klassen "g"
$googleSearchRegex = '~<h3\s+class="r">(.*?)<\/h3>~';
preg_match_all($googleSearchRegex, $output, $match, PREG_PATTERN_ORDER);


//sätter upp sql frågan till databasen för att spara sökresultat.
$stmt = $conn->prepare('INSERT INTO search_strings(string_id, result_url, result_name, search_engine, rank)
	VALUES (?, ?)');
//kollar efter felmeddelande i $stmt
if ( false===$stmt ) {
	  die('prepare() failed: ' . htmlspecialchars($mysqli->error));
}

	

//loopar igenom arrayens första objekt för att gå igenom varje div-tagg
$rankCounter = 0;
foreach($match[1] as $valueTwo){
	//om taggen har en table med classen ts så är det en nyhet och ska inte räknas med.
	$rankCounter ++;
	if (!preg_match('~Nyheter~', $valueTwo)){
		//skriv ut objektet.
		$splitResult = explode('>', $valueTwo, 2);
		//$linkUrl = new SimpleXMLElement($splitResult[0]);
		preg_match_all('~<a href="\/url\?q=(.*?)"~', $splitResult[0], $linkUrl);
		$linkName = strip_tags($splitResult[1]);


		//binder data till den fördefinerade sql frågan.
		$bp = $stmt->bind_param('isssi', $dbPostId, $linkUrl[1][0], $linkName, $searchEngine, $rankCounter);

		//kollar efter felmeddelande i $bp
		if ( false===$bp ) {
			die('bind_param() failed: ' . htmlspecialchars($stmt->error));
		}

		//kör sql frågan mot databasen.
		$stmtexe = $stmt->execute();
		//kollar efter felmeddelande i $stmtexe
		if ( false===$stmtexe ) {
		  die('execute() failed: ' . htmlspecialchars($stmt->error));
		}

		/*
		echo $linkUrl[1][0]. "<br>";
		echo $linkName . "<br><br>";
		*/
	}	
}
	

//stänger kopplingen till mysql databas.
$stmt->close();


?>
