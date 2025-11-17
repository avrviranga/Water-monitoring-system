<?php
include 'db_connection.php';

$latestData = getLatestSensorData();

echo json_encode([
    'Distance_Percentage' => floatval($latestData['Distance_Percentage']),
    'Turbid_Percentage' => floatval($latestData['Turbid_Percentage']),
    'timestamp' => $latestData['timestamp']
]);
?>
