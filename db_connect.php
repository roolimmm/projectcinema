
<?php

$host = "localhost";
$username = "root";
$password = "";
$dbname = "cinema";

// Connect to MySQL
$db = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($db->connect_error) {
    error_log($db->connect_error);
    exit("Database connection failed.");
}

// Set character encoding
$db->set_charset("utf8mb4");

?>
