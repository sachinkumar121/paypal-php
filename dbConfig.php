<?php
define("USER", "sukant_api1.mobilyte.com", true);
define("PWD", "9PZCLK395J63RXPQ", true);
define("SIGNATURE", "A1eRIOfCz42bySEn-ogX.K0yYssjAT2HPxhA0eNcmRT2YL7vGIG18weK", true);
date_default_timezone_set("UTC");
/*define("USER", "[[Username]]", true);
define("PWD", "[[password]]", true);
define("SIGNATURE", "[[signature]]]", true);*/
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "paypal";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
