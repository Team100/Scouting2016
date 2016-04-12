#/bin/bash
#
# $Revision: 2.1 $
# $Date: 2010/04/14 08:21:12 $
#
# clears database and loads a fresh schema, upload tables, etc.
#

dbname=competition
dbuser=compuser
dbpass=100hats

mysql -D ${dbname} -u ${dbuser} --password=${dbpass} < compsystem-tables-drop.sql

mysql -D ${dbname} -u ${dbuser} --password=${dbpass} < compsystem-tables.sql

echo "Competition System Tables Built"