<?php
/** @var mainCardModel $main */
$main = $data["main"];
?>
<div class="row">
    <div class="col-lg-6">
        <h2 class="page-title">Costing - multipart </h2>
    </div>
    <div class="col-lg-6">
        <form class="form-horizontal" id="changeDesignerForm" action="?" data-dir-id="<?= $data["directoryId"] ?>">
            <div class="form-group">
                <label class="col-md-2 control-label">Projektant: </label>
                <div class="col-md-6">
                    <div class="input-group">
                        <select class="form-control" id="designerId"
                                <?php if ($main->isBlocked()): ?>disabled="disabled"<?php endif; ?>>
                            <option value="">Brak</option>
                            <?php foreach ($data["users"] as $user): ?>
                                <option
                                        value="<?= $user["id"] ?>"
                                    <?php if ($user["id"] == $data["designerId"]): ?>
                                        selected="selected"
                                    <?php endif; ?>
                                >
                                    <?= $user["name"] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="input-group-btn">
                            <?php if (!$main->isBlocked()): ?>
                                <button class="btn blue" type="submit">Zmień</button>
                            <?php endif; ?>
                    </span>
                    </div>
                </div>
                <div class="col-md-4">
                    <?php if (!$main->isBlocked()): ?>
                    <div class="btn-group btn-group-solid">
                        <a
                                class="btn red popovers"
                                href="javascript:;"
                                id="costingCancel"
                                data-dir-id="<?= $data["directoryId"] ?>"
                                data-container="body"
                                data-trigger="hover"
                                data-placement="bottom"
                                data-content="Anuluj"
                                data-original-title=""
                        >
                            <i class="fa fa-ban"></i>
                        </a>
                        <a
                                class="btn dark popovers"
                                href="javascript:;"
                                id="costingBlock"
                                data-dir-id="<?= $data["directoryId"] ?>"
                                data-container="body"
                                data-trigger="hover"
                                data-placement="bottom"
                                data-content="Zablokuj"
                                data-original-title=""
                        >
                            <i class="fa fa-lock"></i>
                        </a>
                        <a
                                class="btn green popovers"
                                id="costingAccept"
                                href="javascript:;"
                                data-dir-id="<?= $data["directoryId"] ?>"
                                data-container="body"
                                data-trigger="hover"
                                data-placement="bottom"
                                data-content="Akceptuj"
                                data-original-title=""
                        >
                            <i class="fa fa-check"></i>
                        </a>
                        <?php endif; ?>
                        <a
                                class="btn dark popovers"
                                id="duplicate"
                                href="javascript:;"
                                data-dir-id="<?= $data["directoryId"] ?>"
                                data-container="body"
                                data-trigger="hover"
                                data-placement="bottom"
                                data-content="Duplikuj"
                                data-original-title=""
                        >
                            <i class="fa fa-clone"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <?php foreach ($data["alerts"] as $alert): ?>
            <div class="alert alert-<?= $alert["type"] ?>">
                <strong>Uwaga!</strong> <?= $alert["message"] ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php if ($data["frameSetup"] !== false): ?>
    <?= $data["frameView"] ?>
<?php else: ?>
    <!-- Wycena start -->
    <div class="row">
        <div class="col-lg-12">
            <div class="portlet box green-jungle">
                <div class="portlet-title">
                    <div class="caption">
                        Wycena - <?= $data["directoryName"] ?>
                    </div>
                    <div class="actions">
                        <a class="btn btn-default" href="/plateMulti/<?= $data["directoryId"] ?>/">Main</a>
                        <div class="btn-group">
                            <a class="btn btn-default" href="javascript:;" data-toggle="dropdown"
                               aria-expanded="false">
                                <i class="fa fa-list"></i> Programy
                                <i class="fa fa-angle-down "></i>
                            </a>
                            <ul class="dropdown-menu pull-right" style="position: absolute;">
                                <?php
                                $programs = $main->getPlateMultiPart()->getPrograms();
                                ?>
                                <?php foreach ($programs as $program): ?>
                                    <li>
                                        <a href="/plateMulti/program/<?= $data["directoryId"] ?>/<?= $program->getId() ?>/">
                                            <?= $program->getSheetName() ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="btn-group">
                            <a class="btn btn-default" href="javascript:;" data-toggle="dropdown"
                               aria-expanded="false">
                                <i class="fa fa-list"></i> Detale
                                <i class="fa fa-angle-down "></i>
                            </a>
                            <ul class="dropdown-menu pull-right" style="position: absolute;">
                                <?php
                                $usedDetails = [];
                                $clients = $main->getClients();
                                ?>
                                <?php foreach ($clients as $client): ?>
                                    <?php foreach ($client->getDetails() as $detail): ?>
                                        <?php
                                        if (isset($usedDetails[$detail->getDetailId()])) {
                                            continue;
                                        }
                                        ?>
                                        <li>
                                            <a href="/plateMulti/detail/<?= $data["directoryId"] ?>/<?= $detail->getDetailId() ?>/">
                                                <?= $detail->getProject()->getDetailName() ?>
                                            </a>
                                        </li>
                                        <?php
                                        $usedDetails[$detail->getDetailId()] = true;
                                        ?>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="portlet-body">
                    <!-- Klient start -->
                    <?php foreach ($main->getClients() as $client): ?>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="portlet box yellow-saffron">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <?= $client->getClientId() ?> - <?= $client->getClientName() ?>
                                        </div>
                                    </div>
                                    <!-- Klient.body start -->
                                    <div class="portlet-body">
                                        <div class="table-scrollable">
                                            <table class="table table-striped table-hover">
                                                <thead>
                                                <tr>
                                                    <th>LP</th>
                                                    <th>Detal</th>
                                                    <th>zł/szt N</th>
                                                    <th>zł/kom N</th>
                                                    <th>zł/szt B</th>
                                                    <th>zł/kom B</th>
                                                    <th>zł/kg N</th>
                                                    <th>Waga</th>
                                                    <th>Sztuk</th>
                                                    <th>Materiał</th>
                                                    <th>Parametry</th>
                                                    <th>Projekt</th>
                                                </tr>
                                                </thead>
                                                <tbody class="small-font">
                                                <?php $loop = 0; ?>
                                                <?php foreach ($client->getDetails() as $detail): ?>
                                                    <?php
                                                    $detailProject = $detail->getProject();
                                                    $materialData = $detail->getMaterial();
                                                    $loop++;
                                                    ?>
                                                    <tr
                                                            style="cursor: pointer;"
                                                            class="detail-card-element"
                                                            data-detail-id="<?= $detail->getDetailId() ?>"
                                                            data-directory-id="<?= $data["directoryId"] ?>"
                                                    >
                                                        <td><?= $loop ?></td>
                                                        <td>
                                                            <a
                                                                    class="popovers"
                                                                    data-container="body"
                                                                    data-trigger="hover"
                                                                    data-placement="right"
                                                                    data-html="true"
                                                                    data-content="<img src='<?= $detail->getImg() ?>' alt='Brak obrazka'>"
                                                                    data-original-title=""
                                                            >
                                                                <?= $detailProject->getDetailName() ?>
                                                            </a>
                                                        </td>
                                                        <td><?= $detail->getSztN() ?></td>
                                                        <td><?= $detail->getKomN() ?></td>
                                                        <td><?= $detail->getSztB() ?></td>
                                                        <td><?= $detail->getKomB() ?></td>
                                                        <td><?= $detail->getPrcKgN() ?></td>
                                                        <td><?= round($detail->getAllWeight(), 2) ?></td>
                                                        <td><?= $detail->getCountAll() ?></td>
                                                        <td><?= $materialData->getName() ?>
                                                            - <?= $materialData->getThickness() ?></td>
                                                        <td><?= $detail->getCheckboxLabels() ?></td>
                                                        <td>
                                                            <?= $detailProject->getNumber() ?>
                                                            - <?= $detailProject->getName() ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <!-- Klient.body koniec -->
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <!-- Klient koniec -->
                    <!-- Material start -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="portlet box blue-soft">
                                <div class="portlet-title">
                                    <div class="caption">
                                        Materiał
                                    </div>
                                </div>
                                <!-- Material.body start -->
                                <div class="portlet-body">
                                    <div class="table-scrollable">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                            <tr>
                                                <th>LP</th>
                                                <th>SheetCode</th>
                                                <th>Materiał</th>
                                                <th>Ilość</th>
                                                <th>Grubość</th>
                                                <th>Rozmiar</th>
                                                <th>Czas</th>
                                                <th>Programy</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $loop = 0; ?>
                                            <?php foreach ($client->getMaterials() as $material): ?>
                                                <?php
                                                $loop++;
                                                ?>
                                                <tr>
                                                    <td><?= $loop ?></td>
                                                    <td>
                                                        <?= $material->getSheetCode() ?>
                                                    </td>
                                                    <td><?= $material->getName() ?></td>
                                                    <td><?= $material->getUsedSheetNum() ?></td>
                                                    <td><?= $material->getThickness() ?></td>
                                                    <td><?= $material->getSheetSize() ?></td>
                                                    <td><?= globalTools::seconds_to_time($material->getTime()) ?></td>
                                                    <td>
                                                        <?php foreach ($material->getPrograms() as $program): ?>
                                                            <span>
                                                                <?= $program->getSheetName() ?>,
                                                            </span>
                                                        <?php endforeach; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- Material.body koniec -->
                            </div>
                        </div>
                    </div>
                    <!-- Material koniec -->
                </div>
            </div>
        </div>
    </div>
    <!-- Wycena koniec -->
    <!-- SpisBlach start -->
    <div class="row">
        <div class="col-lg-12">
            <div class="portlet box red-soft">
                <div class="portlet-title">
                    <div class="caption">
                        Spis blach
                    </div>
                </div>
                <!-- SpisBlach.body start -->
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th>LP</th>
                                <th>SheetCode</th>
                                <th>Stal</th>
                                <th>Ilość</th>
                                <th>Grubość</th>
                                <th>Rozmiar</th>
                                <th>Czas</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $materialsPrinted = [];
                            $lp = 0;
                            ?>
                            <?php foreach ($main->getClients() as $client): ?>
                                <?php
                                $materials = $client->getMaterials();
                                ?>
                                <?php foreach ($materials as $material): ?>
                                    <?php
                                    if (isset($materialsPrinted[$material->getSheetCode()])) {
                                        continue;
                                    }
                                    $lp++;
                                    $materialsPrinted[$material->getSheetCode()] = true;
                                    ?>
                                    <tr>
                                        <td><?= $lp ?></td>
                                        <td><?= $material->getSheetCode() ?></td>
                                        <td><?= $material->getName() ?></td>
                                        <td><?= $material->getUsedSheetNum() ?></td>
                                        <td><?= $material->getThickness() ?></td>
                                        <td><?= $material->getSheetSize() ?></td>
                                        <td><?= globalTools::seconds_to_time($material->getTime()) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- SpisBlach.body koniec -->
            </div>
        </div>
    </div>
    <!-- SpisBlach koniec -->
<?php endif; ?>

<div id="flyingWindowComments">
    <div id="fwcIcon" class="font-dark"><i class="fa fa-comments"></i></div>
    <div id="fwcContent">
        <div style="float: right; position: relative; top: 20px; right: 10px;"><i id="shoutrefreshb"
                                                                                  class="fa fa-refresh"
                                                                                  style="cursor: pointer;"></i></div>
        <div style="clear: both"></div>
        <div id="shouts" style="height: 275px; padding-top: 10px;">
            <?php foreach ($data["comments"] as $comment): ?>
                <div class="shout">
                    <div class="shout-header">
                        <b><?= $comment["name"] ?></b>
                        <div style="float: right">
                            <?= $comment["date"] ?>
                        </div>
                    </div><?= $comment["content"] ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div id="stextarea" style="margin-top: 5px;">
            <form id="addshout" action="?">
                <textarea style="width: 100%;" id="shoutbox"></textarea>
                <button type="submit" class="btn blue btn-block" id="addcoment">Dodaj</button>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        $(document).on("click", function (e) {
            if (!$(e.target).is("#flyingWindowComments") && $("#flyingWindowComments").has(e.target).length === 0) {
                var $icon = $("div#fwcIcon");

                $("#fwcContent").hide();

                $icon.parent().animate({
                    width: "49px",
                    height: "40px"
                });

                $icon.show();
            }
        });
        $("div#fwcIcon").on("click", function () {
            $(this).hide();
            var $content = $(this).parent();

            $content.animate({
                width: "300px",
                height: "375px"
            }, function () {
                $("#fwcContent").show();
            });
        });

        $("button#addcoment").on("click", function (e) {
            e.preventDefault();

            var value = $("#shoutbox").val();

            if (value.length === 0) {
                return 0;
            }

            var $button = $(this);
            $button.prop("disabled", true);
            $.ajax({
                method: "GET",
                url: "/engine/addcomment.php",
                data: 'type=plateMultiCosting&eid=<?= $data["directoryId"] ?>&content=' + value
            }).done(function (response) {
                $button.prop("disabled", false);
                $("#shouts").html(response);
            });
        });
    });
</script>
<script type="text/javascript" src="/js/plateFrame/jcanvas.min.js"></script>
<script type="text/javascript" src="/js/plateFrame/plateFrame.js"></script>
<script type="text/javascript" src="/js/plateMultiPart/mainCard.js"></script>