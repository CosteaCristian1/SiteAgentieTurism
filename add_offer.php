<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $location = $_POST['location']; 
    $description = $_POST['description'];
    $price = $_POST['price'];
    $available_from = $_POST['available_from'];
    $available_to = $_POST['available_to'];
    $country = $_POST['country']; 

    $sql = "INSERT INTO offers (location, description, price, available_from, available_to, country) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsss", $location, $description, $price, $available_from, $available_to, $country);

    if ($stmt->execute()) {
        echo "<script>alert('Offer added successfully.'); window.location.href = 'view_offers.php';</script>";
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Offer</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
    <form method="post" action="add_offer.php" class="main-content">
    <h2>Add Offer</h2>
        Location: <input type="text" name="location" required><br>
        Description: <textarea name="description" required></textarea><br>
        Price: <input type="number" name="price" required><br>
        Trip Duration From: <input type="date" name="available_from" required><br>
        Trip Duration To: <input type="date" name="available_to" required><br>
        Country: <input type="text" name="country" required><br>
        <input type="submit" value="Add Offer">
    </form>
</body>
</html>
