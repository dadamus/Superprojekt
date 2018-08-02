<?php
/** @var PlateMultiPart $multiPart */
$multiPart = $data["multiPart"];
$breakLoop = false;
?>

<?php foreach ($multiPart->getPrograms() as $program): ?>
    <div class="row programFrame" data-program-id="<?= $program->getId() ?>">
        <div class="col-lg-12">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <?= $program->getSheetName() ?>
                    </div>
                </div>
                <div class="portlet-body">
                    <?php
                    $materialData = $program->getMaterial();
                    $frameData = $program->getFrame();
                    $imageData = $frameData->getImg();

                    $bmpCutter = new bmpCutter($imageData->getPath());
                    $b64 = $bmpCutter->getBase64();
                    $imageSize = $bmpCutter->getPosition();

                    $dots = json_encode([]);
                    if (strlen($frameData->getPoints()) > 0) {
                        continue;
                    }
                    $breakLoop = true;
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
                                <div style="position: absolute; left: 0px; top: 0px; width: 100%; height: 100%; z-index: 10;"
                                     id="dots">

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
                        Pole: 0<br/>
                    </div>

                    <div>
                        SheetSizeX: <?= $materialData->getSheetSizeX() ?><br/>
                        ImageWidth: <?=$imageSize["width"] ?><br/>
                        Scale: <?= $bmpCutter->getScale() ?><br/>
                    </div>

                    <script type="text/javascript">
                        var frameId = <?=$frameData->getId()?>;

                        var init_dots_position = JSON.parse('<?=$dots?>');

                        var imageSize = {
                            width: <?=$imageSize["width"]?>,
                            height: <?=$imageSize["height"]?>,
                            dpi: <?= ($materialData->getSheetSizeX() / $imageSize["width"] * $bmpCutter->getScale()) ?>
                        };

                        var dest = "/plateMulti/<?= $multiPart->getDirId() ?>/<?= $program->getId() ?>/";
                        var back = function () {
                            location.reload();
                        };
                    </script>
                </div>
            </div>
        </div>
    </div>
    <?php
    //
    if ($breakLoop) {
        break;
    }
    ?>
<?php endforeach; ?>
