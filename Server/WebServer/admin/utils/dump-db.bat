rem
rem $Revision: 1.1 $
rem $Date: 2010/03/20 05:54:58 $
rem
rem backs up tables in db but in a way they can be added as inserts
rem
rem  restore assumes database is already created
rem

rem database credentials
set dbname=competition
set dbuser=compuser
set dbpass=100hats

set options= --complete-insert --no-create-db --no-create-info --extended-insert

rem default backup location root
set backupfile=%1
if "%1" == "" set backupfile=dump-db-inserts


rem ----------------------------------------------------------------------------------

rem bump up files
move "%backupfile%-19.dmp" "%backupfile%-20.dmp"
move "%backupfile%-18.dmp" "%backupfile%-19.dmp"
move "%backupfile%-17.dmp" "%backupfile%-18.dmp"
move "%backupfile%-16.dmp" "%backupfile%-17.dmp"
move "%backupfile%-15.dmp" "%backupfile%-16.dmp"
move "%backupfile%-14.dmp" "%backupfile%-15.dmp"
move "%backupfile%-13.dmp" "%backupfile%-14.dmp"
move "%backupfile%-12.dmp" "%backupfile%-13.dmp"
move "%backupfile%-11.dmp" "%backupfile%-12.dmp"
move "%backupfile%-10.dmp" "%backupfile%-11.dmp"
move "%backupfile%-9.dmp" "%backupfile%-10.dmp"
move "%backupfile%-8.dmp" "%backupfile%-9.dmp"
move "%backupfile%-7.dmp" "%backupfile%-8.dmp"
move "%backupfile%-6.dmp" "%backupfile%-7.dmp"
move "%backupfile%-5.dmp" "%backupfile%-6.dmp"
move "%backupfile%-4.dmp" "%backupfile%-5.dmp"
move "%backupfile%-3.dmp" "%backupfile%-4.dmp"
move "%backupfile%-2.dmp" "%backupfile%-3.dmp"
move "%backupfile%-1.dmp" "%backupfile%-2.dmp"
move "%backupfile%-0.dmp" "%backupfile%-1.dmp"

rem backup with mysqlddump
mysqldump  %options% -u %dbuser% --password=%dbpass% %dbname% > "%backupfile%-0.dmp"

rem Complete
rem 