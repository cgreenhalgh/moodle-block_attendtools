<?php 
/* 
 * blocks/attendtools manage sessions... 
 */

require_once('../../config.php');
//require_once('locallib.php');

require_login();

$urlparams = array();
$path = '/blocks/attendtools/managesessions.php';
$baseurl = new moodle_url($path, $urlparams);
$PAGE->set_url($baseurl);

$context = get_context_instance(CONTEXT_SYSTEM);
$PAGE->set_context($context);

require_capability('block/attendtools:managesessions', $context);

// standard page & heading, etc
$strmanage = get_string('managesessions', 'block_attendtools');

$PAGE->set_pagelayout('standard');
$PAGE->set_title($strmanage);
$PAGE->set_heading($strmanage);

$settingsurl = new moodle_url('/admin/settings.php?section=blocksettingattendtools');
$managesessions = new moodle_url($path); //, $urlparams);
$PAGE->navbar->add(get_string('blocks'));
$PAGE->navbar->add(get_string('pluginname', 'block_attendtools'), $settingsurl);
$PAGE->navbar->add(get_string('managesessions', 'block_attendtools'), $managesessions);
echo $OUTPUT->header();

// Do something useful...

// list sessions
//
// SQL - raw
//require_once($CFG->libdir.'/tablelib.php');
//$table = new table_sql('sessiontable');
//$table->set_sql('*', $CFG->prefix.'block_attendtools_session', '1');
//$table->out(40,true);

$strsessdate = get_string('sessdate', 'block_attendtools');
$strdescription = get_string('description', 'block_attendtools');
$stractions = get_string('actions', 'block_attendtools');

echo '<table border="0" cellspacing="2" cellpadding="4" class="generalbox boxaligncenter"><tr>';
echo '<th class="header" scope="col">'.$strsessdate.'</th>';
echo '<th class="header" scope="col">'.$strdescription.'</th>';
echo '<th class="header" scope="col">'.$stractions.'</th>';
echo '</tr>';

global $DB;

$sessions = $DB->get_records('block_attendtools_session', null, 'sessdate ASC');
foreach ($sessions as $session) {
	echo '<tr><td>'.userdate($session->sessdate).'</td><td>'.$session->description.'</td><td>';
	echo $OUTPUT->action_icon(new moodle_url('/blocks/attendtools/editsession.php',
											array('id' => $session->id)),
		new pix_icon('t/edit', get_string('editsession', 'block_attendtools')));
	
	echo $OUTPUT->action_icon(new moodle_url('/blocks/attendtools/viewsession.php',
							array('id' => $session->id)),
		new pix_icon('t/preview', get_string('viewsession', 'block_attendtools')));
	
	$method = $DB->get_record('block_attendtools_method', array('id'=>$session->methodid));
	if ($method->name=='code') {
		echo $OUTPUT->action_icon(new moodle_url('/blocks/attendtools/printcodes.php',
								array('id' => $session->id)),
			new pix_icon('t/print', get_string('printcodes', 'block_attendtools'), 'block_attendtools'));
	}
		
	echo '</td></tr>';
}

echo '</table>';
// add session button
$options = array('id'=>0);
echo $OUTPUT->single_button(new moodle_url('/blocks/attendtools/editsession.php', $options), get_string('addsession', 'block_attendtools'));

echo $OUTPUT->footer();

