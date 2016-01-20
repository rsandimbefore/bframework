<?php

/**
 * Created by PhpStorm.
 * User: rjsandim
 * Date: 12/28/15
 * Time: 10:27
 */
namespace BFramework\Controllers;

use BFramework\Dispatcher;
use BFramework\Requester;
use BFramework\Exceptions\SystemException;

class Controller {

	public $uses = array();
	public $helpers = array();
	public $components = array();
	/**
	 * @var Requester $request
	 */
	private $request;

	/**
	 * @var Dispatcher $dispatcher
	 */
	private $dispatcher;
	private $viewData;
	private $viewFile;
	private $redirected = false;
	private static $mensagem;

	public function __construct() {

		$this->loadComponents();

		$this->initialize();
	}

	public function setDispatcher(Dispatcher $dispatcher) {
		$this->dispatcher = $dispatcher;
	}

	public function setRequest(Requester $request) {
		$this->request = $request;
	}

	public function callAction() {
		try {
			$actionName = $this->request->getAction();
			$this->render($actionName);
			$this->invokeCallbacksComponent('initialize');
			$this->beforeAction();
			$this->invokeCallbacksComponent('startup');
			$this->$actionName();
			$this->afterAction();

		} catch (SystemException $e) {
			return $this->dispararErros($e);
		}
		return true;
	}

	public function set($key, $value = null) {
		if (is_array($key)) {
			$this->viewData = $key;
		} else {
			$this->viewData[$key] = $value;
		}
	}

	public function getViewData() {
		return $this->viewData;
	}

	public function beforeAction() {

	}

	public function afterAction() {

	}

	private function dispararErros(SystemException $e) {
		$e->validaFormulario();
		return false;
	}

	public function getName() {
		return str_replace('Controller', '', get_class($this));
	}

	public function render($viewName) {
		$this->viewFile = $viewName.".php";
	}

	public function getViewFile() {
		return $this->viewFile;
	}

	public function getMensagem() {
		return self::$mensagem;
	}

	private function redirect($route) {
		$this->redirected = true;
		$controller = !empty($route['controller']) ? $route['controller'] : $this->getName();
		$action = $route['action'];

		$this->request->setRoute(compact("controller", "action"));

		$dispatcherRedirect = new Dispatcher($this->request);
		$dispatcherRedirect->setRequester($this->request);
		$dispatcherRedirect->dispatch();
	}

	public function redirecionar($route, $mensagem = '') {
		self::$mensagem = $mensagem;
		$this->redirect($route);
		return true;
	}

	public function getRequest() {
		return $this->request;
	}

	public function isRedirected() {
		return $this->redirected;
	}

	public function initialize() {

	}

	public function loadComponents() {
		foreach ($this->components as $component) {

			if (is_array($component)) {
				$className = $component['className'];
			} else {
				$className = $component;
			}

			$this->{$component} =  new $className();
		}
	}

	public function invokeCallbacksComponent($callbackName) {
		foreach ($this->components as $component) {

			if (is_array($component)) {
				$className = $component['className'];
			} else {
				$className = $component;
			}

			$this->{$className}->$callbackName($this);
		}
	}

}

