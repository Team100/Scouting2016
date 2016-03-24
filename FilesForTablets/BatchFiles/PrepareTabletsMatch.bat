set backupLoc="C:\TabletFiles\backup"
adb kill-server
adb devices

echo Red1 Tablet
adb -s 077dbf0c pull sdcard/Scouting/Matches %backupLoc%/Red1/Scouting/Matches
adb -s 077dbf0c shell < ClearMatchFiles.txt

echo Red2 Tablet
adb -s 08e5add0 pull sdcard/Scouting/Matches %backupLoc%/Red2/Scouting/Matches
adb -s 08e5add0 shell < ClearMatchFiles.txt

echo Red3 Tablet
adb -s 09e0d463 pull sdcard/Scouting/Matches %backupLoc%/Red3/Scouting/Matches
adb -s 09e0d463 shell < ClearMatchFiles.txt

echo Blue1 Tablet
adb -s 093291ff pull sdcard/Scouting/Matches %backupLoc%/Blue1/Scouting/Matches
adb -s 093291ff shell < ClearMatchFiles.txt

echo Blue2 Tablet
adb -s 0a0eb95a pull sdcard/Scouting/Matches%backupLoc%/Blue2/Scouting/Matches
adb -s 0a0eb95a shell < ClearMatchFiles.txt

echo Blue3 Tablet
adb -s 09f27d4b pull sdcard/Scouting/Matches %backupLoc%s/Blue3/Scouting/Matches
adb -s 09f27d4b shell < ClearMatchFiles.txt

echo Spare Tablet
adb -s 0a845d90 pull sdcard/Scouting/Matches %backupLoc%/spare/Scouting/Matches
adb -s 0a845d90 shell < ClearMatchFiles.txt