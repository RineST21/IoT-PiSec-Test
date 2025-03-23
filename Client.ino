#include "DHT.h"
#include <ESP8266WiFi.h>
#include <WiFiClient.h> 

#define DHT11_PIN 5      
#define DHT_TYPE DHT11   

const char* ssid = "CGA2121_8EgD8ah";         
const char* password = "X2EMZ5fZwZrBBnsCnQ";         
const char* serverIP = "192.168.0.18";       
const int serverPort = 5000;                  

DHT dht(DHT11_PIN, DHT_TYPE);

void setup() {
  Serial.begin(9600);
  dht.begin();

  WiFi.begin(ssid, password);
  Serial.print("Łączenie z WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println(" Połączono!");
}

void loop() {
  float humidity = dht.readHumidity();
  float temperature = dht.readTemperature();

  if (!isnan(humidity) && !isnan(temperature)) {
    Serial.print("Wilgotność: ");
    Serial.print(humidity);
    Serial.print("%RH | Temperatura: ");
    Serial.print(temperature);
    Serial.println("°C ");
    if (WiFi.status() == WL_CONNECTED) {
      WiFiClient client;

      if (client.connect(serverIP, serverPort)) {
        Serial.println("Połączono z serwerem.");

        String url = "/data?humidity=" + String(humidity) + "&temperature=" + String(temperature);
        client.print(String("GET ") + url + " HTTP/1.1\r\n" +
                     "Host: " + serverIP + "\r\n" +
                     "Connection: close\r\n\r\n");

        unsigned long timeout = millis();
        while (client.available() == 0) {
          if (millis() - timeout > 5000) {
            Serial.println(">>> Timeout klienta!");
            client.stop();
            return;
          }
        }

        while (client.available()) {
          String line = client.readStringUntil('\r');
          Serial.print(line);
        }

        client.stop();
        Serial.println("\nPołączenie zamknięte.");
      } else {
        Serial.println("Nie udało się połączyć z serwerem.");
      }
    } else {
      Serial.println("Brak połączenia WiFi.");
    }
  } else {
    Serial.println("Błąd odczytu z czujnika DHT!");
  }

  delay(5000); 
}