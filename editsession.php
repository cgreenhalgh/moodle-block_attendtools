<?php 

/* add/edit a block_attendtools_session */

require_once('../../config.php');
//require_once('locallib.php');

require_login();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot.'/blocks/attendtools/locallib.php');

class session_form extends moodleform {

	// cons
	function __construct($actionurl) {
		parent::moodleform($actionurl);
	}

	// form definition (overrided/specified)
	function definition() {
		$mform =& $this->_form;

		$mform->addElement('hidden', 'id');
		
		//$mform->addElement('editor', 'description_editor', get_string('description', 'block_attendtools'));
		//$mform->setType('description_editor', PARAM_RAW);
		//$mform->addRule('description_editor', null, 'required', null, 'client');
		$mform->addElement('text', 'description', get_string('description', 'block_attendtools'), array('size'=>'60'));
		$mform->setType('description', PARAM_TEXT);
		$mform->addRule('description', null, 'required', null, 'client');
		
		$mform->addElement('date_time_selector', 'sessdate', get_string('sessdate','block_attendtools'));
		
		$mform->addElement('duration', 'duration', get_string('duration','block_attendtools'));

		$mform->addElement('checkbox', 'maxregtimeset', '', get_string('maxregtimeset','block_attendtools'));
		
		$mform->addElement('date_time_selector', 'maxregtime', get_string('maxregtime','block_attendtools'));
		$mform->disabledIf('maxregtime', 'maxregtimeset', 'notchecked');
		
		$methods = get_attendtools_methods();
		$methodoptions = array();
		$codeid = 0;
		foreach ($methods as $m) {
			$methodoptions[$m->id] = $m->title;
			if ($m->name=='code')
				$codeid = $m->id;
		}
		$mform->addElement('select', 'methodid', get_string('method', 'block_attendtools'), $methodoptions);

		// seqsize
		$mform->addElement('text', 'codeseqsize', get_string('codeseqsize', 'block_attendtools'));
		$mform->setType('codeseqsize', PARAM_INT);
		$mform->setDefault('codeseqsize', '0');
		$mform->addRule('codeseqsize', null, 'numeric', null, 'client');
		$mform->disabledIf('codeseqsize', 'methodid', 'neq', $codeid);
		
		// seqmax
		$mform->addElement('checkbox', 'codeseqmaxset', '', get_string('codeseqmaxset','block_attendtools'));
		$mform->disabledIf('codeseqmax', 'methodid', 'neq', $codeid);
		
		$mform->addElement('text', 'codeseqmax', get_string('codeseqmax', 'block_attendtools'));
		$mform->setType('codeseqmax', PARAM_INT);
		//$mform->setDefault('codeseqsize', '0');
		$mform->addRule('codeseqmax', null, 'numeric', null, 'client');
		$mform->disabledIf('codeseqmax', 'methodid', 'neq', $codeid);
		$mform->disabledIf('codeseqmax', 'codeseqmaxset', 'notchecked');
		
		// TODO add or edit?
		$this->add_action_buttons(true, get_string('ok', 'block_attendtools'));
	}
}

// session id (0 for add)
$id = required_param('id', PARAM_INT);

$path = '/blocks/attendtools/editsession.php';
$urlparams = array('id'=>$id);

$baseurl = new moodle_url($path, $urlparams);
$PAGE->set_url($baseurl);

// system-wide setting(s)
$context = get_context_instance(CONTEXT_SYSTEM);
$PAGE->set_context($context);

require_capability('block/attendtools:managesessions', $context);

// base page
$PAGE->set_pagelayout('standard');

$managesessionsurl = new moodle_url('/blocks/attendtools/managesessions.php');

$mform = new session_form(new moodle_url($path, $urlparams));

global $DB;

if ($mform->is_cancelled()) {
	redirect($CFG->wwwroot . '/blocks/attendtools/managesessions.php');	
} else if ($data=$mform->get_data()) {
	// handle submit
	if (!$data->maxregtimeset)
		$data->maxregtime = 0;
	if (!$data->codeseqmaxset)
		$data->codeseqmax = null;
	//$data->description = $data->sdescription;
	//$data->description = $data->description_editor['text'];
	//$data->descriptionformat = $data->description_editor['format'];
	
	if ($data->id) {
		//debugging('edit session '.var_export($data, true));
		$DB->update_record('block_attendtools_session', $data);
		//redirect($baseurl);
		redirect($CFG->wwwroot . '/blocks/attendtools/managesessions.php');		
	}
	else {
		//debugging('add session');
		$data->id = $DB->insert_record('block_attendtools_session', $data);
		$urlparams['id'] = $data->id;
		//redirect(new moodle_url($path, $urlparams));
		redirect($CFG->wwwroot . '/blocks/attendtools/managesessions.php');	
	}
	
} else {
	// form view
	if ($id) {
		$strtitle = get_string('editsession', 'block_attendtools');
		$data = $DB->get_record('block_attendtools_session', array('id'=>$id));
		if (!$data) {
			print_error('sessionnotfound', 'block_attendtools', $managesessionurl);
		}
		$data->maxregtimeset = ($data->maxregtime!=0);
		$data->codeseqmaxset = ($data->codeseqmax!==null);
		
		//$data->sdescription = $data->description;
	}
	else {
		$strtitle = get_string('addsession', 'block_attendtools');
		$data = new stdClass;
	}
		
	$PAGE->set_title($strtitle);
	$PAGE->set_heading($strtitle);
	
	$settingsurl = new moodle_url('/admin/settings.php?section=blocksettingattendtols');
	$PAGE->navbar->add(get_string('blocks'));
	$PAGE->navbar->add(get_string('pluginname', 'block_attendtools'), $settingsurl);
	$PAGE->navbar->add(get_string('managesessions', 'block_attendtools'), $managesessionsurl);
	$PAGE->navbar->add($strtitle);
	
	echo $OUTPUT->header();
	echo $OUTPUT->heading($strtitle, 2);
	
	// display form
	$mform->set_data($data);
	$mform->display();

	echo $OUTPUT->footer();
}
