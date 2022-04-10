#!/bin/python3
from flask import Flask, request
from flask_cors import CORS, cross_origin
import mysql.connector
import datetime
import gladiator as gl

app = Flask(__name__)
cors = CORS(app)
app.config['CORS_HEADERS'] = 'Content-Type'
SERVER_VERSION = "0.1.0"

@app.route("/", methods=['GET'])
@cross_origin()
def api_root():
    # Return api info
    return {
        "verision": SERVER_VERSION,
    }

@app.route("/nutrient_record.post", methods=['POST'])
@cross_origin()
def api_post_nutrient_record():
    # Recieve data
    data = request.json
    print(f"Nutrient data recieved: {data}")

    # Return error if validation fails
    error = {
        "status": "fail"
    }
    validations = (
        ('percent', gl.required),
        ('gallons', gl.required),
        ('week', gl.required, gl.type_(int)),
        ('calimagic', gl.required, gl.type_(bool)),
        ('ph_up', gl.required),
        ('ph_down', gl.required),
        ('plants', gl.required, gl.type_(list)),
    )
    # Validate data
    result = gl.validate(validations, data)
    if not result:
        print("Nutrient data validation failed")
        return error

    # Connect to database
    db = mysql.connector.connect(
        host="localhost",
        user="grow",
        password="helloworld",
        database="grow_test"
    )
    cursor = db.cursor()

    # Insert record into database
    cursor.execute('''insert into nutrient_data (plant_id,timestamp,gallons,replace_water,percent,week_number,ph_up,ph_down,calimagic)
    values (%s,%s,%s,%s,%s,%s,%s,%s,%s)''',
    (data['plants'][0][0], datetime.datetime.now(), data['gallons'], data['plants'][0][1], data['percent'], data['week'], data['ph_up'],
    data['ph_down'], data['calimagic']))
    db.commit()

    # Return success
    return {
        "status": "success"
    }
