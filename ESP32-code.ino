#include <WiFi.h>
#include <HTTPClient.h>
#include <LiquidCrystal_I2C.h> //for lcd display
#include <Wire.h> // Wire library is included for I2C communication

// Initialize the LCD, address 0x27 is common for most I2C modules.
LiquidCrystal_I2C lcd(0x27, 16, 2); 

#define TRIG_PIN 5   // Pin connected to HC-SR04 Trig
#define ECHO_PIN 18  // Pin connected to HC-SR04 Echo
#define RELAY_PIN 16 // Pin connected to Relay module
#define RED_LED_PIN 33 // Pin connected to the red LED (Wi-Fi status)
#define BLUE_LED_PIN 25 // Pin connected to the blue LED (Data sent status)

const int turbidityPin = 34; // ESP32 ADC Pin

// WiFi credentials (Stores the SSID and password to connect to the Wi-Fi)
const char* ssid = "Dialog 4G 722";        //network SSID
const char* password = "7fB351AD"; // network password

// Server URL
const char* serverURL = "http://192.168.8.187/esp32_project/insert_data.php";  // server IP

long duration;
float distance;
const float TANK_DISTANCE = 13.71;  // Maximum distance threshold in cm
const float MAX_DISTANCE = 13.71;  // Maximum distance Distance in cm
const float MIN_DISTANCE = 4.0;   // Minimum distance Distance in cm
const float voltageClear = 0.55;   // Voltage for clear water (0 NTU)
const float voltageTurbid = 0.5;  // Voltage for 1000 NTU (dirty water)
const float maxNTU = 10.0;
const float NTU_THRESHOLD = 4.0;   // Turbidity threshold

// Define LED states based on active LOW(Inverse)
#define LED_ON LOW
#define LED_OFF HIGH

// blinking of the red LED that indicates Wi-Fi connection
unsigned long previousBlinkTime = 0;
const long blinkInterval = 1000; // 1 second interval
bool ledState = false; // Current state of the red LED

// Function Declarations
void connectToWiFi();
void checkWiFiStatus();
float mapVoltageToNTU(float voltage);

void setup() {
  Serial.begin(115200); //Serial Communication
  Wire.begin();  // Initialize I2C communication
  Serial.println("\nI2C Scanner");

  // Initialize the LCD
  lcd.begin(16, 2);  // Set LCD for 16 columns and 2 rows
  lcd.backlight();   // Turn on the backlight

  lcd.setCursor(0, 0);
  lcd.print("WATERMAX"); 
  delay(2000); 

  // Setup pins
  pinMode(TRIG_PIN, OUTPUT);
  pinMode(ECHO_PIN, INPUT);
  pinMode(RELAY_PIN, OUTPUT);
  pinMode(RED_LED_PIN, OUTPUT);
  pinMode(BLUE_LED_PIN, OUTPUT);

  // Start with the relay off and LEDs off
  digitalWrite(RELAY_PIN, LOW);
  digitalWrite(RED_LED_PIN, LOW);
  digitalWrite(BLUE_LED_PIN, LOW);

  // Connect to Wi-Fi
  connectToWiFi();
}

void loop() {
  //  Regularly checks the Wi-Fi status
  checkWiFiStatus();
  
  // I2C scanning part for debugging
  byte error, address;
  int nDevices = 0;

  Serial.println("Scanning...");

  // Scanning Loop
  for (address = 1; address < 127; address++) {
    Wire.beginTransmission(address);
    error = Wire.endTransmission();

   // Device Detection
    if (error == 0) {
      Serial.print("I2C device found at address 0x");
      if (address < 16) Serial.print("0");
      Serial.print(address, HEX);
      Serial.println(" !");
      nDevices++;
    }
  }

  // Ultrasonic sensor code
  digitalWrite(TRIG_PIN, LOW);
  delayMicroseconds(2);
  digitalWrite(TRIG_PIN, HIGH);
  delayMicroseconds(10);
  digitalWrite(TRIG_PIN, LOW);
  duration = pulseIn(ECHO_PIN, HIGH);
  distance = duration / 58.2;  // Convert time to distance in cm

// Fill Percentage Calculation
float fillPercentage = (((MAX_DISTANCE - MIN_DISTANCE)- (distance - MIN_DISTANCE)) / (MAX_DISTANCE - MIN_DISTANCE)) * 100.0;

//Ensures the fill percentage is between 0 and 100 and casts it to an integer for easier display
fillPercentage = constrain(fillPercentage, 0, 100);
int distancePercentage = (int)fillPercentage;

  // calculates the turbidity percentage.
  int sensorValue = analogRead(turbidityPin);
  float voltage = (sensorValue / 4095.0) * 3.3; // ESP32 has a 12-bit ADC
  float ntu = mapVoltageToNTU(voltage);
  int turbidityPercentage = (ntu / maxNTU) * 100;

    // Ensures the turbidity percentage remains within the range of 0 to 100.
  if (turbidityPercentage < 0) turbidityPercentage = 0;
  if (turbidityPercentage > 100) turbidityPercentage = 100;

  // Motor status based on distance
  String motorStatus;
  if (distance <= MIN_DISTANCE) {
    motorStatus = "OFF";    // Turn ON the motor if distance is below or equal to MIN_DISTANCE
    digitalWrite(RELAY_PIN, HIGH);
  } else if (distance <= MAX_DISTANCE) {
    motorStatus = "ON";   // Turn OFF the motor if distance is greater than or equal to MAX_DISTANCE
    digitalWrite(RELAY_PIN, LOW);
  } else {
    motorStatus = "ON";   // Default motor status is OFF
    digitalWrite(RELAY_PIN, LOW);
  }

  // Determine if water is clear or dirty based on NTU value
  String waterQuality = (ntu <= NTU_THRESHOLD) ? "Clear" : "Dirt";

  // Send data to server
  if (WiFi.status() == WL_CONNECTED) { //Checks if connected to Wi-Fi
    HTTPClient http;
    http.begin("http://192.168.8.187/esp32_project/insert_data.php"); // SURL
    http.addHeader("Content-Type", "application/x-www-form-urlencoded"); // content-type

    // Prepares the HTTP POST request data by formatting it as a URL-encoded string
    String httpRequestData = "Turbid_Percentage=" + String(turbidityPercentage)
                           + "&Turbidity_Value=" + String(ntu, 3)
                           + "&Distance_Percentage=" + String(distancePercentage)
                           + "&Ultra_Sonic_Distance=" + String(distance, 2)
                           + "&Motor_Status=" + motorStatus;

    Serial.print("Data sent: ");
    Serial.println(httpRequestData); // Print the data being sent for verification

    int httpResponseCode = http.POST(httpRequestData); //  data to the server using an HTTP POST request

    if (httpResponseCode > 0) {
      String response = http.getString(); // Get the response to the request
      Serial.println(httpResponseCode);   // Print response code in serial monitor
      Serial.println(response);           // Print server response

      // Blink blue LED on successful data send
      digitalWrite(BLUE_LED_PIN, HIGH);
      delay(1000);
      digitalWrite(BLUE_LED_PIN, LOW);
      
      //Error Handling
    } else {
      Serial.print("Error on sending POST: ");
      Serial.println(httpResponseCode);
    }

    http.end(); // Close the connection
  }

// Display the values on the LCD
  lcd.clear();
  lcd.setCursor(0, 0);  // First row
  lcd.print("Fill: ");
  lcd.print(distancePercentage, 1);  // Display fill percentage with 1 decimal
  lcd.print("%");
  
  lcd.setCursor(0, 1);  // Second row
  lcd.print("Turb: ");
  lcd.print(turbidityPercentage, 1);  // Display turbidity percentage with 1 decimal
  lcd.print("%");

  lcd.setCursor(11, 1);  // Continue on the second row
  lcd.print(waterQuality);  // Display "Dirt" or "Clear"

  lcd.setCursor(11, 0);  // First row
  lcd.print(motorStatus);   // Display motor status

  //for varification
  Serial.println(ntu); 
  Serial.println(voltage); 
  Serial.println(distance);

  delay(1000); // Delay between readings
}

// Function to map voltage to NTU based on calibration
float mapVoltageToNTU(float voltage) {
  if (voltage >= voltageClear) {
    return 0.0; // Clear water
  } else if (voltage >= voltageTurbid) {
    return maxNTU; // Very turbid water
  } else {
    return maxNTU * ((voltageClear - voltage) / (voltageClear - voltageTurbid)); // Linear interpolation
  }
}

// Function to connect to Wi-Fi
//Wi-Fi Connection Attempt
void connectToWiFi() {
  WiFi.begin(ssid, password);
  Serial.print("Connecting to WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    // Blink the red LED to indicate connection attempt
    unsigned long currentMillis = millis();
    if (currentMillis - previousBlinkTime >= blinkInterval) {
      previousBlinkTime = currentMillis;
      ledState = !ledState;  // Toggle the LED state
      digitalWrite(RED_LED_PIN, ledState ? LED_ON : LED_OFF);  // Blink the LED
    }
    delay(500);
    Serial.print("."); //printing a dot every half-second.
  }
  Serial.println("\nConnected to WiFi");
  digitalWrite(RED_LED_PIN, LED_OFF);  // Turn off the red LED when connected
}

// Function to check Wi-Fi status and reconnect if needed
void checkWiFiStatus() {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("Wi-Fi connection lost. Reconnecting...");
    connectToWiFi();  // Reconnect to Wi-Fi
  }
}