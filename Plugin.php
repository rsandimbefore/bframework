<?php

/**
 * Created by PhpStorm.
 * User: rjsandim
 * Date: 1/18/16
 * Time: 17:41
 */

namespace BFramework;


class Plugin {

	private static $plugins;

	public static function import(array $pluginList) {
		self::$plugins = $pluginList;
	}

	public function has () {

	}


}