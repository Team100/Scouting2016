adb kill-server
adb devices

echo Red1 Tablet
adb -s 077dbf0c pull sdcard/Scouting/Matches testfiles/Red1/Scouting/Matches

echo Red2 Tablet
adb -s 08e5add0 pull sdcard/Scouting/Matches testfiles/Red2/Scouting/Matches

echo Red3 Tablet
adb -s 09e0d463 pull sdcard/Scouting/Matches testfiles/Red3/Scouting/Matches

echo Blue1 Tablet
adb -s 093291ff pull sdcard/Scouting/Matches testfiles/Blue1/Scouting/Matches

echo Blue2 Tablet
adb -s 0a0eb95a pull sdcard/Scouting/Matches testfiles/Blue2/Scouting/Matches

echo Blue3 Tablet
adb -s 09f27d4b pull sdcard/Scouting/Matches testfiles/Blue3/Scouting/Matches

echo Spare Tablet
adb -s 0a845d90 pull sdcard/Scouting/Matches testfiles/spare/Scouting/Matches