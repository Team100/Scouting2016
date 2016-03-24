set backupLoc="C:\TabletFiles\backup"
set datafileLoc="C:\TabletFiles\filesToLoad"
adb kill-server
adb devices

echo Red1 Tablet
adb -s 077dbf0c pull sdcard/Scouting/Pit %backupLoc%/Red1/Scouting/Pit
adb -s 077dbf0c pull sdcard/Pictures %backupLoc%/Red1/Pictures
adb -s 077dbf0c shell < ClearPitFiles.txt
adb -s 077dbf0c shell < ClearPictureFiles.txt

echo Red2 Tablet
adb -s 08e5add0 pull sdcard/Scouting/Pit %backupLoc%/Red2/Scouting/Pit
adb -s 08e5add0 pull sdcard/Pictures %backupLoc%/Red2/Pictures
adb -s 08e5add0 shell < ClearPitFiles.txt
adb -s 08e5add0 shell < ClearPictureFiles.txt


echo Red3 Tablet
adb -s 09e0d463 pull sdcard/Scouting/Pit %backupLoc%/Red3/Scouting/Pit
adb -s 09e0d463 pull sdcard/Pictures %backupLoc%/Red2/Pictures
adb -s 09e0d463 shell < ClearPitFiles.txt
adb -s 09e0d463 shell < ClearPictureFiles.txt

echo Blue1 Tablet
adb -s 093291ff pull sdcard/Scouting/Pit %backupLoc%/Blue1/Scouting/Pit
adb -s 093291ff pull sdcard/Pictures %backupLoc%/Blue1/Pictures
adb -s 093291ff shell < ClearPitFiles.txt
adb -s 093291ff shell < ClearPictureFiles.txt

echo Blue2 Tablet
adb -s 0a0eb95a pull sdcard/Scouting/Pit%backupLoc%/Blue2/Scouting/Pit
adb -s 0a0eb95a pull sdcard/Pictures %backupLoc%/Blue2/Pictures
adb -s 0a0eb95a shell < ClearPitFiles.txt
adb -s 0a0eb95a shell < ClearPictureFiles.txt

echo Blue3 Tablet
adb -s 09f27d4b pull sdcard/Scouting/Pit %backupLoc%s/Blue3/Scouting/Pit
adb -s 09f27d4b pull sdcard/Pictures %backupLoc%/Blue3/Pictures
adb -s 09f27d4b shell < ClearPitFiles.txt
adb -s 09f27d4b shell < ClearPictureFiles.txt

echo Spare Tablet
adb -s 0a845d90 pull sdcard/Scouting/Pit %backupLoc%/spare/Scouting/Pit
adb -s 09f27d4b pull sdcard/Pictures %backupLoc%/spare/Pictures
adb -s 09f27d4b shell < ClearPitFiles.txt
adb -s 09f27d4b shell < ClearPictureFiles.txt