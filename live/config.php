<?php

/**
Blicity CAD/MDT
Copyright (C) 2018 Decyphr and Blicity.
 Credit is not allowed to be removed from this program, doing so will
 result in copyright takedown.
 WE DO NOT SUPPORT CHANGING CODE IN ANYWAY, AS IT WILL MESS WITH FUTURE
 UPDATES. NO SUPPORT IS PROVIDED FOR CODE THAT IS EDITED.
**/

define('MYSQL_USER', 'pdo');
define('MYSQL_PASSWORD', 'Shadow4115!');
define('MYSQL_HOST', 'localhost');
define('MYSQL_DATABASE', 'blicity');

$connection = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}
$result = $connection->query("SELECT * FROM settings");
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        define('TITLE', $row["title"]);
        define('SITE_URL', $row["siteUrl"]);
        define('DISCORD_MODULE', $row["discordModule"]);
        define('CUSTOM_DEPARTMENTS_MODULE', $row["customDepartmentsModule"]);
        define('DOWF_MODULE', $row["dowfModule"]);
    }
} else {
    echo "0 results";
}
$connection->close();
?>
