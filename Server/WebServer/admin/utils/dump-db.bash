#!/bin/bash
#
#
# dump-tables.bash
#
# dumps tables one at a time
#

# vars to drive export

database=competition
user=compuser
password=100hats
date=`date '+%y%m%d%H%M'`

options=" --complete-insert --no-create-db --no-create-info --extended-insert"


#
# inform user
#
echo " " 
echo "Robotics Competition System Entire DB Export"

mysqldump $options --user=$user --password=$password $database \
   > comp-system-db.$date.dmp

echo " "
echo "Export complete."

