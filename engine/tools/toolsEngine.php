<?php

require_once dirname(__FILE__) . "/toolsEngine/TollsEngineController.php";

$toolsEngineController = new TollsEngineController();

$toolsEngineController->addTool("sql");
$toolsEngineController->addTool("ean13");
$toolsEngineController->addTool("csharp");
$toolsEngineController->addTool("global");

$toolsEngineController->initialize();
