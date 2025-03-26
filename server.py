from flask import Flask, request
import mysql.connector

app = Flask(__name__)

# Remember to change it!
db_config = {
    'user': 'root',      
    'password': 'raspberry',      
    'host': 'localhost',            
    'database': 'sensordata',      
    'port': 3306                    
}

@app.route('/data', methods=['GET'])
def receive_data():
    temperature = request.args.get('temperature')
    humidity = request.args.get('humidity')
    pressure = request.args.get('pressure')
    timestamp = request.args.get('timestamp')
    
    if temperature and humidity and pressure:
        try:
            humidity = float(humidity)
            temperature = float(temperature)
            pressure = float(pressure)
            
            conn = mysql.connector.connect(**db_config)
            c = conn.cursor()
            
            c.execute("INSERT INTO DHT11_measurement (Date, Temperature, Humidity) VALUES (%s, %s, %s)",
                      (timestamp, temperature, humidity))
            
            c.execute("INSERT INTO BMP280_measurement (Date, Pressure) VALUES (%s, %s)",
                      (timestamp, pressure))
            
            conn.commit()
            conn.close()

            print(f"Received: timestamp={timestamp}, temperature={temperature}, humidity={humidity}, pressure={pressure}")
            return "Data saved successfully", 200
        except Exception as e:
            print(f"Error: {e}")
            return str(e), 500
    else:
        return "Missing parameters", 400

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
