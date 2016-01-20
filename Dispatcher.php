<?php

/**
 * Created by PhpStorm.
 * User: rjsandim
 * Date: 1/6/16
 * Time: 17:05
 */

namespace BFramework;

use BFramework\Controllers\Controller;
use BFramework\Exceptions\ControllerNotFoundException;
use BFramework\Exceptions\ViewNotFoundException;
use BFramework\Views\View;

class Dispatcher {

	const CONTROLLER_SUFIX = 'Controller';

	/**
	 * @var Controller $controller
	 */
	private $controller;
	private $request;
	private $view;

	public function __construct(Requester $request) {
		$this->request = $request;
		$this->view = new View();
		$this->controller = null;
	}

	public function dispatch() {
		$this->callActionFromController();
		if (!$this->controller->isRedirected()) {
			$this->controller->invokeCallbacksComponent('beforeRender');
			$this->callViewForController();
			$this->controller->invokeCallbacksComponent('shutdown');
		}
	}

	private function callActionFromController() {

		$controllerName = $this->getControllerNameToBeCalled();

		try {
			$this->controller = new $controllerName();

			if (!is_object($this->controller)) {
				throw new ControllerNotFoundException();
			}

			$this->controller->setDispatcher($this);
			$this->controller->setRequest($this->request);
			$this->controller->callAction();

		} catch (ControllerNotFoundException $e) {

		}
	}


	private function callViewForController() {
		$this->view->setFile($this->getPathToViewFile());
		$this->view->setDataForView($this->controller->getViewData());
		$this->view->render($this->controller->getMensagem());

	}

	private function getControllerNameToBeCalled() {
		return $this->request->getControllerName().self::CONTROLLER_SUFIX;
	}

	private function getPathToViewFile() {
		return getcwd().DS.VIEWS_MODULES_PATH.$this->controller->getName().DS.$this->controller->getViewFile();
	}

	public function setRequester(Requester $requester) {
		$this->request =  $requester;
	}
}