<?php

/**
 * Created by PhpStorm.
 * User: rjsandim
 * Date: 1/8/16
 * Time: 14:46
 */

namespace BFramework\Views;

use BFramework\Exceptions\SystemException;
use BFramework\Exceptions\ViewNotFoundException;
use Debugger;

class View {

	private $viewFileName;
	private $viewData;

	public function showErrors() {
		if (SystemException::temErrosGlobais()) {
			$icone = '<img  src="images/bt_alerta.png" class="png">';
			$this->mostrarMensagem(SystemException::obtemMensagensGlobais(), $icone);
			echo "document.getElementById('janela').style.display='none';";
			echo "document.getElementById('lente').style.display='none';";
		}
	}

	public function showSuccess($mensagem) {
		if ($mensagem) {
			$icone = '<img  src="images/bt_ok.png" class="png">';
			echo "<script>";
			$this->mostrarMensagem($mensagem, $icone);
			echo "if(document.getElementById('btnSalvar')!=null){//desativa o botão salvar para evitar salvar várias vezes
			document.getElementById('btnSalvar').style.display='none';}";
			echo "</script>";
		}
	}

	private function mostrarMensagem($avisos, $icone) {

		if ($avisos != '') {

			echo "document.getElementById('load').style.display='none';";
			echo "document.body.scrollTop='0';";
			echo "mostrarAviso('aviso',5000,unescape('" . rawurlencode($avisos) . "'),'" . $icone . "');";

		}
	}

	public function render($mensagem) {
		$this->retrieveMessagesToView($mensagem);
		try {
			extract($this->viewData);
			Debugger::dump($this->viewData);

			if (!include($this->viewFileName)) {
				throw new ViewNotFoundException();
			}

		} catch (ViewNotFoundException $e) {

		}
	}

	public function setFile($filename) {
		$this->viewFileName = $filename;
	}

	public function setDataForView($data) {
		$this->viewData = $data;
	}

	public function retrieveMessagesToView($mensagem) {
		$this->showSuccess($mensagem);
		$this->showErrors();
	}
}