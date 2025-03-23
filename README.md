# IoT-PiSec-Test
This project creates a weather monitoring system using the ESP8266 to collect data from the DHT11 and BMP280 sensors and the Raspberry Pi as a server for data storage and visualisation. It then tests its level of security at the level of database, communication etc...

##  Overview

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

### Server Side (Raspberry Pi)

* Python 3.x
* Virtual Environment
* Flask
* MySQl Server or another solution
* MySQL Connector for Python
* Apache www server

### Client Side (ESP8266)

* Arduino IDE
* ESP8266 board manager
* DHT sensor library
* BMP280 library
* WiFi library

## Setup Instruction

### ESP8266 ArduinoIDE Configuration 

To enable programming and uploading code to the ESP8266 NODEMCU V3, you need to import the appropriate board in the board manager:

1. Open Arduino IDE
2. Go to File -> Preferences
3. In the preferences window, find the "Additional boards manager URLs:" field
4. Add the following URL: http://arduino.esp8266.com/stable/package_esp8266com_index.json
5. Click OK to save the settings
6. Go to Tools -> Board -> Boards Manager
7. earch for "esp8266" and install the latest version of "ESP8266 by ESP8266 Community"
8. After installation, select Tools -> Board -> ESP8266 Boards -> NodeMCU 1.0 (ESP-12E Module)

### Installing Required Arduino Libraries

1. Open Arduino IDE
2. Go to Sketch -> Include Library -> Manage Libraries
3. Search for and install the following libraries:
  * DHT sensor library by Adafruit
  * Adafruit Unified Sensor
  * Adafruit BMP280 Library
  * ESP8266WiFi

## Setting Up the Raspberry Pi Server (database and www)

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








