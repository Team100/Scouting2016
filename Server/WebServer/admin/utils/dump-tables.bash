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

tables="teambot match_instance match_instance_alliance match_team"

options=" --complete-insert --no-create-db --no-create-info --extended-insert"


#
# inform user
#
echo " " 
echo "Robotics Table Exports"

for table in $tables
do
  mysqldump $options --user=$user --password=$password $database $table \
   > $table.$date.dmp
  echo " "
  echo "Dumped $table to $table.dmp."
done

echo " "
echo "Export complete."

