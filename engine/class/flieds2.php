<?php

$fields = array("material", "thick", "dimension11", "dimension12", "dimension2", "handicap", "qta", "qtp", "sheets", "time", "pierces", "factor", "mp", "cn", "dcn", "ccn");
$material = $_POST["material"];
$thick = $_POST["thick"];
$dimension11 = $_POST["dimension11"];
$dimension12 = $_POST["dimension12"];
$dimension2 = $_POST["dimension2"];
$handicap = $_POST["handicap"];
$qta = $_POST["qta"];
$qtp = $_POST["qtp"];
$sheets = $_POST["sheets"];
$time = $_POST["time"];
$pierces = $_POST["pierces"];
$factor = $_POST["factor"];
$mp = $_POST["mp"];
$cn = $_POST["cn"];
$dcn = $_POST["dcn"];
$ccn = $_POST["ccn"];

$dimension = $dimension11 . "x" . $dimension12;
