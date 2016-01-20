<?php


/**
 * Created by PhpStorm.
 * User: rjsandim
 * Date: 12/15/15
 * Time: 14:08
 */

namespace BFramework\Exceptions;

use Exception;
use BFramework\Models\Validador;

class SystemException extends Exception {
	private static $avisos;
	private static $errosGlobais;

	public function addErro($mensagemDeErro, $campoId) {
		self::$avisos[$campoId][] = $mensagemDeErro;
	}

	//Todo Sugestao para nome, erroGlobal ficou estranho.
	public function addErroGlobal($mensagemDeErro) {
		self::$errosGlobais[] = $mensagemDeErro;
	}

	public function validaFormulario($dados = null, $modulo = null) {
		if (count(self::$avisos)) {
			$valida = new Validador();
			$valida->validaCampos($dados, 'conciliacao_cartoes', self::$avisos);
		}
	}

	public static function temErrosGlobais() {
		return count(self::$errosGlobais);
	}

	public static function temErros() {
		return count(self::$avisos) || count(self::$errosGlobais) ? true : false;
	}

	public static function obtemMensagensGlobais() {
		return implode('</br>', self::$errosGlobais);
	}
}