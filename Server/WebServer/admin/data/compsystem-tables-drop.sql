#
# $Revision: 3.0 $
# $Date: 2016/03/14 23:00:02 $
#
# Red Rock Robotics, Wildhats, Verkler
# Competition System Table Schema
#
# Notes:
#  - drops all tables for a new instance of the competition system
#
#  - create table script is maintained in compsystem-tables.sql
#
#  - if you add a table, make sure it is included here for a drop table
#
#  - running this script also "DROPS THE DATA IN THE TABLES".  Be careful
#
#

drop table event;
drop table team;
drop table team_history;
drop table team_history_award;
drop table teambot;
drop table alliance;
drop table alliance_team;
drop table alliance_unavailable;
drop table match_instance;
drop table match_instance_alliance;
drop table match_team;
#drop table match_alliance_team;
drop table schedule;
#drop table championteam;
drop table process_lock;
drop table message;
drop table user_profile;
drop table topic;
drop table documentation;
drop table pagetodoc;
drop table system_value;
drop table tba_last_modified;

