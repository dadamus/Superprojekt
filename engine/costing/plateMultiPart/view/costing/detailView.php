<?php
/** @var mainCardModel $mainCardModel */
$mainCardModel = $data["card"];
/** @var mainCardClientModel $client */
$mainClient = $data["mainClient"];
/** @var mainCardDetailModel $mainDetail */
$mainDetail = $data["mainDetail"];
/** @var ProgramData[] $programs */
$programs = $data["programs"];
/** @var ProgramCardPartData[] $programDetail */
$programDetail = $data["programDetail"];

$laserMaterialName = $programDetail[reset($programs)->getSheetName()]->getLaserMatName();


?>
<div class="row">
    <div class="col-lg-12">
        <h2 class="page-title">Costing - multipart - karta detalu</h2>
    </div>
</div>

<form action="?" method="POST" id="count">
    <input type="hidden" name="detail_id" value="<?= $mainDetail->getDetailId() ?>">
    <div class="row">
        <div class="col-lg-12">
            <div class="portlet box green-soft">
                <div class="portlet-title">
                    <div class="caption">
                        Informacje
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Nazwa</th>
                                <th>Stal</th>
                                <th>Grubość</th>
                                <th>Tabela</th>
                                <th>Sztuk</th>
                                <th>Właściciel</th>
                                <th>Projekt</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><?= $mainDetail->getProject()->getDetailName() ?></td>
                                <td><?= $mainDetail->getMaterial()->getName() ?></td>
                                <td><?= $mainDetail->getMaterial()->getThickness() ?></td>
                                <td><?= $laserMaterialName ?></td>
                                <td><?= $mainDetail->getCountAll() ?></td>
                                <td><?= $mainClient->getClientName() ?></td>
                                <td><?= $mainDetail->getProject()->getNumber() ?>
                                    - <?= $mainDetail->getProject()->getName() ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="portlet box yellow-saffron">
                <div class="portlet-title">
                    <div class="caption">
                        Ceny wynikowe:
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table">
                            <thead>
                            <tr>
                                <th><b>Szt N</b></th>
                                <th><b>Szt B<</b></th>
                                <th><b>Komp N</b></th>
                                <th><b>Komp B</b></th>
                                <th><b>Cięcie/szt</b></th>
                                <th><b>Mat/szt</b></th>
                                <th><b>Mat N</b></th>
                                <th><b>Cięcie N</b></th>
                                <th><b>Komp N</b></th>
                                <th><b>Komp B</b></th>
                            </tr>
                            <tr>
                                <th><i>[zł]</i></th>
                                <th><i>[zł]</i></th>
                                <th><i>[zł]</i></th>
                                <th><i>[zł]</i></th>
                                <th><i>[zł]</i></th>
                                <th><i>[zł]</i></th>
                                <th><i>[zł]</i></th>
                                <th><i>[zł]</i></th>
                                <th><i>[zł/kg]</i></th>
                                <th><i>[zł/kg]</i></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><?= $mainDetail->getSztN() ?></td>
                                <td><?= $mainDetail->getSztB() ?></td>
                                <td><?= $mainDetail->getKomN() ?></td>
                                <td><?= $mainDetail->getKomB() ?></td>
                                <td><?= $mainDetail->getCut() ?></td>
                                <td><?= $mainDetail->getMat() ?></td>
                                <td><?= $mainDetail->getMatAll() ?></td>
                                <td><?= $mainDetail->getCutAll() ?></td>
                                <td><?= $mainDetail->getPrcKgN() ?></td>
                                <td><?= $mainDetail->getPrcKgB() ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="row" style="margin-bottom: 10px;">
                <div class="col-lg-12">
                    <img src="/assets/global/plugins/holder.js/200x200" alt="200x200"
                         style="height: 200px; margin: 0 auto; width: 100%; display: block;">
                </div>
            </div>
            <div clas="row">
                <div class="col-lg-12">
                    <div class="portlet box green">
                        <div class="portlet-title">
                            <div class="caption">
                                Szczegóły
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="table-scrollable">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Współczynnik prowizji</th>
                                        <th>Waga pojedyńczego detalu</th>
                                        <th>Waga kompletu</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <input class="form-control" name="p_factor"
                                                   value="<?= $mainDetail->getPriceFactor() ?>">
                                        </td>
                                        <td>
                                            <?= $mainDetail->getWeight() ?>
                                        </td>
                                        <td>
                                            <?= $mainDetail->getAllWeight() ?>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="portlet box grey">
                <div class="portlet-title">
                    <div class="caption">
                        Atrybuty
                    </div>
                </div>
                <div class="portlet-body">
                    <?= $data["checkbox"] ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="portlet box purple-soft">
                <div class="portlet-title">
                    <div class="caption">
                        Programy
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th>LP</th>
                                <th>Nazwa programu</th>
                                <th>Ilość programu</th>
                                <th>Sztuk w programie</th>
                                <th>SheetCode</th>
                                <th>Mat zł</th>
                                <th>Cięcie N</th>
                                <th>Program N</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $lp = 0;
                            ?>
                            <?php foreach ($programs as $program): ?>
                                <?php
                                $lp++;
                                /** @var ProgramCardPartData $detail */
                                $detail = $programDetail[$program->getSheetName()];
                                ?>
                                <tr>
                                    <td><?= $lp ?></td>
                                    <td><?= $program->getSheetName() ?></td>
                                    <td><?= $program->getSheetCount() ?></td>
                                    <td><?= $program->getSheetCount() * $detail->getPartCount() ?></td>
                                    <td><?= $program->getMaterial()->getSheetCode() ?></td>
                                    <td><?= round($detail->getMatVal(), 2) ?></td>
                                    <td><?= round($detail->getDetailCut(), 2) ?></td>
                                    <td><?= round($detail->getLastPrice(), 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-2 col-lg-offset-10">
            <button class="btn btn-info" type="submit">Licz</button>
            <a class="btn btn-success" id="saveButton">Zapisz</a>
        </div>
    </div>

</form>

<script type="text/javascript">
    var partQuantity = <?= $detail->getAllSheetQty() ?>
</script>
<script type="text/javascript" src="/js/plateMultiPart/detailCard.js"></script>