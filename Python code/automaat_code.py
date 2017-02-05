from reportlab.pdfgen import canvas
from reportlab.lib import colors
from PIL import Image
import datetime
import pymysql
import time
import os
import zbarlight

def read_bestellingen(qrcode):
    'Lees tabel bestellingen en pak de bestelling met overeenkomende qr-code'
    cursor = conn.cursor()
    sql = 'SELECT * FROM `bestellingen`'
    cursor.execute(sql)
    data = cursor.fetchall()
    for x in data:
        if x[4] == qrcode:
            return x
    cursor.close()

def read_product(productid):
    'Lees tabel producten en pak de producten die overeenkomen met het product_id van de bestelproducten(bestelling tabel)'
    cursor = conn.cursor()
    sql = 'SELECT * FROM `producten`'
    cursor.execute(sql)
    data = cursor.fetchall()
    for x in data:
        if x[0] == productid:
            return x
    cursor.close()

def read_klanten(klantnummer):
    'Lees tabel klanten en pak de klant waar het klantnummer overeenkomt met het klantnummer uit de bestelling'
    cursor = conn.cursor()
    sql = 'SELECT * FROM `klanten`'
    cursor.execute(sql)
    data = cursor.fetchall()
    for x in data:
        if x[0] == klantnummer:
            return x
    cursor.close()

def read_bestelproducten(bestelnummer):
    'Lees de tabel bestelling en pak de producten waar het bestelnummer overeenkomt met de bestelling'
    cursor = conn.cursor()
    sql = 'SELECT * FROM `bestelling`'
    cursor.execute(sql)
    data = cursor.fetchall()
    producten = []
    for x in data:
        if x[0] == bestelnummer:
            producten.append(x)
    cursor.close()
    return producten

def genereer_pdf(c):
    'Deze functie genereert een factuur in een pdf bestand'
    bestelling = read_bestellingen(qrcode)
    bestelproducten = read_bestelproducten(bestelling[0])
    if len(bestelproducten) == 2:
        product1 = read_product(bestelproducten[0][1])
        product2 = read_product(bestelproducten[1][1])
        totaal = bestelproducten[0][2]*product1[4]
        totaal2 = bestelproducten[1][2]*product2[4]
        subtotaal = totaal + totaal2
    else:
        product1 = read_product(bestelproducten[0][1])
        totaal = bestelproducten[0][2]*product1[4]
        subtotaal = totaal

    klant = read_klanten(bestelling[2])
    klantnaam = str(klant[3]+ ' ' +klant[4])

    vandaag = datetime.date.today()

    c.drawString(50,800,'SNOEP4ALL')
    c.drawString(50,780, 'Daltonlaan 200')
    c.drawString(50,760, '3584 BJ  Utrecht')
    c.drawString(50,740, 'TEL: +316 46 30 48 64')

    c.drawInlineImage('Cand-E-Vend.png', 250, 750,350,50)

    c.setFillColor(colors.lightblue)
    c.setStrokeColor(colors.lightblue)
    c.rect(20, 550, 550, 20, fill=1)

    c.setFillColor(colors.lightblue)
    c.setStrokeColor(colors.lightblue)
    c.rect(20, 670, 550, 20, fill=1)

    c.setStrokeColor('black')
    c.line(20, 700, 570, 700)
    #c.line(50, 610, 550, 610)

    c.setFillColor(colors.lightblue)
    c.setStrokeColor(colors.lightblue)
    c.rect(20, 444, 550, 20, fill=1)

    c.setFillColor('black')
    c.drawString(50, 675, 'KLANTGEGEVENS')
    c.drawString(480, 675, str(vandaag))

    c.setFillColor('black')
    c.drawString(50, 650, 'Bestelnummer: ')
    c.drawString(50, 630, 'Naam: ')
    c.drawString(50, 555, 'Aantal')
    c.drawString(150, 555, 'Productnaam')
    c.drawString(310, 555, 'Prijs per stuk')
    c.drawString(480, 555, 'Totaal')

    c.drawString(150, 648, str(bestelling[0]))
    c.drawString(150, 628, klantnaam)
    c.drawString(50, 535, str(bestelproducten[0][2]))
    c.drawString(150, 535, str(product1[2]))
    c.drawString(310, 535, str(product1[4]))
    c.drawString(480, 535, str(totaal))

    if len(bestelproducten) == 2:
        c.drawString(50, 515, str(bestelproducten[1][2]))
        c.drawString(150, 515, str(product2[2]))
        c.drawString(310, 515, str(product2[4]))
        c.drawString(480, 515, str(totaal2))

    c.drawString(480, 450, 'Subtotaal')
    c.drawString(480, 430, str(subtotaal))

    print('bestelling voltooid!')
    time.sleep(2)

def read_qrcode():
    while True:
        'Gebruik fswebcam om een afbeelding te maken en op te slaan als image2.jpg'
        os.system('fswebcam -r 1280x720 --no-banner image2.jpg')

        'Geef het pad aan waar de afbeelding is opgeslagen.'
        file_path = '/home/pi/image2.jpg'
        with open(file_path, 'rb') as image_file:
            image = Image.open(image_file)
            image.load()

        'Gebruik zbarlight om de qrcode te lezen en op te slaan in de variable `codes`'
        codes = zbarlight.scan_codes('qrcode', image)
        if codes != None:
            print('QR codes: %s' % codes)
            return codes[0]
            break
        else:
            print('Geen geldige code')

def update_extensie(tabel, kolom, invoer, plaats):
    'verander een waarde in een extensie'
    cursor = conn.cursor()
    sql = 'UPDATE ' + tabel + ' SET ' + kolom + '=' + invoer + ' WHERE ' + plaats
    cursor.execute(sql)
    conn.commit()
    cursor.close()

def delete_bestelling(tabel, plaats):
    'verwijder een extensie uit een tabel'
    cursor = conn.cursor()
    sql = 'DELETE FROM ' + tabel + ' WHERE ' + plaats
    cursor.execute(sql)
    conn.commit()
    cursor.close()

def naam_pdf():
    'Geef het pdf bestand een naam met bijbehorend bestelnummer'
    bestelling = read_bestellingen(qrcode)
    naam = bestelling[0]
    c = canvas.Canvas('Bestelling ' + str(naam) + '.pdf')
    return c

def update_database():
    'Deze functie updatet de database extensies'
    bestelling = read_bestellingen(qrcode)
    bestelproducten = read_bestelproducten(bestelling[0])
    if len(bestelproducten) == 2:
        product1 = read_product(bestelproducten[0][1])
        product2 = read_product(bestelproducten[1][1])
        totaal = bestelproducten[0][2]*product1[4]
        totaal2 = bestelproducten[1][2]*product2[4]
        subtotaal = totaal + totaal2
        invoer1 = product1[3] - bestelproducten[0][2]
        invoer2 = product2[3] - bestelproducten[1][2]
        update_extensie('`producten`', '`voorraad`', str(invoer1), 'product_ID=' + str(product1[0]))
        update_extensie('`producten`', '`voorraad`', str(invoer2), 'product_ID=' + str(product2[0]))
    else:
        product1 = read_product(bestelproducten[0][1])
        totaal = bestelproducten[0][2]*product1[4]
        subtotaal = totaal
        invoer = product1[3] - bestelproducten[0][2]
        update_extensie('`producten`', '`voorraad`', str(invoer), 'product_ID=' + str(product1[0]))

    credit = read_klanten(bestelling[2])
    new_credit = credit[5] - subtotaal
    update_extensie('`klanten`', '`credits`', str(new_credit), 'klantnummer=' + str(bestelling[2]))

    delete_bestelling('`bestelling`', 'bestel_nummer=' + str(bestelling[0]))
    delete_bestelling('`bestellingen`', 'bestel_nummer=' + str(bestelling[0]))

while True:
    'Maak verbinding met de database'
    conn = pymysql.connect(host='nickspc146.146.axc.nl', user='nickspc146_candy', password='TAJeJQfxV', db='nickspc146_candy')

    '''Lees qrcode'''
    qrcode = read_qrcode()
    c = naam_pdf()
    genereer_pdf(c)
    c.showPage()
    c.save()
    update_database()
    conn.close()
