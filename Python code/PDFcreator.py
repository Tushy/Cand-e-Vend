from reportlab.pdfgen import canvas
from reportlab.lib import colors
import datetime
import pymysql
conn = pymysql.connect(host='localhost', user='root', password='vertrigo', db='idp')

qrcode = '0009'

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

'''Geef het pdf bestand een naam met bijbehorend bestelnummer'''
bestelling = read_bestellingen(qrcode)
naam = bestelling[0]
c = canvas.Canvas('Bestelling ' + str(naam) + '.pdf')

genereer_pdf(c)
c.showPage()
c.save()
conn.close()
