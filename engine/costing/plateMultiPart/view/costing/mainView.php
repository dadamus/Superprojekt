<?php
/** @var mainCardModel $main */
$main = $data["main"];
?>
<div class="row">
    <div class="col-lg-12">
        <h2 class="page-title">Costing - multipart </h2>
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
                        Wycena MP-1/05/2017
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
                                                    <th>Detalu</th>
                                                    <th>zł/szt N</th>
                                                    <th>zł/kom N</th>
                                                    <th>zł/szt B</th>
                                                    <th>zł/kom B</th>
                                                    <th>zł/kg N</th>
                                                    <th>zł/kg B</th>
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
                                                    <tr>
                                                        <td><?= $loop ?></td>
                                                        <td><?= $detailProject->getDetailName() ?></td>
                                                        <td><?= $detail->getSztN() ?></td>
                                                        <td><?= $detail->getKomN() ?></td>
                                                        <td><?= $detail->getSztB() ?></td>
                                                        <td><?= $detail->getKomB() ?></td>
                                                        <td><?= $detail->getMatAll() ?></td>
                                                        <td><?= $detail->getMat() ?></td>
                                                        <td><?= $detail->getCountAll() ?></td>
                                                        <td><?= $materialData->getName() ?>
                                                            - <?= $materialData->getThickness() ?></td>
                                                        <td></td>
                                                        <td><?= $detailProject->getNumber() ?>
                                                            - <?= $detailProject->getName() ?></td>
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
                                                    <td><?= $material->getSheetCode() ?></td>
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
                                <th>Grubość</th>
                                <th>Ilość</th>
                                <th>Rozmiar</th>
                                <th>Czas</th>
                            </tr>
                            </thead>
                            <tbody>

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