<?php 

require_once('../../config.php');
//require_once('locallib.php');

require_login();

require_once($CFG->dirroot.'/blocks/attendtools/locallib.php');

// session id (0 for add)
$id = required_param('id', PARAM_INT);
$confirm = optional_param('confirm', '', PARAM_TEXT);

$path = '/blocks/attendtools/printcodes.php';
$urlparams = array('id'=>$id);

$baseurl = new moodle_url($path, $urlparams);
$PAGE->set_url($baseurl);

// system-wide setting(s)
$context = get_context_instance(CONTEXT_SYSTEM);
$PAGE->set_context($context);

require_capability('block/attendtools:managesessions', $context);

// base page
$PAGE->set_pagelayout('popup');
$PAGE->set_title(get_string('printcodes', 'block_attendtools'));
echo $OUTPUT->header();

$managesessionsurl = new moodle_url('/blocks/attendtools/managesessions.php');

$session = $DB->get_record('block_attendtools_session', array('id'=>$id));
if (!$session)
	print_error('sessionnotfound', 'block_attendtools', $managesessionsurl);

$method = $DB->get_record('block_attendtools_method', array('id'=>$session->methodid));
if (!$method || $method->name!='code')
	print_error('methodcodeonly', 'block_attendtools', $managesessionsurl);

$confirmhash = md5(isset($session->lastprinted) ? $session->lastprinted : 0);
if ($confirm!=$confirmhash) {
	// confirm?!
	$confirmurl = new moodle_url($path,array('id'=>$id,'confirm'=>$confirmhash));
	if ($session->printed) {
		// fix me- user name
		$lastprintedby = 'user '.$session->lastprintedby;
		$strparams = new stdClass();
		$strparams->printed = $session->printed;
		$strparams->lastprinted = userdate($session->lastprinted);
		$strparams->lastprintedby = $lastprintedby;
		echo $OUTPUT->confirm(get_string('confirmprintcodes2', 'block_attendtools', $strparams), $confirmurl, $managesessionsurl);
	}
	else {
		echo $OUTPUT->confirm(get_string('confirmprintcodes1', 'block_attendtools'), $confirmurl, $managesessionsurl);
	}
}
else {
	// do it...
	$max = $session->codeseqmax;	
	$size = $session->codeseqsize;
	if ($max===null)
		$max = $size;
	else if ($max>$size)
		$max = $size;
	// existing?
	$tod = gettimeofday();
	$seed = $tod['sec'].' '.$tod['usec'];
	$seed .= ' '.$_SERVER['REMOTE_ADDR'].' '.$_SERVER['REMOTE_PORT'].' ';
	$seed .= ' '.$session->id.' ';
	
	echo '<table>';
	$skip = 0;
	$registerurl = new moodle_url('/blocks/attendtools/reg.php');
	for ($seq=1; $seq<=$max; $seq=$seq+1) {
		$code = $DB->get_record('block_attendtools_attendance', array('sessionid'=>$session->id, 'codeseq'=>$seq));
		if (!$code) {
			$code = new stdClass();
			$code->sessionid = $session->id;
			$code->methodid = $session->methodid;
			$code->codeused = 0;
			$code->codeseq = $seq;
			while (true) {
				$code->codecode = get_code($seed.($seq+$skip));
				debugging('generate seq '.$seq.': '.$code->codecode);
				if ($DB->count_records('block_attendtools_attendance', array('sessionid'=>$session->id,'codecode'=>$code->codecode)))
				{
					debugging('skipping duplicate code '.$code->codecode.' ('.$skip.' duplicates so far)');
					$skip = $skip+1;
					if ($skip > 10*$max) {
						print_error('errorgeneratingcodes', 'block_attendtools', $managesessionsurl);
					}
					continue;
				}
				break;
			}
			debugging('insert code '.var_export($code,true));
			$code->id = $DB->insert_record('block_attendtools_attendance', $code);
		}		
		echo '<tr><td>'.$registerurl.'&nbsp;'.get_string('regsessionid','block_attendtools').':&nbsp;<span>'.$code->sessionid.'</span>&nbsp;'.get_string('regcode','block_attendtools').':&nbsp;<span>'.$code->codecode.'</span>&nbsp;('.$seq.'/'.$max.', ',$session->description.')</td></tr>';
	}	
	echo '</table>';
	
	$session->printed = $session->printed+1;
	$session->lastprinted = time();
	$session->lastprintedby = $USER->id;
	$DB->update_record('block_attendtools_session', $session);
}
echo $OUTPUT->footer();
