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
