#!/bin/python3

# Script used for importing data from sqlite3 databases into mysql

import mysql.connector, sqlite3
from datetime import datetime

sdb = mysql.connector.connect(
  host="localhost",
  user="grow",
  password="helloworld",
  database="grow_test"
)

# weeks = {}
sc = sdb.cursor()
# sc.execute('select * from nutrient_schedule')
# for row in sc.fetchall():
#     number = row[0]
#     id = row[1]
#     weeks[id] = number
# print(weeks)

def conv_row(i, row):
  row = list(row)
  row[0] = datetime.fromtimestamp(row[0])
  row = tuple([i + 1] + row)
  return row

files = ['000.db', '001.db', '002.db', '003.db', '004.db', '005.db', '006.db', '007.db']
for i, file in enumerate(files):
  print(f'Converting table {file}...')
  hdb = sqlite3.connect(file)
  hc = hdb.cursor()

  print('Converting events...')
  hc.execute('select * from events')
  events = list(hc.fetchall())
  for row in events:
    row = conv_row(i, row)
    sc.execute('''insert into events (plant_id,timestamp,event)
    values (%s,%s,%s)''', row)
    print(row)

  print('Converting comments...')
  hc.execute('select * from comments')
  comments = list(hc.fetchall())
  for row in comments:
    row = conv_row(i, row)
    sc.execute('''insert into comments (plant_id,timestamp,comment)
    values (%s,%s,%s)''', row)
    print(row)

  # hc.execute('select * from data')
  # data = list(hc.fetchall())
  # for row in data:
  #   row = list(row)
  #   row[0] = datetime.fromtimestamp(row[0])
  #   row[4] = weeks[row[4]]
  #   row = tuple([i + 1] + row)
  #   sc.execute('''insert into nutrient_data (plant_id,timestamp,gallons,
  #   replace_water,percent,week_number,ph_up,ph_down,calimagic)
  #   values (%s,%s,%s,%s,%s,%s,%s,%s,%s)''', row)
  #   print(row)

  hc.close()
  hdb.close()

sdb.commit()
sc.close()
sdb.close()

