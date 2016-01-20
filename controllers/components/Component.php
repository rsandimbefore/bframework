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

	//dever� ser invocado antes de before action
	public function initialize(Controller $controller) {

	}
	//devev� ser invocado ap�s o before action, mas antes do controller executar a action
	public function startup(Controller $controller) {

	}

	// dever� ser ser invocado ap�s o controller executar a action, mas antes do controller renderizar a camada de view;
	public function beforeRender(Controller $controller) {

	}

	//chamado depois que a saida foi enviada para o navegador
	public function shutdown(Controller $controller) {

	}

	//� invocado quando o metodo de redirect do controller � invocado. Se esse metodo refornar false o controller n�o continuar� o redirect;
	public function beforeRedirect(Controller $controller, $url, $status = null, $exit = true) {

	}
}