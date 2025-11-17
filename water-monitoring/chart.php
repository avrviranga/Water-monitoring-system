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

// Initialize arrays
$level = array();
$time = array();

// Attempt select query execution
try {
    $sql = "SELECT Distance_Percentage, timestamp 
            FROM esp32_data 
            ORDER BY timestamp DESC 
            LIMIT 10";
    
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $level[] = $row["Distance_Percentage"];
            $time[] = $row["timestamp"];
        }
    } else {
        echo "No records matching your query were found.";
    }
} catch(Exception $e) {
    die("ERROR: Could not execute $sql. " . $e->getMessage());
}
// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progress Chart - WaterMax Water Monitoring System</title>
            <link rel="stylesheet" href="style1.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Water Level Progress</h1>
            <p class="subtitle">Real-Time Monitoring & Analytics</p>
            <div class="live-badge">LIVE DATA</div>
        </div>

        <div class="content">
            <div class="intro">
                <p>Track your water tank levels over time with our advanced monitoring system. This chart displays the last 10 recorded measurements, helping you understand usage patterns and make informed decisions about your water management.</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">💧</div>
                    <h3>Current Level</h3>
                    <div class="stat-value"><?php echo isset($level[0]) ? number_format($level[0], 1) : '0.0'; ?>%</div>
                    <div class="stat-label">Water Tank Capacity</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">📈</div>
                    <h3>Data Points</h3>
                    <div class="stat-value"><?php echo count($level); ?></div>
                    <div class="stat-label">Recent Measurements</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">🕐</div>
                    <h3>Last Update</h3>
                    <div class="stat-value" style="font-size: 1.3em;"><?php echo isset($time[0]) ? date('H:i', strtotime($time[0])) : '--:--'; ?></div>
                    <div class="stat-label"><?php echo isset($time[0]) ? date('M d, Y', strtotime($time[0])) : 'No Data'; ?></div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">⚡</div>
                    <h3>System Status</h3>
                    <div class="stat-value" style="font-size: 1.8em;">ACTIVE</div>
                    <div class="stat-label">Monitoring Online</div>
                </div>
            </div>

            <div class="chart-container">
                <div class="chart-header">
                    <h2>Water Level Trend</h2>
                    <p>Last 10 measurements showing water tank level progression</p>
                </div>
                
                <div class="chart-wrapper">
                    <canvas id="myChart"></canvas>
                </div>

                <div class="legend-container">
                    <div class="legend-item">
                        <div class="legend-color"></div>
                        <span>Water Level Percentage</span>
                    </div>
                </div>
            </div>

            <div class="info-section">
                <h2>Understanding Your Data</h2>
                <div class="info-grid">
                    <div class="info-card">
                        <h4>📊 Chart Reading</h4>
                        <p>The line chart displays water level percentages over time. Higher values indicate fuller tank capacity.</p>
                    </div>

                    <div class="info-card">
                        <h4>🎯 Optimal Range</h4>
                        <p>Maintain water levels between 20-80% for optimal system performance and longevity.</p>
                    </div>

                    <div class="info-card">
                        <h4>⚠️ Alert Levels</h4>
                        <p>When the level reaches 100%, turn off the water pump. Below 10% indicates low water supply.</p>
                    </div>

                    <div class="info-card">
                        <h4>🔄 Update Frequency</h4>
                        <p>Data is collected and updated automatically every 5 seconds for real-time accuracy.</p>
                    </div>
                </div>
            </div>

            <div class="back-section">
                <h2>Continue Monitoring</h2>
                <p>Return to the main dashboard to view live water levels and turbidity readings.</p>
                <a href="index.php" class="cta-button">Back to Dashboard →</a>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
    <script>
        var ctx = document.getElementById('myChart').getContext("2d");
        var gradientStroke = ctx.createLinearGradient(500, 0, 100, 0);
        gradientStroke.addColorStop(0, "#061fc4");
        gradientStroke.addColorStop(1, "#9ca9ff");

        const level = <?php echo json_encode(array_reverse($level)); ?>;
        const time = <?php echo json_encode(array_reverse($time)); ?>;
        
        // Format time labels
        const formattedTime = time.map(t => {
            const date = new Date(t);
            return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        });

        const data = {
            labels: formattedTime,
            datasets: [{
                label: "Water Level (%)",
                borderColor: gradientStroke,
                pointBorderColor: gradientStroke,
                pointBackgroundColor: gradientStroke,
                pointHoverBackgroundColor: gradientStroke,
                pointHoverBorderColor: gradientStroke,
                pointBorderWidth: 10,
                pointHoverRadius: 10,
                pointHoverBorderWidth: 1,
                pointRadius: 5,
                fill: false,
                borderWidth: 4,
                data: level,
                tension: 0.4
            }]
        };

        const config = {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            fontColor: "rgba(0,0,0,0.7)",
                            fontStyle: "bold",
                            fontSize: 14,
                            beginAtZero: true,
                            max: 100,
                            maxTicksLimit: 6,
                            padding: 20,
                            callback: function(value) {
                                return value + '%';
                            }
                        },
                        gridLines: {
                            drawTicks: false,
                            display: true,
                            color: "rgba(0,0,0,0.1)"
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'Water Level Percentage',
                            fontSize: 14,
                            fontStyle: 'bold',
                            fontColor: "rgba(0,0,0,0.7)"
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            zeroLineColor: "transparent",
                            display: true,
                            color: "rgba(0,0,0,0.1)"
                        },
                        ticks: {
                            padding: 20,
                            fontColor: "rgba(0,0,0,0.7)",
                            fontStyle: "bold",
                            fontSize: 12
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'Time',
                            fontSize: 14,
                            fontStyle: 'bold',
                            fontColor: "rgba(0,0,0,0.7)"
                        }
                    }]
                },
                tooltips: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleFontSize: 14,
                    bodyFontSize: 13,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function(tooltipItem) {
                            return 'Water Level: ' + tooltipItem.yLabel.toFixed(1) + '%';
                        }
                    }
                }
            }
        };

        const myChart = new Chart(ctx, config);

        // Add smooth animation on load
        window.addEventListener('load', function() {
            myChart.update();
        });
    </script>
</body>
</html>