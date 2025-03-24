from flask import Flask, request
import mysql.connector
from datetime import datetime

app = Flask(__name__)

//Remember to change it!
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
    
    if temperature and humidity:
        try:
            humidity = float(humidity)
            temperature = float(temperature)
            timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")

            conn = mysql.connector.connect(**db_config)
            c = conn.cursor()
            c.execute("INSERT INTO DHT11_measurement (Date, Temperature, Humidity) VALUES (%s, %s, %s)",
                      (timestamp, temperature, humidity))
            conn.commit()
            conn.close()

            print(f"Received: timestamp={timestamp}, temperature={temperature}, humidity={humidity}")
            return "Data saved successfully", 200
        except Exception as e:
            print(f"Error: {e}")
            return str(e), 500
    else:
        return "Missing parameters", 400

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
