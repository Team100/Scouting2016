adb kill-server
adb devices

echo Red1 Tablet
adb -s 077dbf0c pull sdcard/Scouting/Pit testfiles/Red1/Scouting/Pit
adb -s 077dbf0c pull sdcard/Pictures testfiles/Red1/Pictures

echo Red2 Tablet
adb -s 08e5add0 pull sdcard/Scouting/Pit testfiles/Red2/Scouting/Pit
adb -s 08e5add0 pull sdcard/Pictures testfiles/Red2/Pictures

echo Red3 Tablet
adb -s 09e0d463 pull sdcard/Scouting/Pit testfiles/Red3/Scouting/Pit
adb -s 09e0d463 pull sdcard/Pictures testfiles/Red3/Pictures

echo Blue1 Tablet
adb -s 093291ff pull sdcard/Scouting/Pit testfiles/Blue1/Scouting/Pit
adb -s 093291ff pull sdcard/Pictures testfiles/Blue1/Pictures

echo Blue2 Tablet
adb -s 0a0eb95a pull sdcard/Scouting/Pit testfiles/Blue2/Scouting/Pit
adb -s 0a0eb95a pull sdcard/Pictures testfiles/Blue2/Pictures

echo Blue3 Tablet
adb -s 09f27d4b pull sdcard/Scouting/Pit testfiles/Blue3/Scouting/Pit
adb -s 09f27d4b pull sdcard/Pictures testfiles/Blue3/Pictures

echo Spare Tablet
adb -s 0a845d90 pull sdcard/Scouting/Pit testfiles/spare/Scouting/Pit
adb -s 0a845d90 pull sdcard/Pictures testfiles/spare/Pictures

