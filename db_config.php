<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "travel_agency";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$ip_address = $_SERVER['REMOTE_ADDR'];
$today = date('Y-m-d');

$sql = "SELECT * FROM website_visits WHERE ip_address = ? AND visit_date = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $ip_address, $today);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $insert_sql = "INSERT INTO website_visits (ip_address, visit_date) VALUES (?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("ss", $ip_address, $today);
    $insert_stmt->execute();
    $insert_stmt->close();
}

$stmt->close();


?>
