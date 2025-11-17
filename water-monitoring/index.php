<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - WaterMax Water Monitoring System</title>
        <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'db_connection.php'; ?>
    
    <div class="container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="logo-section">
                <img src="./images/logo.png" alt="WaterMax Logo" />
                <h2>WaterMax™</h2>
                <p>Smart Water Monitoring</p>
            </div>
            
            <nav class="nav-links">
                <a href="./home.html" class="nav-link">
                    <span class="nav-icon">🏠</span>
                    HOME
                </a>
                <a href="./about.html" class="nav-link">
                    <span class="nav-icon">ℹ️</span>
                    ABOUT
                </a>
                <a href="./contact.html" class="nav-link">
                    <span class="nav-icon">📞</span>
                    CONTACT
                </a>
                <a href="./chart.php" class="nav-link">
                    <span class="nav-icon">📊</span>
                    PROGRESS
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="header">
                <div class="header-content">
                    <h1>💧 Water Monitoring Dashboard</h1>
                    <p>Real-time monitoring of your water system</p>
                    <span class="status-badge">🔴 LIVE</span>
                </div>
            </div>

            <div class="monitoring-section">
                <!-- Water Level Monitor -->
                <div class="monitor-card">
                    <h2>Water Level</h2>
                    <div class="skill">
                        <div class="outer">
                            <div class="inner">
                                <div id="number1">0%</div>
                            </div>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="220px" height="220px">
                            <defs>
                                <linearGradient id="GradientColor">
                                    <stop offset="0%" stop-color="#293df0" />
                                    <stop offset="100%" stop-color="#a6aef5" />
                                </linearGradient>
                            </defs>
                            <circle class="circle1" cx="110" cy="110" r="90" stroke-linecap="round" />
                        </svg>
                    </div>
                    <p><strong>Water Level Status:</strong> This progressive indicator shows your current water tank level. When it reaches 100%, turn off the water pump immediately. For detailed trends, visit the Progress page.</p>
                </div>

                <!-- Turbidity Level Monitor -->
                <div class="monitor-card">
                    <h2>Turbidity Level</h2>
                    <div class="skill">
                        <div class="outer">
                            <div class="inner">
                                <div id="number2">0%</div>
                            </div>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="220px" height="220px">
                            <defs>
                                <linearGradient id="GradientColor2">
                                    <stop offset="0%" stop-color="#293df0" />
                                    <stop offset="100%" stop-color="#a6aef5" />
                                </linearGradient>
                            </defs>
                            <circle class="circle2" cx="110" cy="110" r="90" stroke-linecap="round" />
                        </svg>
                    </div>
                    <p><strong>Water Quality Status:</strong> This indicator displays water turbidity levels. If it reaches 100%, check your water tank immediately or call emergency support at 1990 for assistance.</p>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-card">
                    <div class="info-icon">⚡</div>
                    <h3>Auto-Refresh</h3>
                    <p>Data updates automatically every 5 seconds for real-time monitoring</p>
                </div>

                <div class="info-card">
                    <div class="info-icon">📡</div>
                    <h3>Wireless System</h3>
                    <p>100% wireless monitoring via Wi-Fi connectivity</p>
                </div>

                <div class="info-card">
                    <div class="info-icon">🎯</div>
                    <h3>Precision Sensors</h3>
                    <p>Advanced ultrasonic and turbidity sensors for accurate readings</p>
                </div>

                <div class="info-card">
                    <div class="info-icon">🔒</div>
                    <h3>Secure Data</h3>
                    <p>All measurements are encrypted and securely stored</p>
                </div>
            </div>

            <div class="alert-section">
                <h3>⚠️ Important Guidelines</h3>
                <p><strong>Water Level 100%:</strong> Turn off pump immediately to prevent overflow<br>
                <strong>Water Level &lt;10%:</strong> Low water supply - check source<br>
                <strong>Turbidity &gt;80%:</strong> Water quality issue - inspection required<br>
                <strong>Emergency:</strong> Call 1990 for immediate support</p>
            </div>
        </main>
    </div>

    <script src="main.js"></script>
</body>
</html>