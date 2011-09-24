<?php 

defined('MOODLE_INTERNAL') || die();

function get_default_methods() {
	$ms = array();
	$m = new stdClass();
	$m->name = 'code';
	$m->title = get_string('method'.$m->name.'title', 'block_attendtools');
	$m->description = get_string('method'.$m->name.'description', 'block_attendtools');
	$ms[] = $m;
	return $ms;
}

function get_attendtools_methods() {
	global $DB;
	
	return $DB->get_records('block_attendtools_method');
}

//define('CODE_LENGTH', 6);
define('CODE_LENGTH', 10);

function get_code($input) {

	//$chars = array('2','3','4','5','6','7','8','9','A','B','C','D','E','F','G','H','J','K','L','M','N','P','Q','R','S','T','U','V','W','X','Y','Z');
	$chars = array('0','1','2','3','4','5','6','7','8','9');
	// raw...
	$sha1 = sha1($input, true);
	$out = '';
	for ($len = 0; $len<CODE_LENGTH; $len=$len+1) {
		// 5 bits = 32 options
	 	$out .= $chars[ord($sha1[$len]) % count($chars)];
	 	//debugging('char '.$len.', byte='.ord($sha1[$len]).' -> '.(ord($sha1[$len]) & 0x1f).' -> '.$out);
	}
	//debugging('sha1 '.$input.' -> '.bin2hex($sha1).' -> '.$out);
	return $out;
}