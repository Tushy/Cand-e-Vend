import pymysql
conn = pymysql.connect(host='localhost', user='root', password='vertrigo', db='idp')

#Insert één extensie in een tabel in de database
def insert_in_tabel(tabel, values):
    cursor = conn.cursor()
    sql = 'INSERT INTO' + tabel + 'values( ' + values + ')'
    cursor.execute(sql)
    conn.commit()
    cursor.close()

#Laat alle extensies in de console zien van een tabel in de database
def read_tabel_extensies(tabel):
    cursor = conn.cursor()
    sql = 'SELECT * FROM ' + tabel
    cursor.execute(sql)
    data = cursor.fetchall()
    for x in data:
        print(x)
    cursor.close()

#Pas een extensie aan in de database
def update_tabel_extensie(tabel, kolom, invoer, plaats):
    cursor = conn.cursor()
    sql = 'UPDATE ' + tabel + ' SET ' + kolom + '=' + invoer + ' WHERE ' + plaats
    cursor.execute(sql)
    conn.commit()
    cursor.close()

#Verwijder een extensie uit een tabel in de database
def delete_tabel_extensie(tabel, plaats):
    cursor = conn.cursor()
    sql = 'DELETE FROM ' + tabel + ' WHERE ' + plaats
    cursor.execute(sql)
    conn.commit()
    cursor.close()

# insert_in_tabel('`producten`', '4, "test", "Product 4", 7, 2.30')
read_tabel_extensies('`producten`')
read_tabel_extensies('`klanten`')
update_tabel_extensie('`producten`', 'product_code', '"hooi"', 'product_ID=4')
delete_tabel_extensie('`producten`', 'product_ID=4')
conn.close()
