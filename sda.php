<a href="abl:C:\\Windows\\">test</a>

<a href="abl-sync:sync:plate">test sync</a>

<?php

$a = "1";
$b = 1;

if ($a === $b) {
    echo "No ok";
} else {
    echo $a . " - " . $b . "Nie!!";
}

?>

<!--
Super polecenie sql

SELECT * FROM LaserMaterial WHERE LaserMatName = SQL#.Util_Hash('MD5', CONVERT(VARBINARY(MAX), 'tekst')

