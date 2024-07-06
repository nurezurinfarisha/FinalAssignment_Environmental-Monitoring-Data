<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Environmental Monitoring System</title>
    <style>
        /* Styling for the body */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        /* Styling for the main content container */
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }
        /* Styling for the heading */
        h1 {
            text-align: center;
            color: #444;
        }
        /* Styling for the table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
        /* Styling for insights section */
        .insights {
            margin-top: 20px;
        }
        .insights p {
            font-size: 18px;
            line-height: 1.5;
            margin: 5px 0;
        }
        /* Styling for chart containers */
        .chart-container {
            margin-bottom: 30px;
            padding: 10px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
    <!-- Include Chart.js library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <h1>Environmental Monitoring Data</h1>
        <?php
        // Database configuration
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "sensor_data";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Fetch data from the database
        $sql = "SELECT * FROM readings ORDER BY timestamp DESC";
        $result = $conn->query($sql);

        // Initialize variables for insights
        $totalRecords = 0;
        $tempSum = 0;
        $humiditySum = 0;
        $airQualitySum = 0;
        $tempMin = PHP_FLOAT_MAX;
        $tempMax = PHP_FLOAT_MIN;
        $humidityMin = PHP_FLOAT_MAX;
        $humidityMax = PHP_FLOAT_MIN;
        $airQualityMin = PHP_INT_MAX;
        $airQualityMax = PHP_INT_MIN;

        // Arrays to store data for charts
        $timestamps = [];
        $temperatures = [];
        $humidities = [];
        $airQualities = [];

        if ($result->num_rows > 0) {
            // Display table headers
            echo "<table>";
            echo "<tr><th>Timestamp</th><th>Temperature (°C)</th><th>Humidity (%)</th><th>Air Quality</th></tr>";

            // Process each row of data
            while($row = $result->fetch_assoc()) {
                // Display data rows in the table
                echo "<tr><td>" . $row["timestamp"] . "</td><td>" . $row["temperature"] . "</td><td>" . $row["humidity"] . "</td><td>" . $row["air_quality"] . "</td></tr>";

                // Calculate insights: total records, sums, min/max values
                $totalRecords++;
                $tempSum += $row["temperature"];
                $humiditySum += $row["humidity"];
                $airQualitySum += $row["air_quality"];
                
                if ($row["temperature"] < $tempMin) $tempMin = $row["temperature"];
                if ($row["temperature"] > $tempMax) $tempMax = $row["temperature"];
                if ($row["humidity"] < $humidityMin) $humidityMin = $row["humidity"];
                if ($row["humidity"] > $humidityMax) $humidityMax = $row["humidity"];
                if ($row["air_quality"] < $airQualityMin) $airQualityMin = $row["air_quality"];
                if ($row["air_quality"] > $airQualityMax) $airQualityMax = $row["air_quality"];

                // Store data for charts in arrays
                $timestamps[] = $row["timestamp"];
                $temperatures[] = $row["temperature"];
                $humidities[] = $row["humidity"];
                $airQualities[] = $row["air_quality"];
            }
            echo "</table>";

            // Calculate average values
            $averageTemp = $tempSum / $totalRecords;
            $averageHumidity = $humiditySum / $totalRecords;
            $averageAirQuality = $airQualitySum / $totalRecords;
        } else {
            // Display message if no data available
            echo "<p>No data available</p>";
        }

        $conn->close();
        ?>

        <!-- Temperature chart container -->
        <div class="chart-container">
            <h2>Temperature Trends</h2>
            <canvas id="temperatureChart"></canvas>
            <!-- Insights section for temperature -->
            <div class="insights">
                <p><strong>Average:</strong> <?php echo round($averageTemp, 2); ?> °C</p>
                <p><strong>Min:</strong> <?php echo $tempMin; ?> °C</p>
                <p><strong>Max:</strong> <?php echo $tempMax; ?> °C</p>
            </div>
        </div>

        <!-- Humidity chart container -->
        <div class="chart-container">
            <h2>Humidity Trends</h2>
            <canvas id="humidityChart"></canvas>
            <!-- Insights section for humidity -->
            <div class="insights">
                <p><strong>Average:</strong> <?php echo round($averageHumidity, 2); ?> %</p>
                <p><strong>Min:</strong> <?php echo $humidityMin; ?> %</p>
                <p><strong>Max:</strong> <?php echo $humidityMax; ?> %</p>
            </div>
        </div>

        <!-- Air Quality chart container -->
        <div class="chart-container">
            <h2>Air Quality Trends</h2>
            <canvas id="airQualityChart"></canvas>
            <!-- Insights section for air quality -->
            <div class="insights">
                <p><strong>Average:</strong> <?php echo round($averageAirQuality, 2); ?></p>
                <p><strong>Min:</strong> <?php echo $airQualityMin; ?></p>
                <p><strong>Max:</strong> <?php echo $airQualityMax; ?></p>
            </div>
        </div>
    </div>

    <script>
        // JavaScript section for chart creation using Chart.js
        // Prepare data for charts using PHP-generated arrays
        const timestamps = <?php echo json_encode($timestamps); ?>;
        const temperatures = <?php echo json_encode($temperatures); ?>;
        const humidities = <?php echo json_encode($humidities); ?>;
        const airQualities = <?php echo json_encode($airQualities); ?>;

        // Create Temperature Chart
        new Chart(document.getElementById('temperatureChart'), {
            type: 'line',
            data: {
                labels: timestamps,
                datasets: [{
                    label: 'Temperature (°C)',
                    data: temperatures,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    fill: true
                }]
            },
            options: {
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Timestamp'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Temperature (°C)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true
                    }
                }
            }
        });

        // Create Humidity Chart
        new Chart(document.getElementById('humidityChart'), {
            type: 'line',
            data: {
                labels: timestamps,
                datasets: [{
                    label: 'Humidity (%)',
                    data: humidities,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    fill: true
                }]
            },
            options: {
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Timestamp'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Humidity (%)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true
                    }
                }
            }
        });

        // Create Air Quality Chart
        new Chart(document.getElementById('airQualityChart'), {
            type: 'line',
            data: {
                labels: timestamps,
                datasets: [{
                    label: 'Air Quality',
                    data: airQualities,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: true
                }]
            },
            options: {
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Timestamp'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Air Quality'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true
                    }
                }
            }
        });
    </script>
</body>
</html>
