<?php
namespace Agere\Role\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use	Zend\View\Model\JsonModel;
use	Agere\Translit\Translit;
use Agere\Role\Form\Role as RoleForm;
use Agere\Permission\Controller\PermissionAccessController;

class RoleController extends AbstractActionController {

	public $serviceName = 'RoleService';
	public $controllerRedirect = 'role';
	public $actionRedirect = 'index';


	public function indexAction()
	{
		$sm = $this->getServiceLocator();
		/** @var \Agere\Role\Service\RoleService $service */
		$service = $sm->get($this->serviceName);

		//$this->layout('layout/home');

		return [
			'items'	=> $service->getItemsCollection(),
		];
	}

	public function addAction()
	{
		//$this->layout('layout/home');

		$viewModel = new ViewModel();
		$viewModel->setVariables($this->editAction());
		return $viewModel->setTemplate("Agere/Role/edit.phtml");
	}

	public function editAction() {
		/** @var \Zend\Http\Request $request */
		$request = $this->getRequest();
		$route = $this->getEvent()->getRouteMatch();
		$sm = $this->getServiceLocator();
		$fm = $sm->get('FormElementManager');
		/** @var \Agere\Role\Service\RoleService $service */
		$service = $sm->get($this->serviceName);

		$role = ($role = $service->find($id = (int) $route->getParam('id'))) // це воно? так
			? $role
			: $service->getObjectModel();
		/*$id = (int) $route->getParam('id');
		$item = $service->getOneItem($id);*/ // це ти закоментував? так

		$form = new RoleForm();
		$fields = ['role', 'resource'];
		foreach ($fields as $field) {
			$method = 'get' . ucfirst($field);
			$form->get($field)->setValue($route->getParam($field, $role->$method()));
		}
		/** @var \Agere\Permission\Service\PermissionAccessService $permissionAccessService */
		$permissionAccessService = $sm->get('PermissionAccessService');
		//$permissionAccessController = new PermissionAccessController();

		if ($request->isPost()) {
			$post = $request->getPost()->toArray();
			$form->setData($post);
			if ($form->isValid()) {
				$post = $form->getData();
				$saveData = [];
				foreach ($fields as $field) {
					$saveData[$field] = $post[$field];
				}
				if ($saveData) {
					if (!$role->getId()) {
						$translit = new Translit(); // потрібно скопіювати, а ще краще через композер завантажити вже говтовий модулю
						$saveData['mnemo'] = str_replace(' ', '_', strtolower($translit->filter($saveData['role'])));
						$saveData['remove'] = '1';
					}


					foreach ($saveData as $field => $val) {
						$method = 'set' . ucfirst($field);
						$role->$method($val);
					}

					if (!$role->getId()) {
						$service->getObjectManager()->persist($role);
					}
					$service->getObjectManager()->flush();

					//$saveData['id'] = $id;
					//$item = $service->save($saveData, $role);
					$permissionAccessService->edit($sm, $request, $role->getId());
				}
				$this->redirect()->toRoute('default', [
					'controller' => $this->controllerRedirect,
					'action'     => $this->actionRedirect,
				]);
			}
		}

		// перенесемо меню але тобі потрібно буде потім реалізувати NestedSet алгоритм. Перенось, я поки пошукаю алгоритм.
		
		//$this->layout('layout/home');
		/** @var \Agere\Menu\Service\MenuService $menuService */
		$menuService = $sm->get('MenuService'); // ось це потрібно? В цілому так, але на Хмарі воно хреново реалізовано, тому потрібно буде переробити.
		return [
			'id'    => $role->getId(),
			'form'  => $form,
			'tabs'  => $menuService->getMainMenu(),
			'items' => $permissionAccessService->edit($sm, null, $role->getId()),
		];
	}

	//------------------------------------AJAX----------------------------------------
	/**
	 * Ajax delete
	 */
	public function deleteAction()
	{
		/** @var \Zend\Http\Request $request */
		$request = $this->getRequest();

		if ($request->isPost() && $request->isXmlHttpRequest())
		{
			$locator = $this->getServiceLocator();
			/** @var \Agere\Role\Service\RoleService $service */
			$service = $locator->get($this->serviceName);

			// Access to page for current user
			$responseEvent = $service->delete(__CLASS__, []);
			$message = $responseEvent->first()['message'];
			// END Access to page for current user

			if ($message == '')
			{
				$allow = false;
				$post = $request->getPost();

				$allow = $service->deleteItem($post['id']);

				$result = new JsonModel([
					'message' => ($allow) ? '' : 'Невозможно удалить № '.$post['id'].'. Сначала уберите прив\'язку к позиции!',
				]);
			}
			else
			{
				$result = new JsonModel([
					'message' => $message,
				]);
			}

			return $result;
		}
		else
			$this->getResponse()->setStatusCode(404);
	}

}