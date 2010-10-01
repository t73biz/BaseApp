<?php
/* GroupsUser Fixture generated on: 2010-04-11 13:04:24 : 1271005344 */
class GroupsUserFixture extends CakeTestFixture {
	var $name = 'GroupsUser';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'group_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	var $records = array(
		array(
			'id' => 1,
			'group_id' => 1,
			'user_id' => 1,
			'created' => '2010-04-11 13:02:24',
			'modified' => '2010-04-11 13:02:24'
		),
	);
}
?>