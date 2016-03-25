set location="C:\TabletFiles\testfiles"
adb kill-server
adb devices

echo Red1 Tablet
adb -s 077dbf0c pull sdcard/Scouting/Matches %location%/Red1/Scouting/Matches

echo Red2 Tablet
adb -s 08e5add0 pull sdcard/Scouting/Matches %location%/Red2/Scouting/Matches

echo Red3 Tablet
adb -s 09e0d463 pull sdcard/Scouting/Matches %location%/Red3/Scouting/Matches

echo Blue1 Tablet
adb -s 093291ff pull sdcard/Scouting/Matches %location%/Blue1/Scouting/Matches

echo Blue2 Tablet
adb -s 0a0eb95a pull sdcard/Scouting/Matches %location%/Blue2/Scouting/Matches

echo Blue3 Tablet
adb -s 09f27d4b pull sdcard/Scouting/Matches %location%s/Blue3/Scouting/Matches

echo Spare Tablet
adb -s 0a845d90 pull sdcard/Scouting/Matches %location%/spare/Scouting/Matches