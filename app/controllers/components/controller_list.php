<?php

class ControllerListComponent extends Object
{
	function get()
	{
		$controllerSet = array ();
		$baseControllers = array();
		$pluginControllers = array();
		
		$baseControllers['Base'] = Configure::listObjects( 'controller' );
		$plugins = Configure::listObjects( 'plugin' );

		foreach ($plugins as $plugin)
		{
			$path = APP . 'plugins/' . strtolower($plugin) . '/controllers/';
			$pluginControllers[$plugin] = Configure::listObjects( 'controller', $path, false);
		}

		$classes = am($pluginControllers, $baseControllers);

		foreach ( $classes as $set => $controllers )
		{

			switch($set)
			{
				case 'Base':
					foreach ($controllers as $fileName)
					{
						if ($fileName != "App")
						{
							App::import('Controller', $fileName);
							$className = $fileName . 'Controller';
							$actions = get_class_methods( $className );
							$parentActions = get_class_methods( 'AppController' );
							$controllerSet[$fileName] = array_diff( $actions, $parentActions );
						}
					}
				break;
				
				default:
					foreach ($controllers as $controller => $fileName)
					{
						if ($fileName != "App")
						{
							$pluginPath = APP . "plugins/" . strtolower($set) . "/";
							$controllerPath = $pluginPath . "/controllers/";
							$className = $fileName . 'Controller';
							require_once($pluginPath . strtolower($set) . '_app_controller.php');
							require_once($controllerPath . strtolower($fileName) . "_controller.php");
							$actions = get_class_methods( $className );
							$parentActions = get_class_methods( $set . 'AppController' );
							$controllerSet[$fileName] = array_diff( $actions, $parentActions );
						}
					}
			}
		}
		return $controllerSet;
	}
}
?>