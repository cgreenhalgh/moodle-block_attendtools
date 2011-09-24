<?php

/**
 * default attendance tools methods...
 */

require_once($CFG->dirroot.'/blocks/attendtools/locallib.php');

/**
 * Post installation procedure
 */
function xmldb_block_attendtools_install() {
    global $DB;

    // populate default methods
	$result = true;
    $ms = get_default_methods();
    foreach ($ms as $m) {
		$result = $result && $DB->insert_record('block_attendtools_method', $m);
	}

	return $result;
}
