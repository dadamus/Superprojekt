<?php

$xml = simplexml_load_file($data_src . "temp/PrintData.xml");

$detailname = $xml->Parts->Part->PartsNo;
$lasermaterialname = $xml->Items->LaserMaterialName;
$programname = $xml->Header->ProgramName;

$xmaterial = $xml->Items->MaterialType;
for ($i = 1; $i <= count($Material->name); $i++) {
    if ($Material->name[$i] == $xmaterial) {
        $material = $i;
    }
}

$thickness = $xml->Items->Thickness;
$diameter = $xml->Items->MaterialSize->Diameter;
$width = $xml->Items->MaterialSize->Width;
$height = $xml->Items->MaterialSize->Height;
$cornerr = $xml->Items->MaterialSize->CornerR;

$timestudy = $xml->Items->TimeStudy;

$tubetype = $xml->Items->TubeType;
$types = array("CornerRPipe", "RoundPipe", "Channel", "EqualAngle");
for ($i = 0; $i < count($types); $i++) {
    if ($tubetype == $types[$i]) {
        $type = $i;
    }
}

$date = date("Y-m-d H:i:s");

$protect = $db->prepare("SELECT COUNT(*) FROM `sptc` WHERE `lasermaterialname` = '$lasermaterialname' AND `programname` = '$programname' AND `material` = '$material' AND `thickness` = '$thickness' AND `diameter` = '$diameter' AND `width` = '$width' "
        . "AND `height` = '$height' AND `cornerr` = '$cornerr' AND `timestudy` = '$timestudy' AND `type` = '$type' AND `detailname` = '$detailname'");
$protect->execute();
if ($protect->fetchColumn() > 0) {
    echo "2";
} else {
    $query = $db->prepare("INSERT INTO `sptc` (`lasermaterialname`, `programname`, `material`, `thickness`, `diameter`, `width`, `height`, `cornerr`, `timestudy`, `type`, `detailname`, `date`) "
            . "VALUES ('$lasermaterialname', '$programname', '$material', '$thickness', '$diameter', '$width', '$height', '$cornerr', '$timestudy', '$type', '$detailname', '$date')");
    $query->execute();
    echo "1";
}
unlink($data_src . "temp/PrintData.xml");
