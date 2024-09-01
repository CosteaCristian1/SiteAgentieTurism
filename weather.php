<?php
$city = "Bucharest";
$apiKey = "YOUR_API_KEY"; 

$apiUrl = "http://api.weatherapi.com/v1/current.json?key=$apiKey&q=$city";

$weatherData = file_get_contents($apiUrl);
$weather = json_decode($weatherData, true);

if ($weather) {
    echo "<h2>Weather in " . $city . "</h2>";
    echo "<p>Temperature: " . $weather['current']['temp_c'] . "°C</p>";
    echo "<p>Condition: " . $weather['current']['condition']['text'] . "</p>";
} else {
    echo "<p>Unable to fetch weather data.</p>";
}
?>
