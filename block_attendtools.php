<?php

defined('MOODLE_INTERNAL') || die();

class block_attendtools extends block_base {

	function init() {
		$this->title = get_string('title', 'block_attendtools');
	}
	
	function applicable_formats() {
		return array('all' => true);
	}
	
	function instance_allow_multiple() {
		return false;
	}
	
	function get_content() {
		if ($this->content !== null) {
			return $this->content;
		}

		$this->content         =  new stdClass;
		$this->content->text   = 'Register attendance...?!';
		//$this->content->footer = 'Footer here...';

		return $this->content;
	}
}
