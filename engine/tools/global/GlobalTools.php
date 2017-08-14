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
    public static function add_quotes(string $str): string
    {
        return sprintf("'%s'", $str);
    }

    /**
     * @param string $time
     * @return int
     */
    public static function calculate_second(string $time): int
    {
        $date = date_parse($time);
        return $date["hour"] * 3600 + $date["minute"] * 60 + $date["second"];
    }

    /**
     * @param int $time
     * @return string
     */
    public static function seconds_to_time(int $time): string
    {
	    $hours = floor($time / 3600);
	    $time -= $hours * 3600;

	    $minutes = floor($time / 60);
	    $time -= $minutes * 60;

	    $return =
            ($hours < 10 ? '0' . $hours : $hours) . ':' .
            ($minutes < 10 ? '0' . $minutes : $minutes) . ':' .
            ($time < 10 ? '0' . $time : $time);
        return $return;
    }

}