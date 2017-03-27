<?php
session_start();
require_once '../config.php';

$p_login = $_POST["login"];
$p_pass = md5($_POST["pass"]);

$account = $db->prepare("SELECT `id` FROM `accounts` WHERE `login` = '$p_login' AND `pass` = '$p_pass'"); 
$account->execute();
if ($user = $account->fetch()) {
    $_SESSION["login"] = $user["id"];
    $_SESSION["nick"] = $p_login;
    print '<script>window.location.href = "'.$site_path.'/index.php";</script>';
} else {
    print "$p_login + $p_pass";
}