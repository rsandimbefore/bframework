<?php
/**
 * Created by PhpStorm.
 * User: rjsandim
 * Date: 1/12/16
 * Time: 10:25
 */

spl_autoload_register(function ($class) {

	$possibilidades = array(
		$class . '.php',
		'models/contracts/' . $class . '.php',
		'models/entities/' . $class . '.php',
		'models/entities/' . $class . '.php',
		'models/tables/' . $class . '.php',
		'models/tables/' . $class . '.php',
		'controllers/' . $class . '.php',
		'controllers/components/' . $class . '.php',
		'views/' .$class. 'php',
		'views/elements/' . $class . '.php',
		'views/helpers/' . $class . '.php',
		'exceptions/' . $class . '.php',
		'features/' . $class . '.php'
	);

	foreach($possibilidades as $file) {

		$file = __DIR__."/".$file;
		$file = str_replace(array("/","\\"), DIRECTORY_SEPARATOR, $file);
		$file = str_replace(array("BFramework\\","BFramework/"), '', $file);

		if(file_exists($file)) {
			include $file;
			return true;
		}
	}

});

?>