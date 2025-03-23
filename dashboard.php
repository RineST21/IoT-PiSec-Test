<?php
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit();
}

//Remember to change it!
$host = 'localhost';
$user = 'root';
$password = 'raspberry';
$dbname = 'sensordata';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection error: " . $conn->connect_error);
}

$dhtQuery = "SELECT Date, Temperature, Humidity FROM DHT11_measurement";
$dhtResult = $conn->query($dhtQuery);
if (!$dhtResult) {
    die("Error query (DHT11): " . $conn->error);
}

$data = [];
while ($row = $dhtResult->fetch_assoc()) {
    $data[$row['Date']] = $row;
    $data[$row['Date']]['Pressure'] = '';
}

$bmpQuery = "SELECT Date, Pressure FROM BMP280_measurement";
$bmpResult = $conn->query($bmpQuery);
if (!$bmpResult) {
    die("Error query (BMP280): " . $conn->error);
}

while ($row = $bmpResult->fetch_assoc()) {
    $date = $row['Date'];
    if (isset($data[$date])) {
        $data[$date]['Pressure'] = $row['Pressure'];
    } else {
        $data[$date] = [
            'Date' => $date,
            'Temperature' => '',
            'Humidity' => '',
            'Pressure' => $row['Pressure']
        ];
    }
}

ksort($data);
$conn->close();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard</title>
  <style>
    * {
      box-sizing: border-box;
      font-family: Arial, sans-serif;
    }
    body {
      background: #f0f2f5;
      margin: 0;
      padding: 20px;
    }
    .dashboard-container {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      max-width: 800px;
      margin: auto;
    }
    h1 {
      text-align: center;
      color: #333;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      padding: 12px;
      border-bottom: 1px solid #ddd;
    }
    th {
      background: #007BFF;
      color: #fff;
      text-align: center;
    }
    th.numeric_2 {
      text-align: left;
    }
    td.numeric {
      text-align: center;
    }
    tr:hover {
      background: #f1f1f1;
    }
    .logout {
      display: block;
      text-align: center;
      margin-top: 20px;
    }
    .logout button {
      background: #dc3545;
      color: #fff;
      padding: 10px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
      transition: background 0.3s;
    }
    .logout button:hover {
      background: #c82333;
    }
  </style>
</head>
<body>
  <div class="dashboard-container">
    <h1>Sensor DASHBOARD</h1>
    <table>
      <thead>
        <tr>
          <th class="numeric_2">Data</th>
          <th>Temperature (°C)</th>
          <th>Humidity (%RH)</th>
          <th>Pressure (hPa)</th>
        </tr>
      </thead>
      <tbody>
        <?php
          if (!empty($data)) {
              foreach ($data as $row) {
                  echo "<tr>";
                  echo "<td>" . htmlspecialchars($row['Date']) . "</td>";
                  echo "<td class='numeric'>" . htmlspecialchars($row['Temperature']) . "</td>";
                  echo "<td class='numeric'>" . htmlspecialchars($row['Humidity']) . "</td>";
                  echo "<td class='numeric'>" . htmlspecialchars($row['Pressure']) . "</td>";
                  echo "</tr>";
              }
          } else {
              echo "<tr><td colspan='4'>No data</td></tr>";
          }
        ?>
      </tbody>
    </table>
    <div class="logout">
      <form action="logout.php" method="post">
        <button type="submit">Logout</button>
      </form>
    </div>
  </div>
</body>
</html>
