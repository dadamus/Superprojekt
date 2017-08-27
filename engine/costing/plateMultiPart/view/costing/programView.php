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

<form id="count" method="POST" action="?">
    <input type="hidden" name="program_id" value="<?= $program->getId() ?>">
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
                                <td><b><?= $material->getName() ?></b> - <i><?= $material->getMatName() ?></i></td>
                                <td><?= $program->getSheetCount() ?></td>
                                <td><?= $material->getSheetSize() ?></td>
                                <td><?= $material->getThickness() ?></td>
                                <td><?= $material->getSheetCode() ?></td>
                                <td><b><?= $program->getParts()[0]->getLaserMatName() ?></b></td>
                                <td><input
                                            class="form-control"
                                            value="<?= globalTools::seconds_to_time($program->getPrgOTime() * 60) ?>"
                                            id="time1"
                                            name="oTime"
                                    ></td>
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
                                <th>Waga [kg]</th>
                                <th>Single time</th>
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
                                    <td><?= $part->getWeight() / 1000 ?></td>
                                    <td><?= $part->getPrgDetSingleTime() ?></td>
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
                                                <td>zł/arkusz</td>
                                                <td>Waga</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td><?= $material->getSheetSize() ?></td>
                                                <td><?= round($material->getPrgSheetPriceKg(), 2) ?></td>
                                                <td>
                                                    <input
                                                            class="form-control"
                                                            name="prgSheetPrice"
                                                            value="<?= $material->getPrgSheetPrice() ?>"
                                                    >
                                                </td>
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
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td><?= $program->getPreTime() ?></td>
                                                <td><input class="form-control"
                                                           value="<?= round($program->getPrgMinPrice(), 2) ?>"
                                                           name="prgMinPrice"
                                                    ></td>
                                                <td><?= round($program->getCleanCutAll(), 2) ?></td>
                                                <td><?= round($program->getCutAll(), 2) ?></td>
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
                                                <td><b>LP</b></td>
                                                <td><b>Nazwa</b></td>
                                                <td><b>Ciecie</b></td>
                                                <td><b>Ciecie</b></td>
                                                <td><b>Cięcie all netto</b></td>
                                                <td><b>Ilość sztuk</b></td>
                                                <td><b>Cena kg</b></td>
                                                <td><b>Cena ostateczna</b></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td><i>[zł/kom]</i></td>
                                                <td><i>[zł/szt]</i></td>
                                                <td><i>[zł]</i></td>
                                                <td><i>[szt]</i></td>
                                                <td><i>[zł/kg]</i></td>
                                                <td><i>[zł/szt]</i></td>
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
                                                    <td><?= round($part->getComplAllPrice(), 2) ?></td>
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
    <div class="row">
        <div class="col-lg-2 col-lg-offset-10">
            <button class="btn btn-info" type="submit">Licz</button>
            <a class="btn btn-success" href="javascript:;" id="saveProgram">Zapisz</a>
        </div>
    </div>
</form>

<script type="text/javascript" src="/js/plateMultiPart/programCard.js"></script>