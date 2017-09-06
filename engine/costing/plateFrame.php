<?php

/**
 * Created by PhpStorm.
 * User: Dawid
 * Date: 28.05.2017
 * Time: 12:30
 */

ob_start();
if (!is_null(@$_GET["a"])) {
    require_once dirname(__DIR__) . '/../config.php';
    require_once dirname(__FILE__) . '/../protect.php';
}

$action = @$_GET["a"];

if ($action == "addFrame") {
    require_once dirname(__FILE__) . '/plateSinglePart/plateSinglePart.php';

    $frameId = $_POST["f"];
    $dots = $_POST["dots"];
    $areaValue = $_POST["areaValue"];

    $frameUpdate = $db->prepare("UPDATE plate_costingFrame SET points = :points, `value` = :areaValue WHERE id = :frameId");
    $frameUpdate->bindValue(":points", $dots, PDO::PARAM_STR);
    $frameUpdate->bindValue(":areaValue", $areaValue, PDO::PARAM_STR);
    $frameUpdate->bindValue(":frameId", $frameId, PDO::PARAM_INT);
    $frameUpdate->execute();

    $plateSinglePartCostingIdQuery = $db->prepare("
		SELECT 
		c.id as costingId
		FROM
		plate_costingFrame pcf
		LEFT JOIN plate_CostingImage i ON i.id = pcf.imgId
		LEFT JOIN plate_singlePartCosting c ON c.id = i.plate_costingId
		WHERE 
		pcf.id = :frameId
		"
    );
    $plateSinglePartCostingIdQuery->bindValue(":frameId", $frameId, PDO::PARAM_INT);
    $plateSinglePartCostingIdQuery->execute();

    $plateSingePartData = $plateSinglePartCostingIdQuery->fetch();
    if ($plateSingePartData !== false) {
        $plateSinglePart = new plateSinglePart($plateSingePartData["costingId"], false);

        $plateSinglePart->getInputData();
        $plateSinglePart->setFrameData($areaValue);
        $plateSinglePart->calculate();
        $plateSinglePart->saveCostingData();
    }
    die("ok");
}

$frameId = $_GET["f"];

$frameQuery = $db->query("
	SELECT
	f.imgId,
	f.type,
	f.points,
	pspc.sheet_size_x,
	pspc.sheet_size_y,
	i.*
	FROM
	plate_costingFrame f
	LEFT JOIN plate_CostingImage i ON i.id = f.imgId
	LEFT JOIN plate_singlePartCosting pspc ON pspc.id = i.plate_costingId
	WHERE
	f.id = $frameId
");
$frameData = $frameQuery->fetch();

if (isset($_GET["multi"])) {
    $material = $_GET["material"];
    $materialQuery = $db->query("
        SELECT 
        SheetSize
        FROM 
        plate_multiPartCostingMaterial
        WHERE
        id = $material
    ");
    $materialQueryData= $materialQuery->fetch();
    $sheetSize = explode("x", $materialQueryData["SheetSize"]);
    $frameData["sheet_size_x"] = floatval($sheetSize[0]);
}

$bmpCutter = new bmpCutter($frameData["path"]);
$b64 = $bmpCutter->getBase64();

$imageSize = $bmpCutter->getPosition();

$dots = json_encode([]);
if (strlen($frameData["points"]) > 0) {
    $dots = $frameData["points"];
}

?>

<div class="row" style="user-select: none;">
    <div class="col-lg-12" style="position: relative;">
        <div id="frameCutterMenuContainer">
            <ul id="frameCutterMenu">
                <li class="active" id="createMode">
                    <i class="fa fa-pencil fa-3"></i>
                </li>
                <li id="dragMode">
                    <i class="fa fa-arrows fa-3"></i>
                </li>
                <li id="insertMode">
                    <i class="fa fa-compress fa-3"></i>
                </li>
                <li id="rectangleMode">
                    <i class="fa fa-square-o fa-3"></i>
                </li>
                <li id="save" class="green">
                    <i class="fa fa-save fa-3"></i>
                </li>
            </ul>
            <div id="frameCutterMenuCancel">
                <i class="fa fa-remove"></i>
            </div>
        </div>
        <div style="margin: 0 auto; width: <?= $imageSize["width"] ?>px; height: <?= $imageSize["height"] ?>px; position: relative;"
             id="frameCutter">
            <div style="position: absolute; left: 0px; top: 0px; width: 100%; height: 100%; z-index: 10;" id="dots">

            </div>
            <div style="position: absolute; z-index: 2; left: 0px; top: 0px">
                <canvas id="dotConnections" width="<?= $imageSize["width"] ?>px"
                        height="<?= $imageSize["height"] ?>px;"></canvas>
            </div>
            <div style="position: absolute; left: 0px; top: 0px; z-index: 1;">
                <img src="data:image/png;base64,<?= $b64; ?>"/>
            </div>
        </div>
    </div>
</div>

<div id="context-menu">
    <ul class="dropdown-menu pull-left" role="menu">
        <li id="deleteDot">
            <a href="javascript:;">
                <i class="fa fa-trash"></i> Usuń
            </a>
        </li>
    </ul>
</div>

<div id="wynik" style="position: fixed; top: 250px; left: 40px;">
    Pole: 0
</div>

<script type="text/javascript">
    var frameId = <?=$frameId?>;

    var init_dots_position = JSON.parse('<?=$dots?>');

    var imageSize = {
        width: <?=$imageSize["width"]?>,
        height: <?=$imageSize["height"]?>,
        dpi: <?=($frameData["sheet_size_x"] / $imageSize["width"]) ?>
    }
</script>
<script type="text/javascript" src="/js/plateFrame/jcanvas.min.js"></script>
<script type="text/javascript" src="/js/plateFrame/plateFrame.js"></script>