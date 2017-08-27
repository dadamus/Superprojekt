<?php
/** @var mainCardModel $main */
$main = $data["main"];
/** @var ProgramData $program */
$program = $data["program"];
/** @var MaterialData $material */
$material = $program->getMaterial();
?>
<div class="row">
    <div class="col-lg-12">
        <h2 class="page-title">Costing - multipart - karta programu </h2>
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
                            <th>Nazwa programu</th>
                            <th>Materiał</th>
                            <th>Ilość</th>
                            <th>Rozmiar</th>
                            <th>Grubość</th>
                            <th>SheetCode</th>
                            <th>Tabela</th>
                            <th>Czas przeładunku</th>
                            <th>Wartość przeladunku</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?= $program->getSheetName() ?></td>
                            <td><?= $material->getName() ?></td>
                            <td><?= $program->getSheetCount() ?></td>
                            <td><?= $material->getSheetSize() ?></td>
                            <td><?= $material->getThickness() ?></td>
                            <td><?= $material->getSheetCode() ?></td>
                            <td><?= $material->getMatName() ?></td>
                            <td><input class="form-control"
                                       value="<?= globalTools::seconds_to_time($program->getPrgOTime() * 60) ?>"></td>
                            <td><?= $program->getPrgOValue() ?></td>
                            <td></td>
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
                    Detale
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-scrollable">
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>LP</th>
                            <th>Nazwa</th>
                            <th>Ilość</th>
                            <th>Rozmiar</th>
                            <th>RECT</th>
                            <th>RECT W</th>
                            <th>RECT WO</th>
                            <th>Waga</th>
                            <th>Single time</th>
                            <th>All time</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $lp = 0;
                        ?>
                        <?php foreach ($program->getParts() as $part): ?>
                            <?php
                            $lp++
                            ?>
                            <tr>
                                <td><?= $lp ?></td>
                                <td><?= $part->getPartName() ?></td>
                                <td><?= $part->getPartCount() ?></td>
                                <td><?= $part->getUnfoldXSize() ?> x <?= $part->getUnfoldYSize() ?></td>
                                <td><?= $part->getRectangleArea() ?></td>
                                <td><?= $part->getRectangleAreaW() ?></td>
                                <td><?= $part->getRectangleAreaWO() ?></td>
                                <td><?= $part->getWeight() ?></td>
                                <td><?= $part->getPrgDetSingleTime() ?></td>
                                <td><?= $part->getPrgDetAllTime() ?></td>
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
    <div class="col-lg-12">
        <div class="portlet box blue-chambray">
            <div class="portlet-title">
                <div class="caption">
                    Materiał
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="portlet box blue-dark">
                            <div class="portlet-title">
                                <div class="caption">
                                    Główne
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="table-scrollable">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <td>Powierzchnia arkusza</td>
                                            <td>zł/kg</td>
                                            <td>zł/mm^2</td>
                                            <td>Emulator ceny</td>
                                            <td>Waga</td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td><?= $material->getSheetSize() ?></td>
                                            <td><?= $material->getPrice() ?></td>
                                            <td><?= $material->getPrgSheetPriceMm() ?></td>
                                            <td><input class="form-control"></td>
                                            <td><?= $material->getPrgSheetAllWeight() ?></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="portlet box blue-hoki">
            <div class="portlet-title">
                <div class="caption">
                    Kosz cięcia
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="portlet box blue-madison">
                            <div class="portlet-title">
                                <div class="caption">
                                    Komplet
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="table-scrollable">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <td>Czas cięcia</td>
                                            <td>Cena cięcia</td>
                                            <td>Koszt cięcia</td>
                                            <td>Koszt cięcia + przeladowanie</td>
                                            <td>Suma czasów</td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td><?= $program->getPreTime() ?></td>
                                            <td><?= round($program->getPrgMinPrice(), 2) ?></td>
                                            <td><?= round($program->getCleanCutAll(),2) ?></td>
                                            <td><?= round($program->getCutAll(),2) ?></td>
                                            <td><?= globalTools::seconds_to_time($program->getDetAllTimeC()) ?></td>
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
                        <div class="portlet box green-sharp">
                            <div class="portlet-title">
                                <div class="caption">
                                    Detale
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="table-scrollable">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                        <tr>
                                            <td>LP</td>
                                            <td>Nazwa</td>
                                            <td>Ciecie</td>
                                            <td>Ciecie</td>
                                            <td>Komplet netto</td>
                                            <td>Ilość sztuk</td>
                                            <td>Cena kg</td>
                                            <td>Cena ostateczna</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td>zł/kom</td>
                                            <td>zł/szt</td>
                                            <td>zł</td>
                                            <td>szt</td>
                                            <td>zł/kg</td>
                                            <td>zł/szt</td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $lp = 0;
                                        ?>
                                        <?php foreach ($program->getParts() as $part): ?>
                                            <?php
                                            $lp++;
                                            ?>
                                            <tr>
                                                <td><?= $lp ?></td>
                                                <td><?= $part->getPartName() ?></td>
                                                <td><?= round($part->getComplAllPrice(),2) ?></td>
                                                <td><?= round($part->getDetailCut(), 2) ?></td>
                                                <td><?= round($part->getComplAllPrice() * $program->getSheetCount(), 2) ?></td>
                                                <td><?= $part->getAllSheetQty() ?></td>
                                                <td><?= round($part->getPriceKg(), 2) ?></td>
                                                <td><?= round($part->getLastPrice(), 2) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>