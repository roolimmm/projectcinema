
<?php

// store the SQL server and database login details
$host = "localhost";
$username = "root";
$password = "";
$dbname = "cinema";

// open connection that included pages can reuse for their queries.
$db = new mysqli($host, $username, $password, $dbname);

// stop page if a connection cannot be established
if ($db->connect_error) {
    error_log($db->connect_error);
    exit("Database connection failed.");
}

$db->set_charset("utf8mb4");

?>
