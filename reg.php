<?php 

require_once('../../config.php');

require_once($CFG->libdir . '/formslib.php');

class reg_form extends moodleform {

	// cons
	function __construct($actionurl) {
		parent::moodleform($actionurl);
	}

	// form definition (overrided/specified)
	function definition() {
		$mform =& $this->_form;

		$mform->addElement('text', 'sessionid', get_string('regsessionid', 'block_attendtools'));
		$mform->setType('sessionid', PARAM_INT);
		//$mform->setDefault('codeseqsize', '0');
		$mform->addRule('sessionid', null, 'numeric', null, 'client');
		$mform->addRule('sessionid', null, 'required', null, 'client');

		$mform->addElement('text', 'code', get_string('regcode', 'block_attendtools'));
		$mform->setType('code', PARAM_TEXT);
		//$mform->setDefault('codeseqsize', '0');
		//$mform->addRule('code', null, 'numeric', null, 'client');
		$mform->addRule('code', null, 'required', null, 'client');
		
		$mform->addElement('text', 'username', get_string('regusername', 'block_attendtools'));
		$mform->setType('username', PARAM_TEXT);
		//$mform->setDefault('codeseqsize', '0');
		$mform->addRule('username', null, 'required', null, 'client');
		
		// TODO add or edit?
		$this->add_action_buttons(true, get_string('regok', 'block_attendtools'));
	}
}

$path = '/blocks/attendtools/reg.php';

$baseurl = new moodle_url($path);
$PAGE->set_url($baseurl);

// system-wide setting(s)
$context = get_context_instance(CONTEXT_SYSTEM);
$PAGE->set_context($context);

// base page
$PAGE->set_pagelayout('popup');
$strtitle = get_string('regtitle', 'block_attendtools');
$PAGE->set_title($strtitle);
$PAGE->set_heading($strtitle);

$mform = new reg_form(new moodle_url($path));

global $DB;

define('BLACKLIST_MINUTES', 10);
define('BLACKLIST_COUNT', 3);

if ($mform->is_cancelled()) {
	redirect($baseurl);
} else {

	echo $OUTPUT->header();
	
	if ($data=$mform->get_data()) {
		// handle submit
		$ipaddress = $_SERVER['REMOTE_ADDR'];
		$time = time()-BLACKLIST_MINUTES*60;
		$username = $data->username;
		// blacklisting...
		if ($DB->count_records_select('block_attendtools_blacklist', 'username = ? AND time >= ?', array($data->username,$time)) > BLACKLIST_COUNT) {
			echo '<p>'.get_string('blacklistedusername', 'block_attendtools', BLACKLIST_MINUTES).'</p>';			
		} else if ($DB->count_records_select('block_attendtools_blacklist', 'ipaddress = ? AND time >= ?', array($ipaddress,$time)) > BLACKLIST_COUNT) {
			echo '<p>'.get_string('blacklistedipaddress', 'block_attendtools', BLACKLIST_MINUTES).'</p>';			
		} else {
		
			// check attendance
			$attendance = $DB->get_record('block_attendtools_attendance', array('sessionid'=>$data->sessionid,'codecode'=>$data->code));
			if ($attendance) {
				if ($attendance->username==$data->username) {
					echo '<p>'.get_string('codealreadyregistered', 'block_attendtools').'</p>';
				} else if ($attendance->codeused) {
					echo '<p>'.get_string('codealreadyused', 'block_attendtools').'</p>';				
				} else {
					$session = $DB->get_record('block_attendtools_session', array('id'=>$attendance->sessionid));
					if ($session && $session->codeseqmax!==null && $attendance->codeseq>$session->codeseqmax) {
						echo '<p>'.get_string('codeexceedsmax', 'block_attendtools').'</p>';						
					} else if ($session && $session->maxregtime && time()>$session->maxregtime) {
						echo '<p>'.get_string('codetoolate', 'block_attendtools').'</p>';						
					} else {
						if ($DB->count_records('block_attendtools_attendance', array('sessionid'=>$data->sessionid,'username'=>$data->username)))
						{
							echo '<p>'.get_string('alreadyregistered', 'block_attendtools').'</p>';								
						}						
						else {
							// OK
							$attendance->codeused = 1;
							$attendance->username = $data->username;
							$attendance->present = 1;
							$attendance->timetaken = time();
							$DB->update_record('block_attendtools_attendance', $attendance);
							
							echo '<p>'.get_string('codeok', 'block_attendtools').'</p>';				
						}
					}
				}
			} else {
				if (!$DB->count_records('block_attendtools_session', array('id'=>$data->sessionid)))
				{
					echo '<p>'.get_string('codesessionunknown', 'block_attendtools').'</p>';				
				} 
				else {
					echo '<p>'.get_string('codeunknown', 'block_attendtools').'</p>';
					// blacklist...
					$blacklist = new stdClass();
					$blacklist->sessionid = $data->sessionid;
					$blacklist->username = $data->username;
					$blacklist->time = time();
					$blacklist->ipaddress = $_SERVER['REMOTE_ADDR'];
					$blacklist->id = $DB->insert_record('block_attendtools_blacklist', $blacklist);
				}
			}
		}
			
		echo $OUTPUT->single_button($baseurl, get_string('regagain', 'block_attendtools'));
		
	} else {
		// form view
		$data = new stdClass();
		if (isset($USER->username))
			$data->username = $USER->username;
		
		// display form
		$mform->set_data($data);
		$mform->display();
	}
	
	echo $OUTPUT->footer();
}
