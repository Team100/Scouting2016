rem
rem $Revision: 3.0 $
rem $Date: 2016/03/14 23:00:02 $
rem
rem clears database and loads a fresh schema, upload tables, etc.
rem

set dbname=competition
set dbuser=compuser
set dbpass=100hats

mysql -D competition -u compuser --password=%dbpass% < compsystem-tables-drop.sql

mysql -D %dbname% -u %dbuser% --password=%dbpass% < compsystem-tables.sql

rem mysql -D %dbname% -u %dbuser% --password=%dbpass% < insert-team.sql



rem insert documentation

