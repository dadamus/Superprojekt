<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 02.07.2018
 * Time: 22:14
 */

require_once __DIR__ . '/../../../config.php';

$dirId = $_GET['dir_id'];

$dirDataQuery = $db->prepare("
            SELECT * 
            FROM 
            plate_multiPartDirectories
            WHERE
            id = :dirId
        ");
$dirDataQuery->bindValue(":dirId", $dirId, PDO::PARAM_INT);
$dirDataQuery->execute();
$dirData = $dirDataQuery->fetch();

$detailsQuery = $db->prepare("
            SELECT 
            m.*,
            d.pid,
            m.id as mpw_id,
            mat.id as material_id,
            mat.name as material_name,
            details.name as detail_name,
            details.did as detail_id,
            details.src as detail_src_name,
            ccn.matType as laser_material_type,
            ccn.matName as laser_material_name
            FROM 
            plate_multiPartDetails details
            LEFT JOIN mpw m ON m.id = details.mpw
            LEFT JOIN details d ON d.id = details.did
            LEFT JOIN material mat ON mat.id = m.material
            LEFT JOIN cutting_conditions_names ccn ON ccn.id = m.cuttin_conditions_name_id
            WHERE
            details.dirId = :dirId
            ORDER BY details.mpw ASC
        ");
$detailsQuery->bindValue(":dirId", $dirId, PDO::PARAM_INT);
$detailsQuery->execute();

$detailsData = $detailsQuery->fetchAll(PDO::FETCH_ASSOC);

$firstData = reset($detailsData);
$multipartFolder = $firstData['src'];

$csvSrc = $multipartFolder . '/data.csv';

if (!file_exists($multipartFolder)) {
    mkdir($multipartFolder, 0777, true);
}

if (file_exists($csvSrc)) {
    unlink($csvSrc);
}

$file = fopen($csvSrc, "wb");

foreach ($detailsData as $detail) {
    $detailWebSrc = $detail['src'] . '/' . $detail['detail_src_name'];
    $detailWebSrc = str_replace('/var/www/html/ABL/', 'X:\\', $detailWebSrc);
    $detailWebSrc = str_replace('/', '\\', $detailWebSrc);

    fputcsv($file, [
        '"'.$detailWebSrc.'"',
        '"'.$detail['material'].'"',
        '"'.$detail['material_name'].'"',
        $detail['thickness'],
        '"'.$detail['t_material_name'].'"',
        '"'.$detail['laser_material_name'].'"',
        '"File"',
        10
    ], ',', '"', "\n");
}

fclose($file);

$fileContext = file_get_contents($csvSrc);
$fileContext = str_replace(["\n", '""'], ["\r\n", ''], $fileContext);
unlink($csvSrc);
file_put_contents($csvSrc, $fileContext);

echo 'ok';