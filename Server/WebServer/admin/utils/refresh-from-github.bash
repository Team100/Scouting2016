#/bin/bash
#
#
# refresh-from-github
#
# refreshes source from github, then copies files over existing
#  distribution

# variables

# overall home -- home directory underwhich various directories contain 
#   components of system
home=/home/www/robotics

# git home -- repos home for github commands
gitrepo=$home/github/Scouting2016

# master home
#  Assumes htdocs.github and admin.github links are set
master=$home/master

# overlay home
overlay=$home/install.overlay


#
# Top of Commands
#
# Inform user as we progress
#

echo " " 
echo "Updates github to lastest master and installs in website"
echo " " 
echo " "

# update github
echo "Updating github..."

cd $gitrepo
git fetch origin master
git reset --hard FETCH_HEAD
git clean -df

echo "...done."
echo " " 

# copy git repo to htdocs and admin
echo "Copying from refreshed source to website directories..."
cp -LTr $master/htdocs.github $master/htdocs
cp -LTr $master/admin.github $master/admin

echo "done"

# wrap up
echo " " 
echo "Refresh and install complete."

exit

