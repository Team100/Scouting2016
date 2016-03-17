#
# $Revision: 1.1 $
# $Date: 2010/04/01 02:04:17 $
#


##
## Alter tables to rev to V2
##


Team
  sponsors varchar(205),	# team sponsors
  history varchar(1000),	# history of events from FIRST site


create table match_instance
  game_plan varchar(2000), 	# our game plan for the match


pitfields
