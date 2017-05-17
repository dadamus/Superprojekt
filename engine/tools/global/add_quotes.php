<?php

/**
 * Created by PhpStorm.
 * User: Dawid
 * Date: 02.05.2017
 * Time: 23:04
 */

class globalTools
{
	public static function add_quotes($str) {
		return sprintf("'%s'", $str);
	}
}