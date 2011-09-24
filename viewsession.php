<?php 

/* add/edit a block_attendtools_session */

require_once('../../config.php');
//require_once('locallib.php');

require_login();

// session id (0 for add)
$id = required_param('id', PARAM_INT);

$path = '/blocks/attendtools/viewsession.php';
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

global $DB;

$strtitle = get_string('viewsession', 'block_attendtools');
$session = $DB->get_record('block_attendtools_session', array('id'=>$id));
if (!$session) {
	print_error('sessionnotfound', 'block_attendtools', $managesessionurl);
}

$PAGE->set_title($strtitle);
$PAGE->set_heading($strtitle);
	
$settingsurl = new moodle_url('/admin/settings.php?section=blocksettingattendtols');
$PAGE->navbar->add(get_string('blocks'));
$PAGE->navbar->add(get_string('pluginname', 'block_attendtools'), $settingsurl);
$PAGE->navbar->add(get_string('managesessions', 'block_attendtools'), $managesessionsurl);
$PAGE->navbar->add($strtitle);
	
echo $OUTPUT->header();

$strsessdate = get_string('sessdate', 'block_attendtools');
$strdescription = get_string('description', 'block_attendtools');
$stractions = get_string('actions', 'block_attendtools');

echo '<table border="0" cellspacing="2" cellpadding="4" class="generalbox boxaligncenter"><tr>';
echo '<th class="header" scope="col">'.$strsessdate.'</th>';
echo '<th class="header" scope="col">'.$strdescription.'</th>';
echo '</tr>';
echo '<tr><td>'.userdate($session->sessdate).'</td><td>'.$session->description.'</td></tr>';

$strusername = get_string('username', 'block_attendtools');
$strpresent = get_string('present', 'block_attendtools');
$strtimetaken = get_string('timetaken', 'block_attendtools');
$strremarks = get_string('remarks', 'block_attendtools');

echo '<table border="0" cellspacing="2" cellpadding="4" class="generalbox boxaligncenter"><tr>';
echo '<th class="header" scope="col">'.$strusername.'</th>';
echo '<th class="header" scope="col">'.$strpresent.'</th>';
echo '<th class="header" scope="col">'.$strtimetaken.'</th>';
echo '<th class="header" scope="col">'.$strremarks.'</th>';
echo '</tr>';

$attendances = $DB->get_records('block_attendtools_attendance', array('sessionid'=>$id,'present'=>1), 'username ASC');
foreach ($attendances as $attendance) {
	echo '<tr><td>'.$attendance->username.'</td><td>'.($attendance->present ? 'P' : 'A').'</td><td>'.userdate($attendance->timetaken).'</td><td>'.$attendance->remarks.'</td></tr>';
}

echo '</table>';

echo $OUTPUT->footer();
