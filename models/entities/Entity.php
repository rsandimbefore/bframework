<?php

/**
 * Created by PhpStorm.
 * User: rjsandim
 * Date: 1/8/16
 * Time: 14:41
 */

namespace BFramework\Models\Entities;

use BFramework\Exceptions\SystemException;

class Entity {

	public function isValido() {
		return !SystemException::temErros();
	}

}