#!/bin/bash
#
# 
# Runs autoload script periodically
#
# sets up in proper directory and directs output to logfile
#

# variables

# homedir - directory from which PHP script should be run
homedir=/home/www/robotics/master/htdocs

# logfile
logfile=/home/www/robotics/master/admin/log/autoupdate.log

# phpfile
phpfile=bluealliance-autoupdate.php


# change directory and execute
cd $homedir
php $phpfile >>$logfile 2>&1 

# end
