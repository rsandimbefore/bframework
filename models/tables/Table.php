<?php

/**
 * Created by PhpStorm.
 * User: rjsandim
 * Date: 1/8/16
 * Time: 14:25
 */

namespace BFramework\Models\Tables;

use BPDO;
use Consulta;
use Insere;
use Edita;
use Exclui;

class Table {

	protected $Consulta;
	protected $Insere;
	protected $Edita;
	protected $Exclui;
	protected $nomeTabela;
	protected $campos;
	private $filtros;

	public function __construct() {
		$this->Consulta = new Consulta(BPDO::obtemInstancia());
		$this->Insere = new Insere(BPDO::obtemInstancia());
		$this->Edita = new Edita(BPDO::obtemInstancia());
		$this->Exclui = new Exclui(BPDO::obtemInstancia());
	}

	public function getFiltros() {
		return $this->filtros;
	}

	public function setFiltros($filtros) {
		$this->filtros = $filtros;
	}

}