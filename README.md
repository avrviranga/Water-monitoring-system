### WaterMax™ Water Monitoring System
Real-time IoT water monitoring system with ESP32 and responsive web dashboard.

✨ Features

- 📊 Real-time water level & turbidity monitoring (5-second updates)
- 📡 100% wireless via Wi-Fi (802.11 b/g/n)
- 📈 Interactive data visualization with Chart.js
- 🎯 Automated pump control with relay module
- 📱 Fully responsive design
- 🔒 Secure MySQL database storage

🛠️ Tech Stack

| Category | Technology |
|----------|-----------|
| Hardware | ESP32, HC-SR04, Turbidity Sensor, LCD 16x2, Relay |
| Backend | PHP 7.4+, MySQL 5.7+ |
| Frontend | HTML5, CSS3, JavaScript, Chart.js |
| Server | Apache (XAMPP/WAMP) |

### 1. Clone Repository

### 2. Database Setup
  sql
  CREATE DATABASE esp32_data;
  USE esp32_data;
  
  CREATE TABLE esp32_data (
      id INT AUTO_INCREMENT PRIMARY KEY,
      Distance_Percentage FLOAT,
      Turbid_Percentage FLOAT,
      timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  );

### 3. Configure Database
Edit `db_connection.php`:
$servername = "localhost";
$username = "root";
$password = "your_password";
$dbname = "esp32_data";


### 4. Deploy to Server
# For XAMPP
cp -r watermax-monitoring-system /xampp/htdocs/

### 5. Access Dashboard
http://localhost/watermax-monitoring-system/index.php

## 🎯 Pages Overview
| Page | Description |
|------|-------------|
| **Dashboard** | Real-time circular progress indicators |
| **Home** | System introduction & features |
| **About** | Technical specs & components |
| **Contact** | Support information |
| **Progress** | Historical data charts |
