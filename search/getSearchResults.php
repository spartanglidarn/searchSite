<?php
//include ('searchScript.php');
//header('Content-Type: text/plain');
$testSearchString = "annars";

$google = "http://www.google.com/search?q=". $testSearchString;
$bing = "http://www.bing.com";


$sitesToSearch = array($google);
//foreach ($sitesToSearch as $key => $value){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $google);
	curl_setopt($ch, CURLOPT_USERAGENT, "Google Bot");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	//echo $value;
	
	echo $output = curl_exec($ch);
	curl_close($ch);

	//if ($key == 0){ //on google search
	preg_match_all('/<div\s+class="srg">(.*?)<\/div>/', $output, $match);



		foreach($match as $value){
			$counter = 1;
			if (is_array($value)){
				foreach($value as $valueTwo){
				echo $valueTwo;
				echo "\r\n" . $counter. "\r\n";
				$counter ++;
				}
			}

		}
		//print_r ($r);
		//print_r ($kv); 
		//print_r ($st);


	//}
//}

?>