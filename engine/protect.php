<?php
session_start();
if (@$_SESSION["login"] == null) {
    header("Location: ".$_SERVER['SERVER_NAME']);
}