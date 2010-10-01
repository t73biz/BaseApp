<?php
class UsersController extends AppController {

	var $name = 'Users';
	var $components = array('ControllerList');

	function login() {

	}

    function logout() {
        $this->redirect($this->Auth->logout());
    }

	function index() {
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(sprintf(__('Invalid %s', true), 'user'));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('user', $this->User->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->User->create();
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(sprintf(__('The %s has been saved', true), 'user'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(sprintf(__('The %s could not be saved. Please, try again.', true), 'user'));
			}
		}
		$groups = $this->User->Group->find('list');
		if(empty($groups))
		{
			$this->Session->setFlash(sprintf(__('There have not been any %ss created yet. Please create a %s first', true), 'group', 'group'));
			$this->redirect(array('controller' => 'groups', 'action' => 'add'));
		}
		$this->set(compact('groups'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(sprintf(__('Invalid %s', true), 'user'));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(sprintf(__('The %s has been saved', true), 'user'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(sprintf(__('The %s could not be saved. Please, try again.', true), 'user'));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->User->read(null, $id);
		}
		$groups = $this->User->Group->find('list');
		$this->set(compact('groups'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(sprintf(__('Invalid id for %s', true), 'user'));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->User->delete($id)) {
			$this->Session->setFlash(sprintf(__('%s deleted', true), 'User'));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(sprintf(__('%s was not deleted', true), 'User'));
		$this->redirect(array('action' => 'index'));
	}

	private function setPermissions($group, $controller, $action, $permission)
	{
		// First check to make sure that the controller is already set up as an ACO
		$aco = new Aco( );
		$rootAco = $aco->findByAlias( 'ROOT' );
		// Set up $controllerAco if it's not present.
		$controllerAco = $aco->findByAlias( $controller );
		//$this->Administrator->query( 'SELECT Aco.* From acos AS Aco LEFT JOIN acos AS Aco0 ON Aco0.alias = "'.$controller.'" LEFT JOIN acos AS Aco1 ON Aco1.lft > Aco0.lft && Aco1.rght < Aco0.rght AND Aco1.alias = "ROOT" WHERE Aco.lft <= Aco0.lft AND Aco.rght >= Aco0.rght ORDER BY Aco.lft DESC' ) );
		if (empty( $controllerAco ))
		{
			$aco->create();
			$aco->save( array (
				'alias' => $controller, 'parent_id' => $rootAco['Aco']['id']
			) );
			$controllerAco = $aco->findByAlias( $controller );
			//$this->Administrator->query( 'SELECT Aco.* From acos AS Aco LEFT JOIN acos AS Aco0 ON Aco0.alias = "'.$controller.'" LEFT JOIN acos AS Aco1 ON Aco1.lft > Aco0.lft && Aco1.rght < Aco0.rght AND Aco1.alias = "ROOT" WHERE Aco.lft <= Aco0.lft AND Aco.rght >= Aco0.rght ORDER BY Aco.lft DESC' ) );
		}

		// Set up $actionAcoif it's not present.
		$actionAco = $aco->find( array (
			'parent_id' => $controllerAco['Aco']['id'], 'alias' => $action
		) );
		if (empty( $actionAco ))
		{
			$aco->create();
			$aco->save( array (
				'alias' => $action, 'parent_id' => $controllerAco['Aco']['id']
			) );
			$actionAco = $aco->find( array (
				'parent_id' => $controllerAco['Aco']['id'], 'alias' => $action
			) );
		}

		// Set up perms now.
		if ($permission == 'allow')
		{
			$this->Acl->allow( array (
				'model' => 'Group', 'foreign_key' => $group['Group']['id']
			), $controller . '/' . $action );
		} else
		{
			$this->Acl->deny( array (
				'model' => 'Group', 'foreign_key' => $group['Group']['id']
			), $controller . '/' . $action );
		}
	}

	function acl($group_id = null, $controller = null)
	{
		Configure::write( 'debug', 2 );
				if ($group_id == null)
		{
			$this->set( 'groups', $this->User->Group->find('all') );
		}
		else if ($controller == null)
		{
			// Prevents a heap-load of error messages from coming up if DEBUG = 2.
			Configure::write( 'debug', 2 );
			$group = $this->User->Group->read( null, $group_id );
			// See http://cakebaker.42dh.com/2006/07/21/how-to-list-all-controllers/
			$controllerList = $this->ControllerList->get();
			$this->set( 'controllerList', $controllerList );
			$this->set( 'group', $group );
		}
	}

	function acl_set($group_id = null, $controllerSelect = null, $action = null, $permission = null)
	{
		// Prevents a heap-load of error messages from coming up if DEBUG = 2.
		Configure::write( 'debug', 2 );
		$this->layout = null;
		$group = $this->User->Group->read( null, $group_id );
		$this->setPermissions( $group, $controllerSelect, $action, $permission );
		if ($permission == 'allow')
		{
			$msg =  $group['Group']['name'] . ' has been granted access to ' . $controllerSelect . '/' . $action . ".\n<br />";
		}
		else
		{
			$msg =  $group['Group']['name'] . ' has been denied access to ' . $controllerSelect . '/' . $action . ".\n<br />";
		}
		$controllerList = $this->ControllerList->get();
		$controllerPerms = '';
		$aco = new Aco( );
		foreach ( $controllerList as $controller => $actions )
		{
			if ($controller == $controllerSelect)
			{
				foreach ( $actions as $key => $action )
				{
					$controllerPerms[$controller][$action] = $this->Acl->check( $group, $controller . '/' . $action, '*' );
				}
			}
		}
		$this->set(compact('controllerList', 'controllerPerms', 'group', 'msg'));
		$this->viewPath = 'elements';
		$this->render('display_action_list');
	}

	function display_action_list($controllerSelect = null, $group_id = null)
	{
		Configure::write( 'debug', 2 );
		$this->layout = null;
		$group = $this->User->Group->read( null, $group_id );
		$controllerList = $this->ControllerList->get();
		$controllerPerms = '';
		$aco = new Aco( );
		foreach ( $controllerList as $controller => $actions )
		{
			if ($controller == $controllerSelect)
			{
				foreach ( $actions as $key => $action )
				{
					$controllerPerms[$controller][$action] = $this->Acl->check( $group, $controller . '/' . $action, '*' );
				}
			}
		}
		$this->set(compact('controllerList', 'controllerPerms', 'group'));
		$this->viewPath = 'elements';
		$this->render('display_action_list');
	}

	/**
	 * Dangerous Function
	 * Will reset ACL to base default
	 * Use with Caution .. It has been set to a private function to prevent unwanted use.
	 * This is strictly Utilitarian Function. Don't use w/o previous knowledge of what it does
	 */
	function acl_reset() {
		/**
		 * First, Empty ACL Tables
		 */
		$this->User->query("TRUNCATE TABLE `aros`;");
		$this->User->query("TRUNCATE TABLE `acos`;");
		$this->User->query("TRUNCATE TABLE `aros_acos`;");

		/**
		 * Next We setup the Root ACL
		 * It is CRITICAL that there be admin user with User.Id == 1
		 * This will Fail without it.
		 */
		$aro = new aro();
		$groups = $this->User->Group->findAll();

		foreach ($groups as $group) {
			$aro->create();
			$aro->save(array (
				'model' => 'Group',
				'foreign_key' => $group['Group']['id'],
				'parent_id' => null,
				'alias' => $group['Group']['name']
			));
		}
		$users = $this->User->findAll();
		foreach ($users as $user) {
			if ($user['User']['id'] != 1)
			$this->User->save($user);
		}

		$parent = $aro->findByAlias('Admin');
		$parentId = $parent['Aro']['id'];

		$aro->create();
		$aro->save(array (
			'model' => 'User',
			'foreign_key' => 1,
			'parent_id' => $parentId,
			'alias' => 'User::1'
		));

		$aco = new Aco();
		$aco->create();
		$aco->save(array (
			'model' => null,
			'foreign_key' => null,
			'parent_id' => null,
			'alias' => 'ROOT'
		));

		$parent = $aco->findByAlias('ROOT');
		$rootId = $parent['Aco']['id'];

		$controllerList = $this->ControllerList->get();
		foreach ($controllerList as $controller => $actions) {
			$aco->create();
			$aco->save(array (
				'model' => $controller,
				'foreign_key' => null,
				'parent_id' => $rootId,
				'alias' => $controller
			));
			foreach ($actions as $action) {
				$parent = $aco->findByAlias($controller);
				$parentId = $parent['Aco']['id'];
				$aco->create();
				$aco->save(array (
					'model' => $controller,
					'foreign_key' => null,
					'parent_id' => $parentId,
					'alias' => $action
				));
			}

		}
		// Give admin full control
		$this->Acl->allow('Admin', 'ROOT', '*');
		foreach ($groups as $group) {
			$this->Acl->allow($group['Group']['name'], 'Users/logout');
		}

		$this->redirect('/users/acl/1');

	}
}
?>