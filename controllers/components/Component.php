<?php
/**
 * Created by PhpStorm.
 * User: rjsandim
 * Date: 1/12/16
 * Time: 08:49
 */

namespace BFramework\Controllers\Components;

use BFramework\Controllers\Controller;

class Component {

	public $components = array();

	//deverá ser invocado antes de before action
	public function initialize(Controller $controller) {

	}
	//devevá ser invocado após o before action, mas antes do controller executar a action
	public function startup(Controller $controller) {

	}

	// deverá ser ser invocado após o controller executar a action, mas antes do controller renderizar a camada de view;
	public function beforeRender(Controller $controller) {

	}

	//chamado depois que a saida foi enviada para o navegador
	public function shutdown(Controller $controller) {

	}

	//é invocado quando o metodo de redirect do controller é invocado. Se esse metodo refornar false o controller não continuará o redirect;
	public function beforeRedirect(Controller $controller, $url, $status = null, $exit = true) {

	}
}