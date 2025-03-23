#include "DHT.h"
#include <ESP8266WiFi.h>
#include <WiFiClient.h> 

#define DHT11_PIN 5      
#define DHT_TYPE DHT11   

//change it to your WiFi configuration!
const char* ssid = "your_ssid";         
const char* password = "your_password";         
const char* serverIP = "your_ip_address";       
const int serverPort = 5000;                  

DHT dht(DHT11_PIN, DHT_TYPE);

void setup() {
  Serial.begin(9600);
  dht.begin();

  WiFi.begin(ssid, password);
  Serial.print("Connecting to WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println(" Connected!");
}

void loop() {
  float humidity = dht.readHumidity();
  float temperature = dht.readTemperature();

  if (!isnan(humidity) && !isnan(temperature)) {
    Serial.print("Humidity: ");
    Serial.print(humidity);
    Serial.print("%RH | Temperature: ");
    Serial.print(temperature);
    Serial.println("°C ");
    if (WiFi.status() == WL_CONNECTED) {
      WiFiClient client;

      if (client.connect(serverIP, serverPort)) {
        Serial.println("Connected to server.");

        String url = "/data?humidity=" + String(humidity) + "&temperature=" + String(temperature);
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
        Serial.println("Failed to established connection.");
      }
    } else {
      Serial.println("No connection to WiFi.");
    }
  } else {
    Serial.println("Error reading data from DHT!");
  }

  delay(5000); 
}
