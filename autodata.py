import platform

from selenium import webdriver
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options

if platform.system() != 'Windows':
    from webdriver_manager.chrome import ChromeDriverManager

import time
import datetime
from datetime import timedelta

import json
import schedule

class AutoData():
    def __init__(self):
        self.site_energy = "https://www.mercatoelettrico.org/it/Statistiche/ME/DatiSintesi.aspx"
        self.site_gpl = "https://www.cuneoprezzi.it/ingrosso/energetici/?category=19"
        self.site_petrolium = "https://mercati.ilsole24ore.com/materie-prime/commodities/petrolio/BRNST.IPE"
        self.options = Options()
        self.options.add_argument('--headless')
        self.options.add_argument('--no-sandbox')
        self.options.add_argument('--disable-dev-shm-usage')
        
        self.driver = None

        self.price_energy = 0
        self.price_gpl = 0
        self.price_petrolium = 0

    def daily(self):
        if platform.system() == 'Windows':
            self.driver_energy = webdriver.Chrome()
            self.driver_gpl = webdriver.Chrome()
            self.driver_petrolium = webdriver.Chrome()
        else:
            self.driver_energy = webdriver.Chrome(service=Service(ChromeDriverManager().install()), options=self.options)
            self.driver_gpl = webdriver.Chrome(service=Service(ChromeDriverManager().install()), options=self.options)
            self.driver_petrolium = webdriver.Chrome(service=Service(ChromeDriverManager().install()), options=self.options)

        self.driver_gpl.get(self.site_gpl)
                
        # Trova il listino
        listino = self.driver_gpl.find_element(By.XPATH,'/html/body/div[4]/div/div/div/div[1]/div[5]/div[2]/table/tbody/tr[3]/td[4]/b/i') 
        listino = listino.text.encode('utf8') # Codifica i risultati ottenuti in UTF-8
        listino = str(listino).strip('b').strip("'").strip(" ")

        self.price_gpl = round(float(listino.replace(',','.')), 3)

        self.driver_gpl.close()
        
        self.driver_energy.get(self.site_energy)
        self.acceptConditions()
        
        # Trova la tabella nella pagina
        table = self.driver_energy.find_element(By.XPATH,'//*[@id="ContentPlaceHolder1_GridView3"]')
        body = table.find_element(By.TAG_NAME,'tbody')

        file_data = []

        body_rows = body.find_elements(By.TAG_NAME,'tr') # Ottiene tutte le righe
        for row in body_rows:
            data = row.find_elements(By.TAG_NAME,'td') #Ottiene le celle della riga
            file_row = []
            
            for datum in data:
                datum_text = datum.text.encode('utf8') # Codifica i risultati ottenuti in UTF-8
                file_row.append(datum_text)
            
            file_data.append(file_row)

        today = datetime.date.today()
        todayString = today.strftime("%d")

        giorno1 = str(file_data[len(file_data)-4][0])

        if giorno1.__contains__(todayString):
            risultato = str(file_data[len(file_data)-4][1])
        else:
            risultato = str(file_data[len(file_data)-2][1])
        
        risultato = str(risultato).strip('b').strip("'").strip(" ") # Formatta il testo ottenuto
        risultato = risultato.split(" ")
        
        self.price_energy = risultato[0]
        self.price_energy = round(float(self.price_energy.replace(',','.'))/1000,3)
        
        self.driver_energy.close()

        self.driver_petrolium.get(self.site_petrolium)

        self.price_petrolium = self.driver_petrolium.find_element(By.XPATH, '//*[@id="wrapper"]/div[1]/div[4]/div[1]/section[2]/div/div[1]/div/div[2]/div[1]/div[1]/div/div/div/div/div[2]/div/div[1]/div/div[1]/div/span[1]')
        self.price_petrolium = float(self.price_petrolium.text)

        self.driver_petrolium.close()
        self.save() # Salva il nuovo pun nel file Json
        
    def acceptConditions(self):
        try: # Prima casella condizioni
            accetta_contenuto = self.driver_energy.find_element(By.ID,'ContentPlaceHolder1_CBAccetto1')
            self.driver_energy.execute_script("arguments[0].click();", accetta_contenuto)

            # Seconda casella condizioni
            accetta = self.driver_energy.find_element(By.ID,'ContentPlaceHolder1_CBAccetto2')
            self.driver_energy.execute_script("arguments[0].click();", accetta)

            # Accetta tutto
            accetta_finale = self.driver_energy.find_element(By.ID,'ContentPlaceHolder1_Button1')
            self.driver_energy.execute_script("arguments[0].click();", accetta_finale)

            # Prima casella condizioni
            accetta_contenuto = self.driver_energy.find_element(By.ID,'ContentPlaceHolder1_CBAccetto1')
            self.driver_energy.execute_script("arguments[0].click();", accetta_contenuto)

            # Seconda casella condizioni
            accetta = self.driver_energy.find_element(By.ID,'ContentPlaceHolder1_CBAccetto2')
            self.driver_energy.execute_script("arguments[0].click();", accetta)

            # Accetta tutto
            accetta_finale = self.driver_energy.find_element(By.ID,'ContentPlaceHolder1_Button1')
            self.driver_energy.execute_script("arguments[0].click();", accetta_finale)
        except: None
    
    def save(self):
        today = datetime.date.today()
        todayMonth = today.strftime("%m-%Y")
        todayString = today.strftime("%d")
        
        if todayMonth.startswith('0'):
            todayMonth = todayMonth[1:]

        with open('/var/www/html/enerprices/data.json', 'r') as file:
            data = json.load(file)

        data['today']['energy'] = self.price_energy
        data['today']['gpl'] = self.price_gpl
        data['today']['petrolium'] = self.price_petrolium
            
        with open('/var/www/html/enerprices/data.json', 'w') as f:
            json.dump(data, f, indent=4)

autodata = AutoData()

schedule.every().day.at("00:00").do(autodata.daily)
#schedule.every(10).seconds.do(autodata.listino_15_gg) # TEST

while True:
    schedule.run_pending()
    time.sleep(1)