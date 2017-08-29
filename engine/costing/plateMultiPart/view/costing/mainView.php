<?php
/** @var mainCardModel $main */
$main = $data["main"];
?>
<div class="row">
    <div class="col-lg-7">
        <h2 class="page-title">Costing - multipart </h2>
    </div>
    <div class="col-lg-5">
        <form class="form-horizontal" id="changeDesignerForm" action="?" data-dir-id="<?= $data["directoryId"] ?>">
            <div class="form-group">
                <label class="col-md-2 control-label">Projektant: </label>
                <div class="col-md-8">
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
                <?php if (!$main->isBlocked()): ?>
                    <div class="col-md-2">
                        <a
                                class="btn red"
                                href="javascript:;"
                                id="costingBlock"
                                data-dir-id="<?= $data["directoryId"] ?>"
                        >
                            Blokuj
                        </a>
                    </div>
                <?php endif; ?>
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
                                                        <td><?= $detailProject->getDetailName() ?></td>
                                                        <td><?= $detail->getSztN() ?></td>
                                                        <td><?= $detail->getKomN() ?></td>
                                                        <td><?= $detail->getSztB() ?></td>
                                                        <td><?= $detail->getKomB() ?></td>
                                                        <td><?= $detail->getPrcKgN() ?></td>
                                                        <td><?= round($detail->getAllWeight(), 2) ?></td>
                                                        <td><?= $detail->getCountAll() ?></td>
                                                        <td><?= $materialData->getName() ?>
                                                            - <?= $materialData->getThickness() ?></td>
                                                        <td></td>
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

<script type="text/javascript" src="/js/plateFrame/jcanvas.min.js"></script>
<script type="text/javascript" src="/js/plateFrame/plateFrame.js"></script>
<script type="text/javascript" src="/js/plateMultiPart/mainCard.js"></script>