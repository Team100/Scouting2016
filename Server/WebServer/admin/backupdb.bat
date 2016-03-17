rem
rem $Revision: 1.1 $
rem $Date: 2010/03/20 05:54:58 $
rem
rem clears database and loads a fresh schema, upload tables, etc.
rem

rem database credentials
set dbname=competition
set dbuser=compuser
set dbpass=3006redrock

rem backup location root
set backupfile=C:\Program Files\Apache Software Foundation\Apache2.2\Archive\DB-Backups\CompDB-Back

rem ----------------------------------------------------------------------------------

rem bump up files
move "%backupfile%-19.sqldmp" "%backupfile%-20.sqldmp"
move "%backupfile%-18.sqldmp" "%backupfile%-19.sqldmp"
move "%backupfile%-17.sqldmp" "%backupfile%-18.sqldmp"
move "%backupfile%-16.sqldmp" "%backupfile%-17.sqldmp"
move "%backupfile%-15.sqldmp" "%backupfile%-16.sqldmp"
move "%backupfile%-14.sqldmp" "%backupfile%-15.sqldmp"
move "%backupfile%-13.sqldmp" "%backupfile%-14.sqldmp"
move "%backupfile%-12.sqldmp" "%backupfile%-13.sqldmp"
move "%backupfile%-11.sqldmp" "%backupfile%-12.sqldmp"
move "%backupfile%-10.sqldmp" "%backupfile%-11.sqldmp"
move "%backupfile%-9.sqldmp" "%backupfile%-10.sqldmp"
move "%backupfile%-8.sqldmp" "%backupfile%-9.sqldmp"
move "%backupfile%-7.sqldmp" "%backupfile%-8.sqldmp"
move "%backupfile%-6.sqldmp" "%backupfile%-7.sqldmp"
move "%backupfile%-5.sqldmp" "%backupfile%-6.sqldmp"
move "%backupfile%-4.sqldmp" "%backupfile%-5.sqldmp"
move "%backupfile%-3.sqldmp" "%backupfile%-4.sqldmp"
move "%backupfile%-2.sqldmp" "%backupfile%-3.sqldmp"
move "%backupfile%-1.sqldmp" "%backupfile%-2.sqldmp"
move "%backupfile%-0.sqldmp" "%backupfile%-1.sqldmp"

rem backup with mysqldbump
mysqldump  -u %dbuser% --password=%dbpass% %dbname% > "%backupfile%-0.sqldmp"

rem Complete
rem 