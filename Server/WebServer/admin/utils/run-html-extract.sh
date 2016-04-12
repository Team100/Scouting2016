#!/bin/bash
#
# $Revision: 3.0 $
# $Date: 2016/03/14 23:00:02 $
#
# run phpextract, loads database and performs a db dump of the table
#

# variables

# first home
firsthome=/home/jlv/FIRST

# files dir
filesdir=$firsthome/teams

# extract script
extract=$firsthome/php/team-extract.php

# db dump
dbdump=$firsthome/team-tables.sdmp
dbcmd="mysqldump -u compuser --password=3006redrock --complete-insert competition team "

#
#
echo "Process each file..."
for file in $filesdir/*
do
    echo "Processing file $file..."
    $extract $file
done

echo "Dumping database table..."
$dbcmd > $dbdump

echo "Complete"


