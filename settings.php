<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

	$link ='<a href="'.$CFG->wwwroot.'/blocks/attendtools/managesessions.php">'.get_string('managesessions', 'block_attendtools').'</a>';
	$settings->add(new admin_setting_heading('block_attendtools/managesessions', '', $link));
	
	$settings->add(new admin_setting_configcheckbox('block_attendtools/requirelogin', get_string('requirelogin', 'block_attendtools'),
		get_string('requirelogindesc', 'block_attendtools'), 1));
	
}

