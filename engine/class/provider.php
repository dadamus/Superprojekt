<?php

if (!empty($_GET["a"])) {
    require_once dirname(__FILE__) . '/../../config.php';
    require_once dirname(__FILE__) . '/../protect.php';
    require_once dirname(__FILE__) . '/notification.php';
}

$action = @$_GET["a"];
if ($action == 1) {//P list
    $providers = $db->query("SELECT * FROM `provider`");
    foreach ($providers as $row) {
        echo '<option value="' . $row["id"] . '">' . $row["name"] . '</option>';
    }
} else if ($action == 2) { //P add
    $name = $_GET["name"];
    if ($name == "") {
        die("2");
    }
    $db->query("INSERT INTO `provider` (`name`) VALUES ('$name')");
    die("1");
}