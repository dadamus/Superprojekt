<?php

/**
 * Created by PhpStorm.
 * User: Dawid
 * Date: 02.05.2017
 * Time: 23:04
 */

class globalTools
{
	/**
	 * @param $str
	 * @return string
	 */
	public static function add_quotes($str) {
		return sprintf("'%s'", $str);
	}

	/**
	 * @param string $time
	 * @return int
	 */
	public static function calculate_second(string $time): int {
		$date = date_parse($time);
		return $date["hour"] * 3600 + $date["minute"] * 60 + $date["second"];
	}

}