#
# $Revision: 3.0 $
# $Date: 2016/03/14 23:00:02 $
#
# Red Rock Robotics
# Competition System Table Schema
#
# Notes:
#  - creates all tables for a new instance of the competition system
#
#  - if you modify a table, make _sure_ the modification is also made in this schema
#
#  - to execute, run mysql as:
#      mysql -D db-name -u user -p < scriptname
#
#  - document the purpose of a table by in the heading before the table
#
#  - a "drop table" script is maintained in compsystem-tables-drop.sql
#
#


#
# Field
# 
# For nationals, we may have scouting for each of four fields.  Building in ability to clone
#  this app and use it in multiple places, then export and import those data into a master
#
create table league
 (
  league varchar(5),		# league "name" for nationals
  league_name varchar(25),	# long-form of league name	
  primary key (league)
 );


#
# Team
#
# General team information, intended to be carried forward year after year
#
# 
create table team
 (
  teamnum  int, 		# FIRST team number - primary key
  locked varchar(12), 		# row locked for editing by user.  Can clear in application.
  name varchar(30),		# formal name	
  nickname varchar(30),		# our nickname for team
  org varchar(80),		# high school or other organization
  location varchar(40),		# location of team
  students int,			# number of students on team
  website varchar(60),		# team web site
  sponsors varchar(400),	# team sponsors
  rookie_year int, 		# rookie year, from TIMS
  history varchar(10000),	# history of events from FIRST site
  notes varchar(5000),		# notes on our interaciton with the team
  primary key (teamnum)
 );


#
# teambot
#
# Information on the team at the event.  There should only be one entry in this table for event players
# 
# Foreign key from team table
#
create table teambot
 (

  teamnum  int, 		# FIRST team number - foreign key from team table
#  league varchar(5),		# league "name" for nationals
  locked varchar(12), 		# row locked for editing by user.  Can clear in application.
  updatedby varchar(200), 	# last updated by users
  rank_overall real,		# overall rank
  rating_overall int,		# 0-9 rating as an overall bot
  rating_overall_off int,	# 0-9 rating offensively
  rating_overall_def int,	# 0-9 rating defensively
  rank_pos1 real,		# overall rank in position 1
  rating_pos1 int,		# 0-9 rating in position 1
  rank_pos2 real,		# overall rank in position 2
  rating_pos2 int,		# 0-9 rating in position 2
  rank_pos3 real,		# overall rank in position 3
  rating_pos3 int,		# 0-9 rating in position 3
  PlayField_0 varchar(50),	# Play Field 0 (meaning/assignment defined in params)
  PlayField_1 varchar(50),	# Play Field 1 (meaning/assignment defined in params)
  PlayField_2 varchar(50),	# Play Field 2 (meaning/assignment defined in params)
  PlayField_3 varchar(50),	# Play Field 3 (meaning/assignment defined in params)
  PlayField_4 varchar(50),	# Play Field 4 (meaning/assignment defined in params)
  PlayField_5 varchar(50),	# Play Field 5 (meaning/assignment defined in params)
  PlayField_6 varchar(50),	# Play Field 6 (meaning/assignment defined in params)
  PlayField_7 varchar(50),	# Play Field 7 (meaning/assignment defined in params)
  PlayField_8 varchar(50),	# Play Field 8 (meaning/assignment defined in params)
  PlayField_9 varchar(50),	# Play Field 9 (meaning/assignment defined in params)
  PlayField_10 varchar(50),	# Play Field 10 (meaning/assignment defined in params)
  PlayField_11 varchar(50),	# Play Field 11 (meaning/assignment defined in params)
  PlayField_12 varchar(50),	# Play Field 12 (meaning/assignment defined in params)
  PlayField_13 varchar(50),	# Play Field 13 (meaning/assignment defined in params)
  PlayField_14 varchar(50),	# Play Field 14 (meaning/assignment defined in params)
  PlayField_15 varchar(50),	# Play Field 15 (meaning/assignment defined in params)
  PlayField_16 varchar(50),	# Play Field 16 (meaning/assignment defined in params)
  PlayField_17 varchar(50),	# Play Field 17 (meaning/assignment defined in params)
  PlayField_18 varchar(50),	# Play Field 18 (meaning/assignment defined in params)
  PlayField_19 varchar(50),	# Play Field 19 (meaning/assignment defined in params)
  PlayField_20 varchar(50),	# Play Field 20 (meaning/assignment defined in params)
  PlayField_21 varchar(50),	# Play Field 21 (meaning/assignment defined in params)
  PlayField_22 varchar(50),	# Play Field 22 (meaning/assignment defined in params)
  PlayField_23 varchar(50),	# Play Field 23 (meaning/assignment defined in params)
  PlayField_24 varchar(50),	# Play Field 24 (meaning/assignment defined in params)
  PlayField_25 varchar(50),	# Play Field 25 (meaning/assignment defined in params)
  PlayField_26 varchar(50),	# Play Field 26 (meaning/assignment defined in params)
  PlayField_27 varchar(50),	# Play Field 27 (meaning/assignment defined in params)
  PlayField_28 varchar(50),	# Play Field 28 (meaning/assignment defined in params)
  PlayField_29 varchar(50),	# Play Field 29 (meaning/assignment defined in params)
  PlayField_30 varchar(50),	# Play Field 30 (meaning/assignment defined in params)
  PlayField_31 varchar(50),	# Play Field 31 (meaning/assignment defined in params)
  PlayField_32 varchar(50),	# Play Field 32 (meaning/assignment defined in params)
  PlayField_33 varchar(50),	# Play Field 33 (meaning/assignment defined in params)
  PlayField_34 varchar(50),	# Play Field 34 (meaning/assignment defined in params)
  PlayField_35 varchar(50),	# Play Field 35 (meaning/assignment defined in params)
  PitField_0 varchar(50),	# Pit Field 0 (meaning/assignment defined in params)
  PitField_1 varchar(50),	# Pit Field 1 (meaning/assignment defined in params)
  PitField_2 varchar(50),	# Pit Field 2 (meaning/assignment defined in params)
  PitField_3 varchar(50),	# Pit Field 3 (meaning/assignment defined in params)
  PitField_4 varchar(50),	# Pit Field 4 (meaning/assignment defined in params)
  PitField_5 varchar(50),	# Pit Field 5 (meaning/assignment defined in params)
  PitField_6 varchar(50),	# Pit Field 6 (meaning/assignment defined in params)
  PitField_7 varchar(50),	# Pit Field 7 (meaning/assignment defined in params)
  PitField_8 varchar(50),	# Pit Field 8 (meaning/assignment defined in params)
  PitField_9 varchar(50),	# Pit Field 9 (meaning/assignment defined in params)
  PitField_10 varchar(50),	# Pit Field 10 (meaning/assignment defined in params)
  PitField_11 varchar(50),	# Pit Field 11 (meaning/assignment defined in params)
  PitField_12 varchar(50),	# Pit Field 12 (meaning/assignment defined in params)
  PitField_13 varchar(50),	# Pit Field 13 (meaning/assignment defined in params)
  PitField_14 varchar(50),	# Pit Field 14 (meaning/assignment defined in params)
  PitField_15 varchar(50),	# Pit Field 15 (meaning/assignment defined in params)
  offense_analysis varchar(1000),	# offense analysis (text)
  defense_analysis varchar(1000), 	# defense analysis (text)
  pos1_analysis varchar(1000),		# position 1 analysis (text)
  pos2_analysis varchar(1000),		# position 2 analysis (text)
  pos3_analysis varchar(1000),		# position 3 analysis (text)
  robot_analysis varchar(1000),		# overall robot analysis
  driver_analysis varchar(1000),	# driver analysis
  with_recommendation varchar(1000),	# recommendation if partnered with
  against_recommendation varchar(1000),	# recommendation if partnered against
  primary key(teamnum)
 );



#
# Alliance
#
# Only used in scouting for finals.  As alliances will play as a group, we will start
#  scouting and evaluating only competitively, but as an alliance as well as individual 
#  teams

create table alliance
 (
  league varchar(5),			# league "name" for nationals
  alliancenum int,			# Alliance - #1 through #8
  locked varchar(12), 			# row locked for editing by user.  Can clear in application.
  offense_analysis varchar(1000),	# offense analysis (text)
  defense_analysis varchar(1000), 	# defense analysis (text)
  pos1_analysis varchar(1000),		# position 1 analysis (text)
  pos2_analysis varchar(1000),		# position 2 analysis (text)
  pos3_analysis varchar(1000),		# position 3 analysis (text)
  against_recommendation varchar(2000),	# recommendation if partnered against
  primary key (league, alliancenum)
 );


#
# alliance team
#
# normalized recording of alliance team.  Join to alliance for data
#

create table alliance_team
 (
  league varchar(5),			# league "name" for nationals
  alliancenum int,			# Alliance - #1 through #8
  teamnum  int, 			# FIRST team number - foreign key from team table
  locked varchar(12), 			# row locked for editing by user.  Can clear in application.
  position int,				# position in the alliance (1,2,3)
  primary key (league, alliancenum, teamnum)
 );

#
# alliance unavailable -- unavailable for alliance choosing, including 
#  teams that refused us
#
create table alliance_unavailable
 (
  league varchar(5),			# league "name" for nationals
  alliancenum int,			# Alliance - #1 through #8
  teamnum  int, 			# FIRST team number - foreign key from team table
  unavailable boolean,			# marked if team selection is unavailable (refused or otherwise)
  refused boolean,			# refused our offer, so take off the availability list
  primary key (league, teamnum)
 );



#########################
#
# Match Tables
#

#
# Match Instance
#
# Master record for any type of match
#
create table match_instance
 (
  league varchar(5),		# League, separating data for nationals with multiple fields
  type varchar(1), 		# Q=qualifying, P=practice, F=Final  part of primary key
  matchnum int,			# match number, part of primary key
  locked varchar(12), 		# row locked for editing by user.  Can clear in application.
  updatedby varchar(200), 	# last updated by users
  final_type varchar(1),	# used in finals: Q=qarter, S=Semi, F=Final
  scheduled_time time,		# scheduled time
  actual_time time,		# actual time
  game_plan varchar(2000), 	# our game plan for the match.  Note: this is the only field 
				#   that is not match statistics but instead our analysis
  primary key (league, type, matchnum)
 );

#
# Match Instance Alliance
#
#  Scores and other details of a match tied to a given alliance
#
create table match_instance_alliance
 (
  league varchar(5),		# League, separating data for nationals with multiple fields
  type varchar(1), 		# Q=qualifying, P=practice, F=Final  part of primary key
  matchnum int,			# match number, part of primary key
  color varchar(1),		# R=Red, B=Blue
  locked varchar(12), 		# row locked for editing by user.  Can clear in application.
  updatedby varchar(200), 	# last updated by users
  score int,			# final score
  raw_points int, 		# raw points (prior to penalties)
  penalty_points int,		# penalty points
  other_points int,		# other points, might need in the future
  seed_points int,		# seed points - seed points in system
  primary key (league, type, matchnum, color)
 );

#
# Match - team
#
# Team entry for match.  6 teams per match
#
# This table is only used in 
# 
create table match_team
 (
  league varchar(5),		# League, foreign key to match_instance table
  type varchar(1), 		# foreign key to match_instance table
  matchnum int,			# match number, foreign key to match_instance table
  teamnum int,			# team number, foreign key to team table
  locked varchar(12), 		# row locked for editing by user.  Can clear in application.
  updatedby varchar(200), 	# last updated by users
  color varchar(1),		# R=Red, B=Blue
  position varchar(3),		# position played on field
  rating_offense int,		# 0-9 (9 high) rating on offense strength
  rating_defense int,		# 0-9 (9 high) rating on defense strength
  raw_points int,		# raw points scored
  human_points int,		# human points scored
  penalties int,		# penalty points
  MatchField_0 varchar(50),	# Match Field 0 (configured by params)
  MatchField_1 varchar(50),	# Match Field 1 (configured by params)
  MatchField_2 varchar(50),	# Match Field 2 (configured by params)
  MatchField_3 varchar(50),	# Match Field 3 (configured by params)
  MatchField_4 varchar(50),	# Match Field 4 (configured by params)
  MatchField_5 varchar(50),	# Match Field 5 (configured by params)
  MatchField_6 varchar(50),	# Match Field 6 (configured by params)
  MatchField_7 varchar(50),	# Match Field 7 (configured by params)
  MatchField_8 varchar(50),	# Match Field 8 (configured by params)
  MatchField_9 varchar(50),	# Match Field 9 (configured by params)
  MatchField_10 varchar(50),	# Match Field 10 (configured by params)
  MatchField_11 varchar(50),	# Match Field 11 (configured by params)
  MatchField_12 varchar(50),	# Match Field 12 (configured by params)
  MatchField_13 varchar(50),	# Match Field 13 (configured by params)
  MatchField_14 varchar(50),	# Match Field 14 (configured by params)
  MatchField_15 varchar(50),	# Match Field 15 (configured by params)
  MatchField_16 varchar(50),	# Match Field 16 (configured by params)
  MatchField_17 varchar(50),	# Match Field 17 (configured by params)
  MatchField_18 varchar(50),	# Match Field 18 (configured by params)
  MatchField_19 varchar(50),	# Match Field 19 (configured by params)
  match_notes varchar(1000),	# Match notes
  match_offense_analysis varchar(1000),		# offense analysis (text)
  match_defense_analysis varchar(1000), 	# defense analysis (text)
  match_pos_analysis varchar(1000),		# position analysis (text)
  match_with_recommendation varchar(1000),	# recommendation if partnered with
  match_against_recommendation varchar(1000),	# recommendation if partnered against
  primary key (league, type, matchnum, teamnum)
 );


#
# Match - Alliance
#
# Alliance entry for match (one per alliance)
# 
create table match_alliance_team
 (
  league varchar(5),		# League, foreign key to match_instance table
  type varchar(1), 		# foreign key to match_instance table
  matchnum int,			# match number, foreign key to match_instance table
  alliancenum int,		# Alliance - #1 through #8, foreign key to alliance table
  locked varchar(12), 		# row locked for editing by user.  Can clear in application.
  updatedby varchar(200), 	# last updated by users
  color varchar(1),		# R=Red, B=Blue
  position varchar(3),		# position played on field
  rating_offense int,		# 0-9 (9 high) rating on offense strength
  rating_defense int,		# 0-9 (9 high) rating on defense strength
  raw_points int,		# raw points scored
  human_points int,		# human points scored
  penalties int,		# penalty points
  primary key (league, type, matchnum, alliancenum)
 );


#
# championship listing table
#
create table championteam
 (
  league varchar(5),		# league "name" for nationals
  league_name varchar(25),	# long-form of league name
  teamnum  int, 		# FIRST team number - primary key
  primary key (league, teamnum)
);

#
# schedule import received from FRIST to be imported
# temporary table used to load data and begin processing
#

create table schedule
 (
  scheduled_time time, 		# schedule time of match
  type varchar(1),		# match type (see match table)
  matchnum int,		  	# match number
  blue1 int,			# blue teamnum
  blue2 int,			# blue teamnum
  blue3 int,			# blue teamnum
  red1 int,			# red teamnum
  red2 int,			# red teamnum
  red3 int,			# red teamnum
  primary key (type, matchnum)
 );
  


#
# Process Lock
#
# Lock table to lock various processes
#

create table process_lock
 (
   lock_id varchar(20), 	# id of lock in table
   locked varchar(12), 		# row locked for editing by user.  Can clear in application.
   primary key (lock_id)
 );
#
# set up control data in table as part of initialization
#
#
# insert needed locks
insert into process_lock (lock_id) values ('ranking');		# ranking process
insert into process_lock (lock_id) values ('finals_selection');	# ranking process
insert into process_lock (lock_id) values ('doc_topics');	# topics for docprocess


#
# Messages
#
# Message table to communicate with field
#

create table message
 (
   facility varchar(20), 	# unique facility
   message varchar(200),	# message
   locked varchar(12), 		# row locked for editing by user.  Can clear in application.
   primary key (facility)
 );
#
# set up control data in table as part of initialization
#
#
# insert facilities needed
insert into message (facility) values ('finals_selection');	# finals selection

#
# user profile and preferences
#
create table user_profile
 (
  user varchar(30),		# userd (matches that used for system authentication)
  matchview varchar(5),		# view preferences on matchlist view
  primary key (user)
 );

#
# stores all documentation
#
create table documentation
 (
   documentation varchar(20),	# title of this page, what the world will see
   topic varchar(20),		# what topic this page falls under, listed under the 'topic' table
   priority int,		# determines the order of the different doc pages under a topic
   locked varchar(12), 		# current editor of this row
   data varchar(5000),		# stores the actual information for this page
   primary key (documentation)
 );

#
# different topics the documentation fits under
# also, add process lock for this table in the process_lock table
#
create table topic
 (
   topic varchar(12),		# title of this category of documentation, the world will see this
   priority int,		# priority of this topic in relation to other topics
   description varchar(200),	# description of the topic, not needed
   primary key (topic)
 );

#
# stores a relationship between a documentation and a page
#
create table pagetodoc
(
   documentation varchar(20),	# title of the documentation
   page varchar(20),		# page the documentation can be accessed by
   primary key (documentation, page)
);

#
# generic system key/value table
#
# Note: use this table to store system values in the database
#
create table system_value
 (
   skey varchar(20),		# key index into values
   value varchar(40)		# value for the key
 );


##
## add any other data needed as part of setup
##

# set up default regional
insert into league (league, league_name) values ('Reg','Default Single Regional');

# setup alliances for default regional
insert into alliance (league, alliancenum) values ('Reg',1);
insert into alliance (league, alliancenum) values ('Reg',2);
insert into alliance (league, alliancenum) values ('Reg',3);
insert into alliance (league, alliancenum) values ('Reg',4);
insert into alliance (league, alliancenum) values ('Reg',5);
insert into alliance (league, alliancenum) values ('Reg',6);
insert into alliance (league, alliancenum) values ('Reg',7);
insert into alliance (league, alliancenum) values ('Reg',8);


