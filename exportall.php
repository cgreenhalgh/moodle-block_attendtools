<?php 

require_once('../../config.php');
//require_once('locallib.php');

require_login();

$context = get_context_instance(CONTEXT_SYSTEM);
require_capability('block/attendtools:managesessions', $context);

$usernames = $DB->get_fieldset_select('block_attendtools_attendance', 'DISTINCT username');

$sessions = $DB->get_records('block_attendtools_session', null, 'sessdate ASC');

require_once($CFG->libdir . '/csvlib.class.php');

$delimited_name = 'comma';
$delimiter = csv_import_reader::get_delimiter($delimiter_name);
$filename = clean_filename("attendance");
$filename .= clean_filename('-' . gmdate("Ymd_Hi"));
$filename .= '.csv';

header("Content-Type: text/csv\n");
header("Content-Disposition: attachment; filename=$filename");
header('Expires: 0');
header('Cache-Control: must-revalidate,post-check=0,pre-check=0');
header('Pragma: public');

$fp = fopen('php://output', 'w');

$headers = array('username');
foreach ($sessions as $session) {
	$headers[] = $session->description;
}
fputcsv($fp, $headers);

foreach ($usernames as $username) {
	$values = array($username);
	foreach ($sessions as $session) {
		$attendance = $DB->get_record('block_attendtools_attendance',array('sessionid'=>$session->id,'username'=>$username));
		$values[] = $attendance->present;
	}
	fputcsv($fp, $values);	
}

fclose($fp);
