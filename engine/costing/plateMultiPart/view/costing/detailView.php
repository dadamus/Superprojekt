<?php
/** @var mainCardModel $mainCardModel */
$mainCardModel = $data["card"];
/** @var mainCardClientModel $client */
$mainClient = null;
/** @var mainCardDetailModel $mainDetail */
$mainDetail = null;

$plateMultiPart = $mainCardModel->getPlateMultiPart();

/** @var ProgramData[] $programs */
$programs = [];
/** @var ProgramCardPartData $programDetail */
$programDetail = [];

foreach ($plateMultiPart->getPrograms() as $program) {
    foreach ($program->getParts() as $part) {
        if ($part->getDetailId() == $data["detailId"]) {
            if (!isset($programs[$program->getSheetName()])) {
                $programs[$program->getSheetName()] = $program;
                $programDetail[$program->getSheetName()] = $part;
                continue 2;
            }
        }
    }
}

foreach ($mainCardModel->getClients() as $client) {
    $detail = $client->getDetail($data["detailId"]);

    if ($detail !== false) {
        $mainClient = $client;
        $mainDetail = $detail;
    }
}

?>
<div class="row">
    <div class="col-lg-12">
        <h2 class="page-title">Costing - multipart - karta detalu</h2>
    </div>
</div>

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
                            <td><?= $mainDetail->getMaterial()->getMatName() ?></td>
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
                            <th>Szt N</th>
                            <th>Szt B</th>
                            <th>Komp N</th>
                            <th>Komp B</th>
                            <th>Cięcie/szt</th>
                            <th>Mat/szt</th>
                            <th>Mat N</th>
                            <th>Cięcie N</th>
                            <th>Komp N</th>
                            <th>Komp B</th>
                        </tr>
                        <tr>
                            <th>zł</th>
                            <th>zł</th>
                            <th>zł</th>
                            <th>zł</th>
                            <th>zł</th>
                            <th>zł</th>
                            <th>zł</th>
                            <th>zł</th>
                            <th>zł/kg</th>
                            <th>zł/kg</th>
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
                                        <input class="form-control" value="<?= $mainDetail->getPriceFactor() ?>">
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
                                <td><?= $detail->getMatVal() ?></td>
                                <td><?= $detail->getDetailCut() ?></td>
                                <td><?= $detail->getComplAllPrice() ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>