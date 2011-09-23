A Moodle2 block to support a version of attendance monitoring, initially using 
unique codes handed out to students in the session, for subsequent entry into the 
system (cf. phone credit scratch-cards).

Chris Greenhalgh (Computer Science), The University of Nottingham

Copyright 2011, The University of Nottingham

Status:
	- skeleton plugin
	
Next steps:
    - allow sessions to be created (session form)
    - allow sessions to be listed (manage sessions)
    - allow session to be printed (and generate codes)
    - allow code to be entered
    - allow attendance to be viewed
    - blacklisting support
    - allow attendnace to be exported
	
Changes:

Design notes:
	- initial use case works WITHOUT student accounts on Moodle, and WITHOUT 
	  moodle courses/activities/enrolment
	- extension(s) will allow mapping to courses and probably Attendance activity
	  where students accounts, courses and enrolment are maintained
