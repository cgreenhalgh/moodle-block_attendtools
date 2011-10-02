A Moodle2 block to support a version of attendance monitoring, initially using 
unique codes handed out to students in the session, for subsequent entry into the 
system (cf. phone credit scratch-cards).

Chris Greenhalgh (Computer Science), The University of Nottingham

Copyright 2011, The University of Nottingham

Status:
	- can create sessions
    - can list sessions
    - can generate and print codes
    - can register using code
    - support for temporary blacklisting to limit exposure to brute force
    - can view session attendance
    - can export (CSV) attendance for all sessions
    - minimal block provides link to registration form
    (no connections to native moodle functionality, e.g. courses, 
     or attendance block)
	
Next steps:
    - allow sessions to be deleted
    ? allow sessions to be grouped
    - allow finer-grained permissions for printing, etc. (currently admin)
	
Changes:
    - first version
    - site-wide option to require login to register
    - output student names (where known)

Design notes:
	- initial use case works WITHOUT student accounts on Moodle, and WITHOUT 
	  moodle courses/activities/enrolment
	- extension(s) will allow mapping to courses and probably Attendance activity
	  where students accounts, courses and enrolment are maintained
