#include "DHT.h"
#include <ESP8266WiFi.h>
#include <WiFiClient.h>
#include <Wire.h>
#include <Adafruit_BMP280.h>
#include <time.h> 

#define DHT11_PIN 0      
#define DHT_TYPE DHT11   

// change these to your WiFi configuration!
const char* ssid = "CGA2121_8EgD8ah";         
const char* password = "X2EMZ5fZwZrBBnsCnQ";         
const char* serverIP = "192.168.0.18";       
const int serverPort = 5000;              

DHT dht(DHT11_PIN, DHT_TYPE);
Adafruit_BMP280 bmp;  

const char* ntpServer1 = "pool.ntp.org";
const long  gmtOffset_sec = 0;
const int   daylightOffset_sec = 0;

void setup() {
  Serial.begin(9600);
  dht.begin();

  if (!bmp.begin(0x76)) {  
    Serial.println("BMP280 sensor not found. Check wiring!");
    while (1);
  }
  
  WiFi.begin(ssid, password);
  Serial.print("Connecting to WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println(" Connected!");

  configTime(gmtOffset_sec, daylightOffset_sec, ntpServer1);
}

void loop() {
  float humidity = dht.readHumidity();
  float temperature = dht.readTemperature();
  float pressure = bmp.readPressure() / 100.0F; 

  struct tm timeinfo;
  if (!getLocalTime(&timeinfo)) {
    Serial.println("Failed to obtain time");
    delay(1000);
    return;
  }
  char timeStr[25];
  strftime(timeStr, sizeof(timeStr), "%Y-%m-%d%%20%H:%M:%S", &timeinfo);  
  
  if (!isnan(humidity) && !isnan(temperature)) {
    Serial.print("Humidity: ");
    Serial.print(humidity);
    Serial.print("% | Temperature: ");
    Serial.print(temperature);
    Serial.print("°C | Pressure: ");
    Serial.print(pressure);
    Serial.println(" hPa");
    
    if (WiFi.status() == WL_CONNECTED) {
      WiFiClient client;
      
      if (client.connect(serverIP, serverPort)) {
        Serial.println("Connected to server.");

        String url = "/data?humidity=" + String(humidity) +
                     "&temperature=" + String(temperature) +
                     "&pressure=" + String(pressure) +
                     "&timestamp=" + String(timeStr);
                     
        client.print(String("GET ") + url + " HTTP/1.1\r\n" +
                     "Host: " + serverIP + "\r\n" +
                     "Connection: close\r\n\r\n");

        unsigned long timeout = millis();
        while (client.available() == 0) {
          if (millis() - timeout > 5000) {
            Serial.println(">>> Client timeout!");
            client.stop();
            return;
          }
        }

        while (client.available()) {
          String line = client.readStringUntil('\r');
          Serial.print(line);
        }

        client.stop();
        Serial.println("\nConnection closed.");
      } else {
        Serial.println("Failed to establish connection with server.");
      }
    } else {
      Serial.println("No WiFi connection.");
    }
  } else {
    Serial.println("Error reading data from DHT!");
  }

  delay(5000); 
}
