<?php
namespace Agere\Role;

return array(
	'controllers' => array(
		'invokables' => array(
			'role' => Controller\RoleController::class
		),
	),

	'view_manager' => array(
        'template_map' => array(
            'role/partial/settings-content'    => __DIR__ . '/../view/agere/role/partial/settings/content.phtml',
        ),
		'template_path_stack' => array(
			__DIR__ . '/../view',
		),
	),

	'service_manager' => array(
		'aliases' => array(
			'RoleService'	=> Service\RoleService::class,
		),

		'factories' => array(
			Service\RoleService::class => function ($sm) {
				$em = $sm->get('Doctrine\ORM\EntityManager');
				//$service = \Agere\Agere\Service\Factory\Helper::create('Role/Role', $em);

				// Users
				//$service::addService('users', $sm->get('UsersService'));

				//Agere\Agere\Service\Factory\Helper::create('permission/permissionAccess', $em);
				//\Agere\Agere\Service\Factory\Helper::create('mail/mailOptionRole', $em);
                $service = new Service\RoleService($sm->get('UserService'));

				return $service;
			},

		),
	),

	// Doctrine config
	'doctrine' => array(
		'driver' => array(
			'orm_default' => array(
				'drivers' => array(
					__NAMESPACE__ . '\Model' => __NAMESPACE__ . '_driver',
				)
			),

			__NAMESPACE__ . '_driver' => array(
				'class' => 'Doctrine\ORM\Mapping\Driver\YamlDriver',
				'cache' => 'array',
				'extension' => '.dcm.yml',
				'paths' => array(__DIR__ . '/yaml')
			),

		),
	),

	// @link http://adam.lundrigan.ca/2012/07/quick-and-dirty-zf2-zend-navigation/
	// All navigation-related configuration is collected in the 'navigation' key
	'navigation' => array(
		// The DefaultNavigationFactory we configured in (1) uses 'default' as the sitemap key
		'default' => array(
			// And finally, here is where we define our page hierarchy
			'Role' => array(
				'module' => 'Role',
				'label' => 'Главная',
				'route' => 'default',
				'controller' => 'index',
				'action' => 'index',
				'pages' => array(
					'settings-index' => array(
						'label'      => 'Настройки',
						'route'      => 'default',
						'controller' => 'settings',
						'action'     => 'index',
						'pages' => array(
							'Role-index' => array(
								'label' => 'Роли',
								'route' => 'default',
								'controller' => 'Role',
								'action' => 'index',
								'pages' => array(
									'Role-add' => array(
										'label' => 'Добавить',
										'route' => 'default',
										'controller' => 'Role',
										'action' => 'add',
									),
									'Role-edit' => array(
										'label' => 'Редактировать',
										'route' => 'default/id',
										'controller' => 'Role',
										'action' => 'edit',
									),
								),
							),
						),
					),
				),
			),
		),
	),

);