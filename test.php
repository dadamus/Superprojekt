<?php
include 'config.php';

$sec = _secToTime("1805", ":");
echo $sec . " | ";
echo _timeToSec("00:30:05");
