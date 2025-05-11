/**
   BasicHTTPClient.ino

    Created on: 24.05.2015

*/

#include <Arduino.h>

#include <ESP8266WiFi.h>
#include <ESP8266WiFiMulti.h>

#include <ESP8266HTTPClient.h>

#include <WiFiClient.h>

#include "DHT.h"

/************ Global State (you don't need to change this!) ******************/
#define DHTTYPE DHT11   // DHT 11
#define DHTPIN D3        // broche ou l'on a branche le capteur
DHT dht(DHTPIN, DHTTYPE); //déclaration du capteur

ESP8266WiFiMulti WiFiMulti;

void setup() {

  Serial.begin(115200);
  // Serial.setDebugOutput(true);

  Serial.println();
  Serial.println();
  Serial.println();

  for (uint8_t t = 4; t > 0; t--) {
    Serial.printf("[SETUP] WAIT %d...\n", t);
    Serial.flush();
    delay(1000);
  }

  WiFi.mode(WIFI_STA);
  WiFiMulti.addAP("Freebox-23ED2F", "493mfq7kf2qb3dntqckdb2");
}

void loop() {

  float h = dht.readHumidity();//on lit l'hygrometrie
  float t = dht.readTemperature();//on lit la temperature en celsius (par defaut)
  String valeur = "?temperature=" + String(t) + "&humidite=" + String(h);
  String url = "http://192.168.1.136/projectiotY.HamzaS.Hamza/siteweb/get.php";
  String requette = url + valeur;

  // verifie la connection wifi
  if ((WiFiMulti.run() == WL_CONNECTED)) {
    WiFiClient client;
    HTTPClient http;

    Serial.print("[HTTP] commence requête GET !\n");
      if (http.begin(client, requette)) { 

      Serial.print("[HTTP] requête GET reussie !\n");

      // requête HTTP GET est envoyée au serveur web
      int httpCode = http.GET();

      // si le code de HTTP est positif, c'est que la communication avec le serveur est etablie
      if (httpCode > 0) {
        // HTTP header has been send and Server response header has been handled
        Serial.printf("[HTTP] GET... code: %d\n", httpCode);

        // file found at server
        if (httpCode == HTTP_CODE_OK || httpCode == HTTP_CODE_MOVED_PERMANENTLY) {
          String payload = http.getString();
          Serial.println(payload);
        }
      } else {
        Serial.printf("[HTTP] GET... failed, error: %s\n", http.errorToString(httpCode).c_str());
      }

      http.end();
    } else {
      Serial.println("[HTTP] Unable to connect");
    }
  }
  delay(5000);
}
