<?php
$DB_HOST = "localhost";
$DB_USER = "root";      // XAMPP default username
$DB_PASS = "";          // XAMPP default password is blank
$DB_NAME = "portfolioDB";  // your database name

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
  die("DB connection failed: " . $conn->connect_error);
}
?>
