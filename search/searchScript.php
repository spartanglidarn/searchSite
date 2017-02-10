<?php 

include '../db/dbconn.php';
$timeStamp = date('Y-m-d H:i:s');

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


?>