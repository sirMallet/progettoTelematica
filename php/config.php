<?php 

$host = "127.0.0.1";
$user = "root";
$password = "";
$database = "edilizia";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Connessione al database fallita: " . mysqli_connect_error());
}

?>  