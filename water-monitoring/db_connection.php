<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "esp32_data";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


function getLatestSensorData() {
    global $conn;
    $sql = "SELECT Turbid_Percentage, Distance_Percentage, timestamp FROM esp32_data  ORDER BY timestamp DESC LIMIT 1";
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        return $row;
    }
    return ['Turbid_Percentage' => 0, 'Distance_Percentage' => 0, 'timestamp' => null];
}
?>
