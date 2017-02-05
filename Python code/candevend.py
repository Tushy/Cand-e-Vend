from reportlab.pdfgen import canvas
from reportlab.lib import colors
from PIL import Image
import RPi.GPIO as GPIO
import datetime
import pymysql
import time
import os
import zbarlight
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart

def servo(aantal):
    'Laat de eerste servo motor draaien. De parameter is het aantal van het bestelde product.'
    GPIO.setwarnings(False)
    GPIO.setmode(GPIO.BCM)
    GPIO.setup(12, GPIO.OUT)
    pwm = GPIO.PWM(12, 100)
    x = 0
    while x < aantal:
        pwm.start(5)
        pwm.ChangeDutyCycle(10)
        time.sleep(1.3)
        pwm.ChangeDutyCycle(0)
        time.sleep(0.5)
        x += 1
        pwm.stop

def servo2(aantal2):
    'Laat de tweede servo motor draaien. De parameter is het aantal van het bestelde product.'
    GPIO.setwarnings(False)
    GPIO.setmode(GPIO.BCM)
    GPIO.setup(16, GPIO.OUT)
    pwm = GPIO.PWM(16, 100)
    y = 0
    while y < aantal2:
        pwm.start(5)
        pwm.ChangeDutyCycle(5)
        time.sleep(1.255)
        pwm.ChangeDutyCycle(0)
        time.sleep(0.5)
        y += 1
        pwm.stop

def send_pdf():
    'Deze functie stuurt het gegenereerde factuur naar de klant zijn e-mail.'
    sender = 'noreply.candevend@gmail.com'
    naam = bestelling[0]
    klant = bestelling[2]
    klantinfo = read_klanten(klant)
    email = klantinfo[1]
    recipient = email

    msg = MIMEMultipart()
    msg['Subject'] = 'Uw bestelling is voltooid'
    msg['From'] = sender
    msg['Reply-To'] = recipient

    msg.preamble = 'Multipart massage.\n'
    part = MIMEText('Uw factuur zit in de bijlage.')
    msg.attach(part)

    part = MIMEText(open('Bestelling ' + str(naam) + '.pdf').read())
    part.add_header('Content-Disposition', 'attachment', filename=('Bestelling ' + str(naam) + '.pdf'))
    msg.attach(part)

    mail = smtplib.SMTP('smtp.gmail.com', 587)
    mail.ehlo()
    mail.starttls()
    mail.login(sender, 'Candevend1')
    mail.sendmail(sender, recipient, msg.as_string())

    print('factuur is verstuurd!')


def read_bestellingen(qrcode):
    'Lees tabel bestellingen en pak de bestelling met overeenkomende qr-code'
    cursor = conn.cursor()
    sql = 'SELECT * FROM `bestellingen`'
    cursor.execute(sql)
    data = cursor.fetchall()
    for x in data:
        if x[4] == qrcode:
            if x[3] == False:
                return x
            else:
                x = False
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
    bestelproducten = read_bestelproducten(bestelling[0])
    if len(bestelproducten) == 2:
        product1 = read_product(bestelproducten[0][1])
        product2 = read_product(bestelproducten[1][1])
        totaal = bestelproducten[0][2] * product1[4]
        totaal2 = bestelproducten[1][2] * product2[4]
        subtotaal = totaal + totaal2
        aantal = bestelproducten[0][2]
        aantal2 = bestelproducten[1][2]
        print(aantal)
        print(aantal2)
        servo(aantal)
        time.sleep(1.5)
        servo2(aantal2)
    else:
        product1 = read_product(bestelproducten[0][1])
        totaal = bestelproducten[0][2] * product1[4]
        subtotaal = totaal
        aantal = bestelproducten[0][2]
        if bestelproducten[0][1] == 1:
            servo(aantal)
        else:
            servo2(aantal)

    klant = read_klanten(bestelling[2])
    klantnaam = str(klant[3] + ' ' + klant[4])

    vandaag = datetime.date.today()

    c.drawString(50, 800, 'SNOEP4ALL')
    c.drawString(50, 780, 'Daltonlaan 200')
    c.drawString(50, 760, '3584 BJ  Utrecht')
    c.drawString(50, 740, 'TEL: +316 46 30 48 64')

    c.setFillColor(colors.lightblue)
    c.setStrokeColor(colors.lightblue)
    c.rect(20, 550, 550, 20, fill=1)

    c.setFillColor(colors.lightblue)
    c.setStrokeColor(colors.lightblue)
    c.rect(20, 670, 550, 20, fill=1)

    c.setStrokeColor('black')
    c.line(20, 700, 570, 700)

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

    c.setFont('Helvetica', 40)
    c.drawString(320, 750, 'Cand-e-Vend')

    print('bestelling voltooid!')
    time.sleep(4)

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
        time.sleep(2)

def update_extensie(tabel, kolom, invoer, plaats):
    'verander een waarde in een extensie'
    cursor = conn.cursor()
    sql = 'UPDATE ' + tabel + ' SET ' + kolom + '=' + invoer + ' WHERE ' + plaats
    cursor.execute(sql)
    conn.commit()
    cursor.close()

def naam_pdf():
    'Geef het pdf bestand een naam met bijbehorend bestelnummer'
    naam = bestelling[0]
    c = canvas.Canvas('Bestelling ' + str(naam) + '.pdf')
    return c

def update_database():
    'Deze functie updatet de database extensies'
    bestelproducten = read_bestelproducten(bestelling[0])
    if len(bestelproducten) == 2:
        product1 = read_product(bestelproducten[0][1])
        product2 = read_product(bestelproducten[1][1])
        totaal = bestelproducten[0][2] * product1[4]
        totaal2 = bestelproducten[1][2] * product2[4]
        subtotaal = totaal + totaal2
        invoer1 = product1[3] - bestelproducten[0][2]
        invoer2 = product2[3] - bestelproducten[1][2]
        update_extensie('`producten`', '`voorraad`', str(invoer1), 'product_ID=' + str(product1[0]))
        update_extensie('`producten`', '`voorraad`', str(invoer2), 'product_ID=' + str(product2[0]))
    else:
        product1 = read_product(bestelproducten[0][1])
        totaal = bestelproducten[0][2] * product1[4]
        subtotaal = totaal
        invoer = product1[3] - bestelproducten[0][2]
        update_extensie('`producten`', '`voorraad`', str(invoer), 'product_ID=' + str(product1[0]))

    credit = read_klanten(bestelling[2])
    new_credit = credit[5] - subtotaal
    update_extensie('`klanten`', '`credits`', str(new_credit), 'klantnummer=' + str(bestelling[2]))
    update_extensie('`bestellingen`', '`voltooid`', str(1), 'klantnummer=' + str(bestelling[2]))

while True:
    'Maak verbinding met de database'
    conn = pymysql.connect(host='nickspc146.146.axc.nl', user='nickspc146_candy', password='TAJeJQfxV',
                           db='nickspc146_candy')

    '''Lees qrcode, daarna loopt hij programma de code door op basis van de bestelling bij de gelezen qrcode.'''
    qrcode = read_qrcode()
    bestelling = read_bestellingen(qrcode)
    if bestelling != False:
        c = naam_pdf()
        genereer_pdf(c)
        c.showPage()
        c.save()
        send_pdf()
        update_database()
    else:
        print('ongeldige code')
    conn.close()