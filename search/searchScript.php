<?php 

include '../db/dbconn.php';
$timeStamp = date('Y-m-d H:i:s');
//header('Content-Type: text/plain');
//header('Content-Type: application/json');
//$searchString = $_POST['searchString'];
$searchString = "vattenflaska";

//sätter upp sql frågan till databasen.
$stmt = $conn->prepare('INSERT INTO search_strings(search_string, reg_date)
						VALUES (?, ?)');
//kollar efter felmeddelande i $stmt
if ( false===$stmt ) {
	  die('prepare() failed: ' . htmlspecialchars($mysqli->error));
}

//binder data till den fördefinerade sql frågan.
$bp = $stmt->bind_param('ss', $searchString, $timeStamp);

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

$dbPostId = $conn->insert_id;

//stänger kopplingen till mysql databas.
$stmt->close();

//söker på google och bing, samt sparar ner i databasen
$encodedSearchString = urlencode($searchString);

$google = 'http://www.google.com/search?num=30&q='. $encodedSearchString;
$bing = "http://www.bing.com";
$searchEngine = "Google";

$sitesToSearch = array($google);

//hämtar hem googlesidan med hjälp av cUrl
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $google);
curl_setopt($ch, CURLOPT_USERAGENT, "Google Bot");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);


echo $output = curl_exec($ch);
curl_close($ch);

//letar reda på alla h3 taggar med klassen "r"
$googleSearchRegex = '~<h3\s+class="r">(.*?)<\/h3>~';
preg_match_all($googleSearchRegex, $output, $match, PREG_PATTERN_ORDER);


//sätter upp sql frågan till databasen för att spara sökresultat.
$resultStmt = $conn->prepare('INSERT INTO search_results(string_id, result_url, result_name, search_engine, rank)
	VALUES (?, ?, ?, ?, ?)');
//kollar efter felmeddelande i $stmt
if ( false===$resultStmt ) {
	  die('prepare() failed: ' . htmlspecialchars($mysqli->error));
}

$searchResultsArray = array();	

//loopar igenom arrayens första objekt för att gå igenom varje div-tagg
$rankCounter = 0;
foreach($match[1] as $valueTwo){

	//skriv ut objektet.
	$splitResult = explode('>', $valueTwo, 2);
	//$linkUrl = new SimpleXMLElement($splitResult[0]);
	preg_match_all('~<a href="\/url\?q=(.*?)"~', $splitResult[0], $linkUrl);
	$linkName = strip_tags($splitResult[1]);


	$rankCounter ++;
	if(isset($linkUrl[1][0])){
		$linkUrlString = $linkUrl[1][0];
		if (!preg_match('~Nyheter~', $valueTwo) && ($linkUrlString != null) && ($linkName != null)){


			//binder data till den fördefinerade sql frågan.
			$resultBp = $resultStmt->bind_param('isssi', $dbPostId, $linkUrlString, $linkName, $searchEngine, $rankCounter);

			//kollar efter felmeddelande i $bp
			if ( false===$resultBp ) {
				die('bind_param() failed: ' . htmlspecialchars($resultStmt->error));
			}

			//kör sql frågan mot databasen.
			$resultStmtexe = $resultStmt->execute();
			//kollar efter felmeddelande i $stmtexe
			if ( false===$resultStmtexe ) {
			  	die('execute() failed: ' . htmlspecialchars($resultStmt->error));
			}

			array_push($searchResultsArray, array("url" => $linkUrlString, "name" => $linkName, "searchEngine" => $searchEngine, "rank" => $rankCounter));

		} 
	}	
}
	
//print_r ($searchResultsArray);

//stänger kopplingen till mysql databas.
$resultStmt->close();
echo json_encode($searchResultsArray);
?>
