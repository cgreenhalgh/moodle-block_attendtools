<?php 

function xmldb_block_attendtools_upgrade($oldversion) {
	global $DB;

	$dbman = $DB->get_manager();

	if ($oldversion < 2011100400) {
	
		// Define field requirelogin to be added to block_attendtools_session
		$table = new xmldb_table('block_attendtools_session');
		$field = new xmldb_field('requirelogin', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, null, null, '0', 'lastprintedby');
	
		// Conditionally launch add field requirelogin
		if (!$dbman->field_exists($table, $field)) {
			$dbman->add_field($table, $field);
		}
	
		// attendtools savepoint reached
		upgrade_block_savepoint(true, 2011100400, 'attendtools');
	}

}
