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
$PAGE->navbar->add(get_string('managesessions', 'block_attendtools'), $manageorgdataurl);
echo $OUTPUT->header();

// Do something useful...

echo $OUTPUT->footer();

