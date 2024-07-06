# FinalAssignment_Environmental-Monitoring-Data
Prepared By: Nur Ezurin Farisha binti Jusshairi (284253)


# Intelligent Environmental Monitoring System

## Overview

The **Intelligent Environmental Monitoring System** is a project designed to monitor environmental conditions such as temperature, humidity, and air quality using the ESP8266 microcontroller. The system gathers data from DHT11 and MQ135 sensors, processes this data to extract meaningful insights, and provides real-time visualization through a web-based interface. It also includes output control mechanisms to trigger alerts based on sensor data thresholds.

## Features

- **Real-time Data Collection**: Continuously monitors temperature, humidity, and air quality.
- **Data Processing**: Computes averages, minimum, and maximum values and identifies trends.
- **Data Storage**: Stores processed data in a MySQL database.
- **Output Control**: Activates LED and buzzer based on sensor thresholds.
- **Web-based Interface**: Provides real-time visualization and historical data trends.
- **Modern and Simple UI**: Clean and user-friendly interface for data display.


## Components Used

- **ESP8266**: The microcontroller used for processing and networking.
- **DHT11 Sensor**: Measures temperature and humidity.
- **MQ135 Sensor**: Monitors air quality.
- **LED**: Provides visual alerts.
- **Buzzer**: Emits sound alerts for critical air quality levels.
- **MySQL Database**: Stores sensor data for historical analysis.
- **PHP Scripts**: Handles data insertion and web interface.

## Getting Started

### Prerequisites

1. **Hardware**:
   - ESP8266 microcontroller
   - DHT11 temperature and humidity sensor
   - MQ135 air quality sensor
   - LED and resistor
   - Two-pin buzzer
   - Breadboard and jumper wires

2. **Software**:
   - Arduino IDE with ESP8266 support
   - XAMPP (or similar LAMP stack) for local MySQL and PHP server
   - Web browser for accessing the interface

### Installation

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/your-username/environmental-monitoring-system.git
   cd environmental-monitoring-system
   ```

2. **Setup MySQL Database**:
   - Start XAMPP and ensure Apache and MySQL are running.
   - Access phpMyAdmin at `http://localhost/phpmyadmin`.
   - Create a new database named `sensor_data`.
   - Run the following SQL command to create the `readings` table:
     ```sql
     CREATE TABLE readings (
       id INT AUTO_INCREMENT PRIMARY KEY,
       timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
       temperature FLOAT,
       humidity FLOAT,
       air_quality INT
     );
     ```

3. **Configure ESP8266**:
   - Open the `ESP8266_Environmental_Monitoring.ino` file in Arduino IDE.
   - Install the required libraries:
     - `ESP8266WiFi`
     - `ESP8266HTTPClient`
     - `DHT`
   - Modify the WiFi credentials and server URL in the code:
     ```cpp
     const char* ssid = "your_SSID";
     const char* password = "your_PASSWORD";
     const char* serverUrl = "http://your_server_ip/insert_data.php"; // Replace with your server's IP and path
     ```
   - Upload the code to the ESP8266.

4. **Deploy PHP Scripts**:
   - Copy the `insert_data.php` and `display_data.php` files to the `htdocs` directory of XAMPP.
   - Ensure the server URL in the ESP8266 code points to the `insert_data.php` file.

5. **Access the Web Interface**:
   - Open your web browser and navigate to `http://localhost/display_data.php`.

### Connections

| **Component**    | **Pin on Component** | **Connected to ESP8266 Pin** | **Description**                |
|------------------|----------------------|------------------------------|--------------------------------|
| **DHT11 Sensor** | VCC                  | 3.3V                         | Power Supply                   |
|                  | GND                  | GND                          | Ground                         |
|                  | Data                 | GPIO2 (D4)                   | Data signal for temperature and humidity |
| **MQ135 Sensor** | VCC                  | 3.3V                         | Power Supply                   |
|                  | GND                  | GND                          | Ground                         |
|                  | A0 (Analog Output)   | A0                           | Analog signal for air quality  |
|                  | D0 (Digital Output)  | Not connected (optional)     | Digital threshold output (not used) |
| **LED**          | Anode (+)            | GPIO14 (D5)                  | Positive terminal (with resistor) connected to control pin |
|                  | Cathode (-)          | GND                          | Negative terminal              |
| **Buzzer**       | Positive (+)         | GPIO12 (D6)                  | Control signal for sound output |
|                  | Negative (-)         | GND                          | Ground                         |

### Usage

1. **Data Monitoring**:
   - View real-time temperature, humidity, and air quality data on the web interface.
   - Observe the historical trends through interactive charts.

2. **Alerts**:
   - The LED lights up when the temperature exceeds 30 °C.
   - The buzzer sounds when the air quality reading is poor (greater than 700).

3. **Data Analysis**:
   - Insights such as average, minimum, and maximum values for each parameter are displayed alongside their respective charts.

### Testing

The system has undergone comprehensive testing to ensure it meets the functional requirements. Detailed testing results can be found in the `testing_report.md` file. Below are some highlights:

- **Temperature and Humidity Readings**: Verified against standard instruments.
- **Air Quality Measurement**: Cross-referenced with a baseline air quality monitor.
- **Data Storage**: Successfully stored sensor readings in the MySQL database.
- **Output Control**: LED and buzzer react correctly based on sensor data thresholds.
- **User Interface**: Real-time and historical data are displayed accurately on the web interface.

### Future Enhancements

1. **Advanced Data Analytics**: Incorporating machine learning algorithms to predict environmental trends.
2. **Cloud Integration**: Expanding to support cloud-based storage and remote monitoring.
3. **Mobile App Development**: Creating mobile applications for easier access and control.
4. **Energy Efficiency**: Implementing power-saving techniques for extended battery life.
5. **Additional Sensors**: Integrating more sensors for comprehensive environmental monitoring.
6. **Enhanced UI**: Improving the user interface with interactive features and customizable dashboards.
7. **Real-time Alerts**: Adding real-time notifications and automation based on sensor data.
