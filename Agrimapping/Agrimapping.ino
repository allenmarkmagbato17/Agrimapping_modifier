#include <DHT.h>
#include <LiquidCrystal_I2C.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <Arduino_MultiWiFi.h>

int lcdColumns = 16;
int lcdRows = 2;

#define DHT11_PIN 13

DHT dht(DHT11_PIN, DHT11);
LiquidCrystal_I2C lcd(0x27, lcdColumns, lcdRows);


String serverName = "http://192.168.8.104/agrimapping3/agrimapping/inc/get_data.php";
//String serverName = "http://zdspgcagri.com/inc/get_data.php";

const char* ssid = "DEVICE";   
const char* password = "12345678";

void initWiFi() {
  MultiWiFi multi;
  multi.add(ssid, password);
  multi.add("DEVICE", "12345678");
  multi.add("ZDSPGC", "zdspgcpagadian");
  multi.add("Tender Care", "walakokabaloui");
  multi.add("B315_1E2AB", "5MNYE8DD79Y");
  while (multi.run() != WL_CONNECTED) {
    Serial.print(".");
  }
  if (multi.run() == WL_CONNECTED) {
    Serial.print("Successfully connected to network: ");
    Serial.println(WiFi.SSID());
  } else {
    Serial.println("Failed to connect to a WiFi network");
  }
}

void setup() {
  Serial.begin(9600);
  pinMode(33, INPUT_PULLUP);
  lcd.init();
  lcd.backlight();
  dht.begin();
  initWiFi();
}
 
int moisture(int sensor_pin) {
  int sensor_analog = analogRead(sensor_pin);
  int _moisture = ( 100 - ( (sensor_analog / 4095.00) * 100 ) );
  Serial.print(String(sensor_pin) + " Moisture = ");
  Serial.print(sensor_analog );
  Serial.println("%");
  return _moisture;
}

int modes = 1;

const int s1 = 36;
const int s2 = 39;
const int s3 = 34;
const int s4 = 35;
const int s5 = 32;

void loop() {
  int val_one = moisture(s1);
  int val_two = moisture(s2);
  int val_three = moisture(s3);
  int val_four = moisture(s4);
  int val_five = moisture(s5);

  int btn = digitalRead(33);
  String hum = String(dht.readHumidity());
  String temp = String(dht.readTemperature());

  if (btn == 0 ) {
    if (modes == 6) {
      modes = 1;
    } else {
      modes += 1;
    }
  }

  if (modes == 1) {
    lcd.setCursor(0, 0);
    lcd.print("Humidity:" + String(dht.readHumidity()));
    lcd.setCursor(0, 1);
    lcd.print("S1 Mois:" + String(val_one));
  } else if (modes == 2) {
    lcd.setCursor(0, 0);
    lcd.print("Humidity:" + String(dht.readHumidity()));
    lcd.setCursor(0, 1);
    lcd.print("S2 Mois:" + String(val_two));
  } else if (modes == 3) {
    lcd.setCursor(0, 0);
    lcd.print("Humidity:" + String(dht.readHumidity()));
    lcd.setCursor(0, 1);
    lcd.print("S3 Mois:" + String(val_three));
  } else if (modes == 4) {
    lcd.setCursor(0, 0);
    lcd.print("Humidity:" + String(dht.readHumidity()));
    lcd.setCursor(0, 1);
    lcd.print("S4 Mois:" + String(val_four));
  } else if (modes == 5) {
    lcd.setCursor(0, 0);
    lcd.print("Humidity:" + String(dht.readHumidity()));
    lcd.setCursor(0, 1 );
    lcd.print("S5 Mois:" + String(val_five));
  } else if (modes == 6) {
    lcd.setCursor(0, 0);
    lcd.print("Humidity:" + String(dht.readHumidity()));
    lcd.setCursor(0, 1 );
    lcd.print("Temperature:" + String(dht.readTemperature()));
  }

  sendDataToServer(val_one, val_two, val_three, val_four, val_five, hum, temp);
  delay(200);
  lcd.clear();
}

unsigned long previousMillis  = 0;
unsigned long currentMillis   = 0;
int interval = 20000;

void sendDataToServer(int s_one, int s_two, int s_three, int s_four, int s_five, String hum, String temp) {
  if ((millis() - previousMillis) > interval) {
    if (WiFi.status() == WL_CONNECTED) {
      HTTPClient http;
      String query = "?data";
      query += "&s_one=" + String(s_one);
      query += "&s_two=" + String(s_two);
      query += "&s_three=" + String(s_three);
      query += "&s_four=" + String(s_four);
      query += "&s_five=" + String(s_five);
      query += "&hum=" + String(hum);
      query += "&temp=" + String(temp);

      String serverPath = serverName + query;

      http.begin(serverPath.c_str());
      int httpResponseCode = http.GET();

      if (httpResponseCode > 0) {
        Serial.print("HTTP Response code: ");
        Serial.println(httpResponseCode);
        String payload = http.getString();
        Serial.println(payload);

        if (payload == "Data inserted successfully.") {
          Serial.println("Data saved successfully.");
          lcd.setCursor(0, 0);
          lcd.print("Data saved!");
          delay(2000); // Display message for 2 seconds
          lcd.clear();
        }
      } else {
        Serial.print("Error code: ");
        Serial.println(httpResponseCode);
      }
      http.end();
    } else {
      Serial.println("WiFi Disconnected");
    }
    previousMillis = millis();
  }
}