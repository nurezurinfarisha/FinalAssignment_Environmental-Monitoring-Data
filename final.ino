#include <ESP8266WiFi.h>
#include <DHT.h>

// WiFi credentials
const char* ssid = "Ezu";
const char* password = "ezu2142002";

// Server URL (to be used in HTTP POST)
const char* serverUrl = "http://localhost/display_data.php"; // Replace with your server's IP and path

// GPIO Pins
#define DHTPIN D4  // DHT11 data pin connected to GPIO2 (D4)
#define DHTTYPE DHT11
#define LEDPIN D5  // LED connected to GPIO14 (D5)
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
  Serial.begin(115200);
  
  // Initialize DHT sensor
  dht.begin();
  
  // Set GPIO modes
  pinMode(LEDPIN, OUTPUT);
  pinMode(BUZZERPIN, OUTPUT);
  
  // Initialize WiFi
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nWiFi connected");

  Serial.println("Setup complete");
}

void loop() {
  unsigned long currentMillis = millis();

  if (currentMillis - previousMillis >= interval) {
    previousMillis = currentMillis;
    
    // Read data from sensors
    readSensors();
    
    // Control outputs based on data
    controlOutputs();
    
    // Send data to the server
    sendDataToServer();
  }
}

void readSensors() {
  // Read temperature and humidity from DHT11
  temperature = dht.readTemperature();
  humidity = dht.readHumidity();
  
  // Read air quality from MQ135 (analog value)
  airQuality = analogRead(A0);
  
  // Print readings for debugging
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
    digitalWrite(LEDPIN, HIGH); // Turn on LED if temperature is high
  } else {
    digitalWrite(LEDPIN, LOW);
  }
  
  // Control buzzer based on air quality threshold
  if (airQuality > 700) {
    digitalWrite(BUZZERPIN, HIGH); // Turn on buzzer if air quality is poor
  } else {
    digitalWrite(BUZZERPIN, LOW); // Turn off buzzer
  }
}

void sendDataToServer() {
  if (WiFi.status() == WL_CONNECTED) { // Check if connected to the WiFi
    WiFiClient client; // Create WiFiClient object
    
    // Change server to the IP address of your computer on the local network
    if (client.connect("192.168.50.58", 80)) { // Replace with your computer's local IP address
      // Prepare the data to send
      String postData = "temperature=" + String(temperature) + "&humidity=" + String(humidity) + "&air_quality=" + String(airQuality);
      
      // Send the HTTP POST request
      client.println("POST /insert_data.php HTTP/1.1"); // Relative path to the script
      client.println("Host: 192.168.50.58"); // Replace with your computer's local IP address
      client.println("Content-Type: application/x-www-form-urlencoded");
      client.println("Connection: close");
      client.println("Content-Length: " + String(postData.length()));
      client.println();
      client.println(postData);
      
      // Print the server response for debugging
      while (client.connected() || client.available()) {
        if (client.available()) {
          String line = client.readStringUntil('\n');
          Serial.println(line); // Print the response from the server
        }
      }
      
      client.stop(); // Close the connection
    } else {
      Serial.println("Connection to server failed");
    }
  } else {
    Serial.println("WiFi not connected");
  }
}
