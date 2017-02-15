<?php 

include '../db/dbconn.php';
$timeStamp = date('Y-m-d H:i:s');
//header('Content-Type: text/plain');
header('Content-Type: application/json');
$searchString = $_POST['searchString'];
//söksträng som används vid testning
//$searchString = "vatten flaska";

//sätter upp sql frågan till databasen för att spara ner söksträngen.
$stmt = $conn->prepare('INSERT INTO search_strings(search_string, reg_date)
						VALUES (?, ?)');
//kollar efter felmeddelande i $stmt
if ( false===$stmt ) {
	  die('prepare() failed: ' . htmlspecialchars($mysqli->error));
}

//binder data till den fördefinerade sql frågan som spara söksträngen.
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
//hämtar hem söksträngens id i databasen. Denna används sedan när resultaten ska sparas
$dbPostId = $conn->insert_id;

//stänger kopplingen till mysql databas.
$stmt->close();

//Encodar strängen för att den skall fungera i en url
$encodedSearchString = urlencode($searchString);

//Sätter upp google och bing urler
$google = 'http://www.google.com/search?num=30&q='. $encodedSearchString;
$bing = 'http://www.bing.com/search?count=30&q='. $encodedSearchString;
$searchEngine = "Google";
$bingSearchEngine = "Bing";

//hämtar hem googlesidan med hjälp av cUrl
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $google); //sätter url
//Väljer att surfa in på sidan som "google bot". använder google bot i förhoppning om att datan ska vara lite mer strukturerad på sidan men har itne sett någon större skillnad
curl_setopt($ch, CURLOPT_USERAGENT, "Google Bot"); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

//sparar googles resultatsida i en variable
$output = curl_exec($ch);

//Söker på bing
curl_setopt($ch, CURLOPT_URL, $bing);

//Sparar Bings resultatsida i en variable
$bingOutput = curl_exec($ch);

//Stänger curlanrop
curl_close($ch);

//Definerar den första regex för att hitta sökresultat på respektive site.
$googleSearchRegex = '~<h3\s+class="r">(.*?)<\/h3>~';
$bingSearchRegex = '~<div\s+class="b_title"><h2>(.*?)<\/h2>~';

//Med hjälp av preg_match_all kan vi hämta hem varje resultat och spara det i en array. I och med att jag använder _all kan jag även anropa index nummer 1 i den första arrayen av två nestlade och därmed sortera bort söksträngen i sig.
preg_match_all($googleSearchRegex, $output, $match, PREG_PATTERN_ORDER);
preg_match_all($bingSearchRegex, $bingOutput, $bingMatch, PREG_PATTERN_ORDER);


//sätter upp sql frågan till databasen för att spara sökresultat.
$resultStmt = $conn->prepare('INSERT INTO search_results(string_id, result_url, result_name, search_engine, rank)
	VALUES (?, ?, ?, ?, ?)');
//kollar efter felmeddelande i $stmt
if ( false===$resultStmt ) {
	  die('prepare() failed: ' . htmlspecialchars($mysqli->error));
}

//Skapar upp en array som ska hålla alla resultat.
$searchResultsArray = array();	

//loopar igenom arrayens första objekt för att gå igenom varje div-tagg
$rankCounter = 1;

//Går igenom sökresultatet på google för att plocka ut URL och namn för varje resultat
foreach($match[1] as $valueTwo){

	//Delar upp strängen i två delar, första delen är <a href -öppningstaggen(URLen) och den andra delen är det som kommer inom taggen(namnet)
	$splitResult = explode('>', $valueTwo, 2);

	//Gör en till preg_match sökning för att få ut endast url texten och ingenting html relaterat. Här kunde jag inte använda mig av strip_tags funktionen då den även tog bort själva url texten.
	preg_match_all('~<a href="\/url\?q=(.*?)&amp~', $splitResult[0], $linkUrl);
	//kör linkName genom strip_tags för att ta bort alla html taggar som kan finnas i strängen
	$linkName = strip_tags($splitResult[1]);

	//Kollar ifall det finns något i länkURL arrayen eller ifall den är tom.
	if(isset($linkUrl[1][0])){
		//om det finns så gör vi om det objektet till en sträng för enklare hantering i koden.
		$linkUrlString = $linkUrl[1][0];

		//på sidan visas upp till 30 resultat så här kollar vi ifall vi har sparat mer än 30 resultat
		if ($rankCounter <= 10){
			//binder data till den fördefinerade sql frågan för att spara ett resultat i databasen.
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
			//lägger in resultatet i en array som sedan skickas som json tillbaka till js script
			array_push($searchResultsArray, array("url" => $linkUrlString, "name" => $linkName, "searchEngine" => $searchEngine, "rank" => $rankCounter));
			$rankCounter ++;
		} else {
			//om vi har nått 10 resulat från denna sökmotor så bryter vi loopen
			break;
		}
		
	}	
}

//reset av rankCounter till 1. 
$rankCounter = 1;

//Går igenom sökresultatet på bing för att plocka ut URL och namn för varje resultat
foreach ($bingMatch[1] as $bingValue) {
	
	//Delar upp strängen i två delar, första delen är <a href -öppningstaggen(URLen) och den andra delen är det som kommer inom taggen(namnet)
	$bingSplitResult = explode('>', $bingValue, 2);
	
	//Gör en till preg_match sökning för att få ut endast url texten och ingenting html relaterat. Här kunde jag inte använda mig av strip_tags funktionen då den även tog bort själva url texten.
	preg_match_all('~<a href="(.*?)"~', $bingSplitResult[0], $bingLinkUrl);
	
	//kör linkName genom strip_tags för att ta bort alla html taggar som kan finnas i strängen
	$bingLinkName = strip_tags($bingSplitResult[1]);
	
	//Kollar ifall det finns något i länkURL arrayen eller ifall den är tom.
	if (isset($bingLinkUrl[1][0])){
		//om det finns så gör vi om det objektet till en sträng för enklare hantering i koden.
		$bingLinkUrlString = $bingLinkUrl[1][0];
		//på sidan visas upp till 30 resultat så här kollar vi ifall vi har sparat mer än 30 resultat
		if($rankCounter <= 10){
		//binder data till den fördefinerade sql frågan för att spara ett resultat i databasen.
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
			//lägger in resultatet i en array som sedan skickas som json tillbaka till js script
			array_push($searchResultsArray, array("url" => $bingLinkUrlString, "name" => $bingLinkName, "searchEngine" => $bingSearchEngine, "rank" => $rankCounter));
			$rankCounter ++;
		} else {
			//om vi har nått 10 resulat från denna sökmotor så bryter vi loopen
			break;
		}
		
	}
}

//stänger kopplingen till mysql databas.
$resultStmt->close();
//När scriptet har körts och arrayen med sökresultat är fylld så konverterar vi den till json format och skickar tillbaka den till ajax anropet via success funktionen
echo json_encode($searchResultsArray);
?>