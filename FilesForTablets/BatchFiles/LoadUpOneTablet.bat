set backupLoc="C:\TabletFiles\backup"
set datafileLoc="C:\TabletFiles\filesToLoad"
adb kill-server

adb install %datafileLoc%\PitScouting2016.apk
adb install %datafileLoc%\MatchScouting2016.apk
adb push %datafileLoc%\TeamData.json sdcard/Scouting 
adb push %datafileLoc%\DS.txt sdcard/Scouting 
adb push %datafileLoc%\images sdcard/Scouting/images