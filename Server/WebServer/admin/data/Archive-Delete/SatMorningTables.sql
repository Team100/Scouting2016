#
# $Revision: 1.1 $
# $Date: 2010/03/20 13:03:43 $
#




#
# Process Lock
#
# Lock table to lock various processes
#

create table process_lock
 (
   lock_id varchar(12), 	# id of lock in table
   locked varchar(12), 		# row locked for editing by user.  Can clear in application.
   primary key (lock_id)
 );
# insert needed locks
insert into process_lock (lock_id) values ('ranking');		# ranking process



#
# Messages
#
# Message table to communicate with field
#

create table message
 (
   facility varchar(12), 	# unique facility
   message varchar(200),	# message
   locked varchar(12), 		# row locked for editing by user.  Can clear in application.
   primary key (facility)
 );
# insert facilities needed
insert into message (facility) values ('finals');

#
# alter tables
#
