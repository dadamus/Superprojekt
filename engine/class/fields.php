<?php
$fields = array("material", "type", "dimension1", "dimension2", "dimension3", "pieces", "pod", "time", "materialp", "project", "factor",
        "pricedetail", "pricen", "materialq", "priceset", "profilea", "profilep", "allotime");
$material = $_POST["material"];
$type = $_POST["type"];
$dimension1 = $_POST["dimension1"];
$dimension2 = $_POST["dimension2"];
$dimension3 = $_POST["dimension3"];
$pieces = $_POST["pieces"];
$pod = $_POST["pod"];
$time = $_POST["time"];
$materialp = $_POST["materialp"];
$project = $_POST["project"];
$factor = $_POST["factor"];
$pricedetail = $_POST["priceDetailN"];
$pricen = $_POST["priceN"];
$materialq = $_POST["materialQ"];
$priceset = $_POST["priceSetN"];
$profilea = $_POST["profileA"];
$profilep = $_POST["profileP"];
$allotime = $_POST["alloTime"];
$pricedetailu = $_POST["priceDetailuN"];

$dimension = $dimension1 . "x" . $dimension2 . "x" . $dimension3;
