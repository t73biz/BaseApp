<?php
/* GroupsUser Test cases generated on: 2010-04-11 13:04:24 : 1271005344*/
App::import('Model', 'GroupsUser');

class GroupsUserTestCase extends CakeTestCase {
	var $fixtures = array('app.groups_user', 'app.group', 'app.user');

	function startTest() {
		$this->GroupsUser =& ClassRegistry::init('GroupsUser');
	}

	function endTest() {
		unset($this->GroupsUser);
		ClassRegistry::flush();
	}

}
?>