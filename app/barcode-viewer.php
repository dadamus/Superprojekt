<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 14.11.2017
 * Time: 22:50
 */

require_once __DIR__ . '/barcode.php';

$number = $_GET['n'];
$barcode = new Barcode('0' . $number, 12);
$barcode->display();