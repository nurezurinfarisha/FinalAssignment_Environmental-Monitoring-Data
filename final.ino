#include <ESP8266WiFi.h>  // Include the ESP8266 WiFi library
#include <DHT.h>          // Include the DHT sensor library

// WiFi credentials
const char* ssid = "Ezu";  // WiFi network SSID
const char* password = "ezu2142002";          // WiFi password

// Server URL (to be used in HTTP POST)
const char* serverUrl = "http://localhost/display_data.php"; // Replace with your server's IP and path

// GPIO Pins
#define DHTPIN D4     // DHT11 data pin connected to GPIO2 (D4)
#define DHTTYPE DHT11 // DHT sensor type (DHT11)
#define LEDPIN D5     // LED connected to GPIO14 (D5)
#define BUZZERPIN D6  // Buzzer connected to GPIO12 (D6)

// Create DHT sensor object
DHT dht(DHTPIN, DHTTYPE);

// Variables to store sensor data
float temperature;
float humidity;
int airQuality;

// Timing variables
unsigned long previousMillis = 0;
const long interval = 10000; // Data collection interval (10 seconds)

void setup() {
  Serial.begin(115200);  // Start serial communication for debugging
  
  // Initialize DHT sensor
  dht.begin();
  
  // Set GPIO modes
  pinMode(LEDPIN, OUTPUT);    // LED pin as output
  pinMode(BUZZERPIN, OUTPUT); // Buzzer pin as output
  
  // Initialize WiFi connection
  WiFi.begin(ssid, password);  // Connect to WiFi network
  
  // Wait for connection
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nWiFi connected");  // Confirm WiFi connection
  
  Serial.println("Setup complete");  // Initialization complete
}

void loop() {
  unsigned long currentMillis = millis();  // Get current time in milliseconds

  // Check if it's time to send data to the server
  if (currentMillis - previousMillis >= interval) {
    previousMillis = currentMillis;  // Update timing
    
    // Read sensor data
    readSensors();
    
    // Control outputs based on sensor readings
    controlOutputs();
    
    // Send sensor data to the server
    sendDataToServer();
  }
}

void readSensors() {
  // Read temperature and humidity from DHT11 sensor
  temperature = dht.readTemperature();  // Read temperature in Celsius
  humidity = dht.readHumidity();        // Read relative humidity
  
  // Read air quality from analog sensor (MQ135)
  airQuality = analogRead(A0);  // Read analog input from A0 pin
  
  // Print sensor readings for debugging
  Serial.print("Temperature: ");
  Serial.print(temperature);
  Serial.print(" °C, Humidity: ");
  Serial.print(humidity);
  Serial.print(" %, Air Quality: ");
  Serial.println(airQuality);
}

void controlOutputs() {
  // Control LED based on temperature threshold
  if (temperature > 20) {
    digitalWrite(LEDPIN, HIGH);  // Turn on LED if temperature is high
  } else {
    digitalWrite(LEDPIN, LOW);   // Otherwise, turn off LED
  }
  
  // Control buzzer based on air quality threshold
  if (airQuality > 700) {
    digitalWrite(BUZZERPIN, HIGH);  // Turn on buzzer if air quality is poor
  } else {
    digitalWrite(BUZZERPIN, LOW);   // Otherwise, turn off buzzer
  }
}

void sendDataToServer() {
  if (WiFi.status() == WL_CONNECTED) {  // Check if connected to WiFi
    WiFiClient client;  // Create WiFiClient object for server communication
    
    // Connect to the server (replace IP with your server's local IP)
    if (client.connect("192.168.50.58", 80)) {
      // Prepare data to send in HTTP POST format
      String postData = "temperature=" + String(temperature) + "&humidity=" + String(humidity) + "&air_quality=" + String(airQuality);
      
      // Send HTTP POST request
      client.println("POST /insert_data.php HTTP/1.1");  // Path to server script
      client.println("Host: 192.168.50.58");  // Replace with server's local IP
      client.println("Content-Type: application/x-www-form-urlencoded");
      client.println("Connection: close");
      client.println("Content-Length: " + String(postData.length()));
      client.println();
      client.println(postData);  // Send data
      
      // Print server response for debugging
      while (client.connected() || client.available()) {
        if (client.available()) {
          String line = client.readStringUntil('\n');
          Serial.println(line);  // Print server's response
        }
      }
      
      client.stop();  // Close connection with server
    } else {
      Serial.println("Connection to server failed");  // Failed to connect
    }
  } else {
    Serial.println("WiFi not connected");  // WiFi not connected
  }
}
