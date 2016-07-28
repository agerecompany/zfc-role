<?php
namespace Agere\Role\Service;

use Agere\Core\Service\DomainServiceAbstract;
use Agere\Logs\Event\Logs as LogsEvent;
use Agere\Role\Model\Role;


class RoleService extends DomainServiceAbstract
{
	protected $entity = Role::class;
	protected $repository = Role::class;

	protected $_repositoryName = 'Role';
	protected $_resources = [
		'custom'	=> 'Custom',
		'all'		=> 'All',
	];

    protected $userService;


    public function __construct($userService)
    {
        $this->userService = $userService;
    }

	/**
	 * @return array
	 */
	public function getResources()
	{
		return $this->_resources;
	}

	/**
	 * @return mixed
	 */
	public function getItemsCollection()
	{
		$repository = $this->getRepository(/*$this->entity*/);

		return $repository->findAll();
	}

	/**
	 * @param int $id
	 * @param string $field
	 * @return mixed
	 */
	public function getOneItem($id, $field = 'id')
	{
		$repository = $this->getRepository(/*$this->_repositoryName*/);

		return $repository->findById($id);
	}

	/**
	 * @param array $data
	 * @param object $oneItem
	 * @return mixed
	 */
	public function save($data, $oneItem)
	{
		$isNew = false;

		if (is_null($oneItem->getId()))
		{
			$isNew = true;
		}

		unset($data['id']);

		foreach ($data as $field => $val)
		{
			$method = 'set'.ucfirst($field);
			$oneItem->$method($val);
		}

		$repository = $this->getRepository(/*$this->_repositoryName*/);
		$repository->save($oneItem);

		if ($isNew)
		{
			// Update Module Permission
			$this->updatePermission(__CLASS__);
		}

		return $oneItem;
	}

	/**
	 * @param $id
	 * @return bool
	 */
	public function deleteItem($id)
	{
		$oneItem = $this->getOneItem($id);

		if ($oneItem && $oneItem->getRemove())
		{
			/** @var \Agere\Users\Service\UsersService $serviceUsers */
			$serviceUsers = $this->getService('users');
			$userItem = $serviceUsers->getItemsCollection(['roleId' => $id], 1, true, 1);

			/** @var \Agere\Mail\Service\MailOptionRoleService $mailOptionRolesService */
			$mailOptionRolesService = $this->getService('mailOptionRole');
			/** @var \Agere\Mail\Model\MailOptionRole $itemOptionRoles */
			$itemOptionRoles = $mailOptionRolesService->getOneItem($id, 'roleId');

			if (! $userItem && ! $itemOptionRoles->getId())
			{
				$repository = $this->getRepository($this->_repositoryName);
				$repository->delete($oneItem);

				$servicePermissionAccess = $this->getService('permissionAccess');
				$servicePermissionAccess->deleteByRoleId($id);

				return true;
			}
		}

		return false;
	}


	//------------------------------------------Events------------------------------------------
	/**
	 * Module Users
	 *
	 * @param $class
	 * @param $params
	 * @return mixed
	 */
	public function delete($class, $params)
	{
		$event = new LogsEvent();
		return $event->events($class)->trigger('Role.delete', $this, $params);
	}

	/**
	 * Module Permission
	 *
	 * @param $class
	 * @param $params
	 */
	public function updatePermission($class, $params = [])
	{
		$event = new LogsEvent();
		$event->events($class)->trigger('role.updatePermission', $this, $params);
	}

}