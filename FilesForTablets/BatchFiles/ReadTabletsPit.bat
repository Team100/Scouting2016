set location="C:\TabletFiles\testfiles"
set loctabletfiles="C:\TabletFiles\ingest-pit"


adb kill-server
adb devices

echo Red1 Tablet
adb -s 077dbf0c pull sdcard/Scouting/Pit %location%/Red1/Scouting/Pit
adb -s 077dbf0c pull sdcard/Scouting/Pit %loctabletfiles%
adb -s 077dbf0c pull sdcard/Pictures %location%/Red1/Pictures

echo Red2 Tablet
adb -s 08e5add0 pull sdcard/Scouting/Pit %location%/Red2/Scouting/Pit
adb -s 08e5add0 pull sdcard/Scouting/Pit %loctabletfiles%
adb -s 08e5add0 pull sdcard/Pictures %location%/Red2/Pictures

echo Red3 Tablet
adb -s 09e0d463 pull sdcard/Scouting/Pit %location%/Red3/Scouting/Pit
adb -s 09e0d463 pull sdcard/Scouting/Pit %loctabletfiles%
adb -s 09e0d463 pull sdcard/Pictures %location%/Red3/Pictures

echo Blue1 Tablet
adb -s 093291ff pull sdcard/Scouting/Pit %location%/Blue1/Scouting/Pit
adb -s 093291ff pull sdcard/Scouting/Pit %loctabletfiles%
adb -s 093291ff pull sdcard/Pictures %location%/Blue1/Pictures

echo Blue2 Tablet
adb -s 0a0eb95a pull sdcard/Scouting/Pit %location%/Blue2/Scouting/Pit
adb -s 0a0eb95a pull sdcard/Scouting/Pit %loctabletfiles%
adb -s 0a0eb95a pull sdcard/Pictures %location%/Blue2/Pictures

echo Blue3 Tablet
adb -s 09f27d4b pull sdcard/Scouting/Pit %location%/Blue3/Scouting/Pit
adb -s 09f27d4b pull sdcard/Scouting/Pit %loctabletfiles%
adb -s 09f27d4b pull sdcard/Pictures %location%/Blue3/Pictures

echo Spare Tablet
adb -s 0a845d90 pull sdcard/Scouting/Pit %location%/spare/Scouting/Pit
adb -s 0a845d90 pull sdcard/Scouting/Pit %loctabletfiles%
adb -s 0a845d90 pull sdcard/Pictures %location%/spare/Pictures

