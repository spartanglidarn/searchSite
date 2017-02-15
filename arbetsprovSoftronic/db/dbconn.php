<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "search_site";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

//check connection
if ($conn->connect_error){
	die("Connection failed: " . $conn->connection_error);
}

?>