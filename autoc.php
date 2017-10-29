<?php

require_once dirname(__FILE__) . '/config.php';
require_once dirname(__FILE__) . '/engine/class/material.php';
require_once dirname(__FILE__) . '/aengine/AutoEngineController.php';

$Material = new Material();

if (!file_exists($data_src . "temp")) {
    mkdir($data_src . "temp", 0777, true);
}
$scan = scandir($data_src . "temp");
$files = array();

foreach ($scan as $file) {
    if (!is_dir($file)) {
        array_push($files, $file);
    }
}

$gC_mb = @$_GET["c_mb"];
$p_a = @$_GET["p_a"];

if (isset($_POST["p_a"]))  {
	$p_a = $_POST["p_a"];
}

//------AUTO ENGINE CONTROLLER INIT
$autoEngineController = new AutoEngineController();