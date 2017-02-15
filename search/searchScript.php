<?php 

include '../db/dbconn.php';
$timeStamp = date('Y-m-d H:i:s');
//header('Content-Type: text/plain');
header('Content-Type: application/json');
$searchString = $_POST['searchString'];
//$searchString = "testing script";

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
$bing = 'http://www.bing.com/search?count=30&q='. $encodedSearchString;
$searchEngine = "Google";
$bingSearchEngine = "Bing";

$sitesToSearch = array($google);

//hämtar hem googlesidan med hjälp av cUrl
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $google);
curl_setopt($ch, CURLOPT_USERAGENT, "Google Bot");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);


$output = curl_exec($ch);

curl_setopt($ch, CURLOPT_URL, $bing);
curl_setopt($ch, CURLOPT_USERAGENT, "Google Bot");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$bingOutput = curl_exec($ch);

curl_close($ch);

//letar reda på alla h3 taggar med klassen "r"
$googleSearchRegex = '~<h3\s+class="r">(.*?)<\/h3>~';
$bingSearchRegex = '~<div\s+class="b_title"><h2>(.*?)<\/h2>~';

preg_match_all($googleSearchRegex, $output, $match, PREG_PATTERN_ORDER);
preg_match_all($bingSearchRegex, $bingOutput, $bingMatch, PREG_PATTERN_ORDER);



//sätter upp sql frågan till databasen för att spara sökresultat.
$resultStmt = $conn->prepare('INSERT INTO search_results(string_id, result_url, result_name, search_engine, rank)
	VALUES (?, ?, ?, ?, ?)');
//kollar efter felmeddelande i $stmt
if ( false===$resultStmt ) {
	  die('prepare() failed: ' . htmlspecialchars($mysqli->error));
}

$searchResultsArray = array();	

//loopar igenom arrayens första objekt för att gå igenom varje div-tagg
$rankCounter = 1;

foreach ($bingMatch[1] as $bingValue) {
	$bingSplitResult = explode('>', $bingValue, 2);
	preg_match_all('~<a href="(.*?)"~', $bingSplitResult[0], $bingLinkUrl);
	
	$bingLinkName = strip_tags($bingSplitResult[1]);
	

	if (isset($bingLinkUrl[1][0])){
		$bingLinkUrlString = $bingLinkUrl[1][0];
		if (($bingLinkUrlString != null) && ($bingLinkName != null)){
			if($rankCounter <= 10){

				$resultBp = $resultStmt->bind_param('isssi', $dbPostId, $bingLinkUrlString, $bingLinkName, $bingSearchEngine, $rankCounter);
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
				array_push($searchResultsArray, array("url" => $bingLinkUrlString, "name" => $bingLinkName, "searchEngine" => $bingSearchEngine, "rank" => $rankCounter));
				$rankCounter ++;
			} else {
				break;
			}
		}
	}
}

$rankCounter = 1;

foreach($match[1] as $valueTwo){

	//skriv ut objektet.
	$splitResult = explode('>', $valueTwo, 2);
	//$linkUrl = new SimpleXMLElement($splitResult[0]);
	preg_match_all('~<a href="\/url\?q=(.*?)&amp~', $splitResult[0], $linkUrl);
	$linkName = strip_tags($splitResult[1]);


	
	if(isset($linkUrl[1][0])){
		$linkUrlString = $linkUrl[1][0];
		if (!preg_match('~Nyheter~', $valueTwo) && ($linkUrlString != null) && ($linkName != null)){

			if ($rankCounter <= 10){
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
				$rankCounter ++;
			} else {
				break;
			}
		} 
	}	
}
	
//print_r ($searchResultsArray);

//stänger kopplingen till mysql databas.
$resultStmt->close();
echo json_encode($searchResultsArray);
?>
