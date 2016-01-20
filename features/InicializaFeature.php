<?php

/**
 * Created by PhpStorm.
 * User: rjsandim
 * Date: 1/12/16
 * Time: 14:31
 */

namespace BFramework\Features;

trait InicializaFeature {

	public function inicializaPeloArray(array $conciliacao) {
		$atributos = array_keys(get_object_vars($this));

		foreach ($conciliacao as $key => $item) {
			if (in_array($key, $atributos, true)) {
				$this->$key = $item;
			}
		}
	}

}