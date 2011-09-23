<?php 

/* add/edit a block_attendtools_session */

require_once('../../config.php');
//require_once('locallib.php');

require_login();

// session id (0 for add)
$id = required_param('id', PARAM_INT);

require_once($CFG->libdir . '/formslib.php');

class session_form extends moodleform {

	// cons
	function __construct($actionurl) {
		parent::moodleform($actionurl);
	}

	// form definition (overrided/specified)
	function definition() {
		$mform =& $this->_form;

		$mform->addElement('date_time_selector', 'sessdate', get_string('sessdate','block_attendtools'));
		
		for ($i=0; $i<=23; $i++) {
			$hours[$i] = sprintf("%02d",$i);
		}
		for ($i=0; $i<60; $i+=5) {
			$minutes[$i] = sprintf("%02d",$i);
		}
		$durtime = array();
		$durtime[] =& MoodleQuickForm::createElement('select', 'hours', get_string('hour', 'form'), $hours, false, true);
		$durtime[] =& MoodleQuickForm::createElement('select', 'minutes', get_string('minute', 'form'), $minutes, false, true);
		$mform->addGroup($durtime, 'duration', get_string('duration','attendtools'), array(' '), true);

		$mform->addElement('checkbox', 'maxregtimeset', '', get_string('maxregtimeset','block_attendtools'));
		
		$mform->addElement('date_time_selector', 'maxregtime', get_string('maxregtime','block_attendtools'));
		$mform->disabledIf('maxregtime', 'maxregtimeset', 'notchecked');
		
		$mform->addElement('editor', 'description', get_string('description', 'block_attendtools'), null, array('maxfiles'=>EDITOR_UNLIMITED_FILES, 'noclean'=>true, 'context'=>$modcontext));
		$mform->setType('description', PARAM_RAW);
		
		// TODO method...
		
		// TODO add or edit?
		$this->add_action_buttons(true, get_string('edit', 'block_attendtools'));
	}
}

$path = '/blocks/attendtools/session.php';
$urlparams = array('id'=>$id);

