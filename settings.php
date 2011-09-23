<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

	$link ='<a href="'.$CFG->wwwroot.'/blocks/attendtools/managesessions.php">'.get_string('managesessions', 'block_attendtools').'</a>';
	$settings->add(new admin_setting_heading('block_attendtools_managesessions', '', $link));
	
}

