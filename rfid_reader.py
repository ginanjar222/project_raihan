import serial
import requests

# Ganti dengan COM port sesuai dengan ESP kamu (cek di Device Manager)
ser = serial.Serial('COM3', 115200, timeout=1)

while True:
    if ser.in_waiting > 0:
        uid = ser.readline().decode('utf-8').strip()
        print("UID:", uid)

        # Kirim ke Laravel (ganti URL dan token sesuai)
        try:
            response = requests.post('http://192.168.3.101:8000/api/rfid', data={'uid': uid})
            print("Server:", response.text)
        except Exception as e:
            print("Gagal kirim ke server:", e)