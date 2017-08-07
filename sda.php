<a href="abl:C:\\Windows\\">test</a>

<a href="abl-sync:sync:plate">test sync</a>

<?php

$versionsScan = glob("/DATA/1-49/4/PROJEKTY/1/*", GLOB_ONLYDIR);
$versions = [];

foreach ($versionsScan as $v) {
    if (basename($v)[0] == "V") {
        echo "$v/dxf/1sztxbok-trojkaty.dxf";
        if (file_exists("$v/dxf/1sztxbok-trojkaty.dxf" )) {
            $versions[] = dirname($v);
        }
    }
}
var_dump($versions);

?>

<!--
Super polecenie sql

SELECT * FROM LaserMaterial WHERE LaserMatName = SQL#.Util_Hash('MD5', CONVERT(VARBINARY(MAX), 'tekst')

