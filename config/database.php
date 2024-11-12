
<?php

//viết trang database liên kết đến database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "quan_ly_nhan_su";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}