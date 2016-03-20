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

rem mysql -D %dbname% -u %dbuser% --password=%dbpass% < insert-league.sql

rem mysql -D %dbname% -u %dbuser% --password=%dbpass% < insert-team.sql

rem
rem comments out the right statement for regionals or nationals
rem 

rem This statement for regionals
rem mysql -D %dbname% -u %dbuser% --password=%dbpass% < insert-teambot.sql

rem These statements for nationals
rem mysql -D %dbname% -u %dbuser% --password=%dbpass% < insert-champ.sql

rem mysql -D %dbname% -u %dbuser% --password=%dbpass% <insert-champ-teambot.sql

