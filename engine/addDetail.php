<?php

require_once dirname(__FILE__) . '/../config.php';

$path = @$_GET["path"];

if ($path == null) {
    die("Błąd");
}
$date = date("Y-m-d H:i:s");

$epath = explode("/", str_replace('\\', "/", $path));

$dirProjects = array_search("PROJEKTY", $epath);

$cid = $epath[$dirProjects-1];
$pn = $epath[$dirProjects+1];

$name = basename(str_replace("\\", "/", $path));

$newDetailQuery = $db->prepare("
	SELECT 
	p.id as pid,
	d.id as did,
	m.type
	FROM `projects` p
	LEFT JOIN `details` d ON d.src = :detailName AND d.pid = p.id
	LEFT JOIN `mpw` m ON m.did = d.id
	WHERE 
	p.nr = :pNumber
	AND p.cid = :cNumber
");
$newDetailQuery->bindValue(":detailName", $name, PDO::PARAM_STR);
$newDetailQuery->bindValue(":pNumber", $pn, PDO::PARAM_INT);
$newDetailQuery->bindValue(":cNumber", $cid, PDO::PARAM_INT);
$newDetailQuery->execute();

$pid = 0;

while($detail = $newDetailQuery->fetch()) {
	$pid = $detail["pid"];
	$did = $detail["did"];
	$type = $detail["type"];

	if ($did > 0 && $type < 7) {
		die("Detal znajduje się juz w bazie!");
	}
}

if ($pid == 0) {
	die("Brak projektu $pn!");
}

$sqlBuilder = new sqlBuilder("INSERT", "details");
$sqlBuilder->bindValue("pid", $pid, PDO::PARAM_INT);
$sqlBuilder->bindValue("src", $name, PDO::PARAM_STR);
$sqlBuilder->bindValue("date", $date, PDO::PARAM_STR);
$sqlBuilder->flush();

$did = $db->lastInsertId();
insertStatus($did, $STATUS_NEW);
die("1");
?>