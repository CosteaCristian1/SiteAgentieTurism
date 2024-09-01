<?php
session_start();
include 'db_config.php';


$apiKey = '0825640f14c127f24b52dd62782291fe'; 
$city = 'london'; 
$apiUrl = "https://api.openweathermap.org/data/2.5/weather?q=$city&appid=$apiKey";


$weatherData = file_get_contents($apiUrl);


$weatherArray = json_decode($weatherData, true);

$temperature = $weatherArray['main']['temp'];
$description = $weatherArray['weather'][0]['description'];
$humidity = $weatherArray['main']['humidity'];
$windSpeed = $weatherArray['wind']['speed'];
$cityName = $weatherArray['name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Costea Tour</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <nav>
            <ul>
                <li><a href="view_offers.php">Offers</a></li>
                <?php if (isset($_SESSION['username'])): ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>

    <main>
        <div class="main-content">
            <h1>Welcome to Our Travel Agency</h1>
            <p>Discover amazing places at exclusive deals with Costea Tour!<br><br>
            Costea Tour is more than a travel agency—it’s a gateway to the world’s 
            most remarkable destinations. The staff consists of experienced travel 
            consultants who have explored every continent, and their passion for discovery 
            is evident in their personalized service and insider knowledge.</p>
        </div>

        <section id="offers" class="main-content">
            <h2>Our Top Offers</h2>
            <?php
            $sql = "SELECT * FROM offers LIMIT 3";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='offer'>";
                    echo "<h3>" . $row['location'] . "</h3>";
                    echo "<p>" . $row['description'] . "</p>";
                    echo "<p>Price: $" . $row['price'] . "</p>";
                    echo "<p>Available from: " . $row['available_from'] . " to " . $row['available_to'] . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p>No offers available at the moment.</p>";
            }
            ?>
            <br><br>
            <h2>Weather in <?php echo $cityName; ?></h2>
            <p><strong>Temperature:</strong> <?php echo $temperature-273.15; ?> °C</p>
            <p><strong>Condition:</strong> <?php echo ucfirst($description); ?></p>
            <p><strong>Humidity:</strong> <?php echo $humidity; ?>%</p>
            <p><strong>Wind Speed:</strong> <?php echo $windSpeed; ?> m/s</p>

        <br><br>
        <?php 
        if (isset($_SESSION['role'])): { ?>
            <h2>Contact Us</h2>
            <form action="send_email.php" method="post">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
                <label for="message">Message:</label>
                <textarea id="message" name="message" required></textarea>
                <input type="submit" value="Send">
            </form>
            <br>
        <?php } endif; ?> 

        <footer>
            <p>&copy; <?php echo date('Y'); ?> Costea Tour. All rights reserved.</p>
        </footer>
        </section>
    </main>
</body>
</html>
