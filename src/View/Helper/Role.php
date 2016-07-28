<?php
namespace Agere\Role\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Agere\Role\Service\RoleService;

class Role extends AbstractHelper
{
	/**
	 * @var \Agere\Role\Service\RoleService
	 */
	protected $roleService;


	/**
	 * @param RoleService $roleService
	 */
	public function __construct(RoleService $roleService) {
		$this->roleService = $roleService;
	}


	/**
	 * @param int|array $valSelected
	 * @param string $title
	 * @return string
	 */
	public function rolesList($valSelected, $title = '')
	{
		$strOptions = '<option value="">'.$title.'</option>';

		$collections = $this->roleService->getItemsCollection();

		foreach ($collections as $collection)
		{
			$selected = ((! is_array($valSelected) && $collection->getId() == $valSelected) OR (is_array($valSelected) && in_array($collection->getId(), $valSelected))) ? ' selected=""' : '';
			$strOptions .= '<option value="'.$collection->getId().'"'.$selected.'>'.$collection->getRole().'</option>';
		}

		return $strOptions;
	}

	/**
	 * @param int $valSelected
	 * @return string
	 */
	public function resourceList($valSelected)
	{
		$strOptions = '';

		$items = $this->roleService->getResources();

		foreach ($items as $key => $val)
		{
			$selected = ($key == $valSelected) ? ' selected=""' : '';
			$strOptions .= '<option value="'.$key.'"'.$selected.'>'.$val.'</option>';
		}

		return $strOptions;
	}
	
}