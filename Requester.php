<?php

/**
 * Created by PhpStorm.
 * User: rjsandim
 * Date: 1/8/16
 * Time: 18:15
 */

namespace BFramework;

class Requester {

	private $controllerName;
	private $action;
	private $params;
	private $type;
	private $postData;
	private $queryData;

	public function __construct() {
		$this->type = strtoupper(filter_input(INPUT_SERVER, 'REQUEST_METHOD'));
		$this->postData = filter_input_array(INPUT_POST);
		$this->queryData = filter_input_array(INPUT_GET);
	}

	public function setRoute($route) {
		$this->controllerName = $route['controller'];
		$this->action = $route['action'];
		$this->params = $route['params'];
	}

	public function isPost() {
		return $this->type === "POST";
	}

	public function isGet() {
		return $this->type === "GET";
	}

	public function getControllerName() {
		return $this->controllerName;
	}

	public function getAction() {
		return $this->action;
	}

	public function getParams() {
		return $this->params;
	}

	public function getType() {
		return $this->type;
	}

	public function getData($key = "") {

		if ($key === "") {
			return $this->postData;
		}

		return $this->postData[$key];
	}

	public function getQuery($key = "") {

		if ($key === "") {
			return $this->queryData;
		}

		return $this->queryData[$key];
	}


}