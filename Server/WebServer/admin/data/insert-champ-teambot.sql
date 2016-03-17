#
# $Revision: 3.0 $
# $Date: 2016/03/14 23:00:02 $
#
# Insert into teambot our league
#

insert into teambot (teamnum) select teamnum from 
  championteam where league_name='Galileo';

