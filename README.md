# IoT-PiSec-Test

This project creates a weather monitoring system using the ESP8266 to collect data from the DHT11 and BMP280 sensors and the Raspberry Pi as a server for data storage and visualisation.

## Overview

The system collects temperature, humidity, and pressure data using an ESP8266 microcontroller connected to DHT11 and BMP280 sensors. The data is transmitted to a Raspberry Pi server which stores it in a MariaDB database and provides a web interface for visualization.

## Requirements:

### Hardware
* Raspberry Pi (any model) or similar device to use as a server
* ESP8266 NodeMCU V3 microcontroller
* DHT11 temperature and humidity sensor
* BMP280 barometric pressure and temperature sensor
* Connecting wires
* Power supply for Raspberry Pi and ESP8266

### Software

#### Server Side (Raspberry Pi)
* Python 3.x
* Virtual Environment
* Flask
* MySQl Server or another solution
* MySQL Connector for Python
* Apache server

#### Client Side (ESP8266)
* Arduino IDE
* ESP8266 board manager
* DHT sensor library
* BMP280 library
* WiFi library

## Setup Instructions

### ESP8266 ArduinoIDE Configuration
To enable programming and uploading code to the ESP8266 NODEMCU V3, you need to import the appropriate board in the board manager:
1. Open Arduino IDE
2. Go to File -> Preferences
3. In the preferences window, find the "Additional boards manager URLs:" field
4. Add the following URL: http://arduino.esp8266.com/stable/package_esp8266com_index.json
5. Click OK to save the settings
6. Go to Tools -> Board -> Boards Manager
7. Search for "esp8266" and install the latest version of "ESP8266 by ESP8266 Community"
8. After installation, select Tools -> Board -> ESP8266 Boards -> NodeMCU 1.0 (ESP-12E Module)

### Installing CP210x USB to UART Driver
To enable communication between your computer and the ESP8266:
1. Download the appropriate CP210x USB to UART Bridge VCP driver for your operating system from the Silicon Labs website: https://www.silabs.com/developers/usb-to-uart-bridge-vcp-drivers
2. Install the driver according to your operating system's procedure
3. Restart your computer if necessary
4. Connect your ESP8266 and check if it appears in the Device Manager (Windows) or using `ls /dev/tty*` (Mac/Linux)
5. In Arduino IDE, select the correct COM port under Tools -> Port

### Installing Required Arduino Libraries
1. Open Arduino IDE
2. Go to Sketch -> Include Library -> Manage Libraries
3. Search for and install the following libraries:
  * DHT sensor library by Adafruit
  * Adafruit Unified Sensor
  * Adafruit BMP280 Library
  * ESP8266WiFi

## Setting Up the Raspberry Pi Server (database and www)

### Finding Your Raspberry Pi's IP Address
To connect to your Raspberry Pi, you'll need to know its IP address:

1. Connect your Raspberry Pi to your network (via Ethernet or WiFi)
2. On the Raspberry Pi, open a terminal and type:
   ```
   hostname -I
   ```
   This will display the IP address(es) assigned to your Raspberry Pi

Alternatively, you can check your router's connected devices list or use network scanning tools like:
```
sudo apt install nmap
nmap -sn 192.168.1.0/24  # Replace with your network range
```

### Connecting to Raspberry Pi via SSH
You can connect to your Raspberry Pi remotely using:

#### Using PuTTY (Windows)
1. Download and install PuTTY from: https://www.putty.org/
2. Open PuTTY
3. Enter your Raspberry Pi's IP address in the "Host Name" field
4. Keep the port as 22
5. Click "Open"
6. Enter your Raspberry Pi username (default: pi) and password when prompted

#### Using VSCode SSH Extension
1. Install the "Remote - SSH" extension in Visual Studio Code
2. Click on the green icon in the bottom-left corner of VSCode
3. Select "Remote-SSH: Connect to Host..."
4. Click "+ Add New SSH Host..."
5. Enter `ssh username@raspberry_pi_ip_address` (e.g., `ssh pi@192.168.1.100`)
6. Select a configuration file to update
7. Click "Connect" and enter your password when prompted

#### Using Terminal (Mac/Linux)
```
ssh username@raspberry_pi_ip_address
```

### Setting Up the Server
1. Install the required system packages:
```
sudo apt update
sudo apt upgrade
sudo apt update
sudo apt install apache2 -y
sudo apt install php -y
sudo apt install phpmyadmin
sudo service apache2 restart
```

2. Create a database
```
sudo apt install mysql-server php-mysql -y
sudo mysql --user=root
CREATE USER 'put_yor_username'@'localhost' IDENTIFIED BY 'create_your_password';
GRANT ALL PRIVILEGES ON *.* TO 'your_username'@'localhost';
mysql --user=your_username -p
```

### Creating the Database Structure
Follow these steps to create the required database and tables for the project:

1. Log in to MySQL:
```
mysql -u your_username -p
```

2. Create the database:
```
CREATE DATABASE sensordata;
USE sensordata;
```

3. Create the required tables:

```sql
-- Table for BMP280 pressure sensor measurements
CREATE TABLE `BMP280_measurement` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Date` timestamp NULL DEFAULT current_timestamp(),
  `Pressure` float NOT NULL,
  PRIMARY KEY (`ID`)
) 

-- Table for DHT11 temperature and humidity sensor measurements
CREATE TABLE `DHT11_measurement` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Date` timestamp NULL DEFAULT current_timestamp(),
  `Temperature` float NOT NULL,
  `Humidity` float NOT NULL,
  PRIMARY KEY (`ID`)
)

-- Table for user authentication
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) 
```

4. Add an initial admin user (for testing purposes only, change this in production):
```sql
INSERT INTO `users` (`username`, `password`) VALUES ('admin', 'admin');
```

5. Verify the tables were created correctly:
```sql
SHOW TABLES;
DESCRIBE BMP280_measurement;
DESCRIBE DHT11_measurement;
DESCRIBE users;
```

Alternatively, you can also use phpMyAdmin to create the database and tables:

1. Open a web browser and navigate to http://[Raspberry_Pi_IP]/phpmyadmin
2. Log in with your MySQL username and password
3. Click "New" in the left sidebar to create a new database
4. Enter "sensordata" as the database name and click "Create"
5. Select the "sensordata" database from the left sidebar
6. Click the "SQL" tab
7. Copy and paste the SQL commands above into the SQL query window
8. Click "Go" to execute the queries

**Note on Security**: The current user table stores passwords in plain text, which is not secure for production. In a real-world application, you should use proper password hashing (e.g., bcrypt or Argon2).

3. Create a website structure
```
sudo mkdir /var/www/html/website
cd /var/www/html/website
sudo nano index.php
sudo nano dashboard.php
sudo nano logout.php
sudo ln -s /usr/share/phpmyadmin /var/www/html/phpmyadmin
```  

4. Set up a virtual environment:
```
python3 -m /var/www/html/website .venv
source /var/www/html/website/.venv/bin/activate
```

5. Install Python dependencies:
```
pip install Flask
pip install flask mysql-connector-python
```

## USAGE
1. Upload the Arduino sketch to ESP8266
2. Start the Flask server on Raspberry Pi
3. Access the web interface at http://[Raspberry_Pi_IP]:5000

## Troubleshooting

### ESP8266 Connection Issues
* Ensure the CP210x USB to UART driver is installed on your computer
* Check if the correct COM port is selected in Arduino IDE
* Verify the power supply is adequate

### Server Issues
* Check if MySQL service is running
* Ensure the Flask application has the correct database credentials
* Verify the ESP8266 can reach the Raspberry Pi's IP address

### Database Issues
* If you can't connect to the database, check if the MySQL service is running: `sudo systemctl status mysql`
* Verify the database exists: `mysql -u username -p -e "SHOW DATABASES;"`
* Check if the required tables exist: `mysql -u username -p -e "USE sensordata; SHOW TABLES;"`
* Ensure the Flask application has the correct database credentials in its configuration
