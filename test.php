<?php


$test = "SET wadaw=dwadaw WHERE SheetCode = 'testowyshetcode'";

$part = explode('WHERE', $test);
$wherePart = str_replace(["SheetCode = '", ' '], ['', ''], end($part));
$output = substr($wherePart, 0, strlen($wherePart) - 1);

echo 'Outp:'.$output . PHP_EOL;