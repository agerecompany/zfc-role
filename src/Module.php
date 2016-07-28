<?php
namespace Agere\Role;

class Module {

	public function getConfig()
	{
		return include __DIR__ . '/../config/module.config.php';
	}

	public function getViewHelperConfig()
	{
		return array(
			'factories' => array(
				'role' => function($sm) {
					$locator = $sm->getServiceLocator();
					return new \Agere\Role\View\Helper\Role($locator->get('RoleService'));
				},
			),
		);
	}

}