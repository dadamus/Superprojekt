<?php

/**
 * Created by PhpStorm.
 * User: Dawid
 * Date: 13.06.2017
 * Time: 21:28
 */

require dirname(__FILE__) . '/plateSinglePart.php';

$action = @$_GET["a"];
if (!is_null($action)) {
    require dirname(__FILE__) . '/../../config.php';
    $costingId = $_GET["costingId"];
} else {
    $action = 0;
}

$plateSinglePart = new plateSinglePart($costingId, false);

if ($action === "calculate" || $action === "save") {
    $plateSinglePart->setDataFromForm();
}
$plateSinglePart->getCostingData();

$data = $plateSinglePart->getSerializedData();
$settings = $plateSinglePart->getMaterialData();
$attribute_values = $plateSinglePart->getAttributes();

if ($action === "save") {
    $plateSinglePart->saveInputData();
    $plateSinglePart->saveCostingData();
    $plateSinglePart->saveAttributes();
    echo "ok";
    die;
}

if ($action === "calculate") {
    echo json_encode([
        "inputData" => $data["inputData"],
        "outputData" => $data["outputData"],
        "settings" => $settings,
        "attribute" => $attribute_values
    ]);
    die;
}

?>
<form id="plateSinglePartForm" action="?" method="post" data-id="<?= $costingId ?>">
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-title">Costing - single plate
                <small><?= $data["inputData"]["detal_name"] ?> / <?= $data["inputData"]["sheet_name"] ?></small>
            </h2>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="portlet box blue-sharp">
                    <div class="portlet-title">
                        <div class="caption">General Info</div>
                    </div>
                    <div class="portlet-body flip-scroll">
                        <table class="table table-striped table-condensed flip-content">
                            <thead>
                            <tr>
                                <td>Nazwa</td>
                                <td>Wartość</td>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Sztuk</td>
                                <td><input type="text" disabled="disabled"
                                           value="<?= $data["inputData"]["part_count"] ?>"
                                           name="inputData[part_count]" id="inputData[part_count]" class="form-control">
                                </td>
                            </tr>
                            <tr>
                                <td>SheetCode</td>
                                <td><input type="text" disabled="disabled"
                                           value="<?= $data["inputData"]["sheet_code"] ?>"
                                           name="inputData[sheet_code]" id="inputData[sheet_code]" class="form-control">
                                </td>
                            </tr>
                            <tr>
                                <td>Stal</td>
                                <td><input type="text" disabled="disabled"
                                           value="<?= $data["inputData"]["material_type"] ?>"
                                           name="inputData[material_type]" id="inputData[material_type]"
                                           class="form-control"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="portlet box yellow-gold">
                    <div class="portlet-title">
                        <div class="caption">Ceny ostateczne</div>
                    </div>
                    <div class="portlet-body flip-scroll">
                        <table class="table table-striped table-condensed flip-content">
                            <thead>
                            <tr>
                                <td>Nazwa</td>
                                <td>Netto</td>
                                <td>Brutto</td>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Komplet</td>
                                <td><input type="text" value="<?= round($data["outputData"]["price_kom_n"], 2) ?>"
                                           name="outputData[price_kom_n]" id="outputData[price_kom_n]"
                                           class="form-control">
                                </td>
                                <td><input type="text" value="<?= round($data["outputData"]["price_kom_b"], 2) ?>"
                                           name="outputData[price_kom_b]" id="outputData[price_kom_b]"
                                           class="form-control">
                                </td>
                            </tr>
                            <tr>
                                <td>Detal</td>
                                <td><input type="text" value="<?= round($data["outputData"]["price_det_n"], 2) ?>"
                                           name="outputData[price_det_n]" id="outputData[price_det_n]"
                                           class="form-control">
                                </td>
                                <td><input type="text" value="<?= round($data["outputData"]["price_det_b"], 2) ?>"
                                           name="outputData[price_det_b]" id="outputData[price_det_b]"
                                           class="form-control">
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="portlet box red">
                    <div class="portlet-title">
                        <div class="caption">Material Calculation</div>
                    </div>
                    <div class="portlet-body flip-scroll">
                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <table class="table table-striped table-condensed flip-content">
                                    <thead>
                                    <tr>
                                        <td>Nazwa</td>
                                        <td>Wartość</td>
                                        <td>Jednostka</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>Cena blachy</td>
                                        <td><input type="text" disabled="disabled"
                                                   value="<?= round($data["outputData"]["details_mat_price"], 2) ?>"
                                                   name="outputData[details_mat_price]"
                                                   id="outputData[details_mat_price]"
                                                   class="form-control"></td>
                                        <td>zł/kg</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td><input type="text"
                                                   value="<?= round($data["inputData"]["sheet_price_all"], 2) ?>"
                                                   name="inputData[sheet_price_all]" id="inputData[sheet_price_all]"
                                                   class="form-control"></td>
                                        <td>zł</td>
                                    </tr>
                                    <tr>
                                        <td>Cena materiału</td>
                                        <td><input type="text" value="<?= $settings["materialPrice"] ?>"
                                                   name="settings[materialPrice]" id="settings[materialPrice]"
                                                   class="form-control"></td>
                                        <td><input type="text"
                                                   value="<?= round($settings["materialPrice"] / floatval($data["inputData"]["part_count"]), 2) ?>"
                                                   name="settings[materialPriceDet]" id="settings[materialPriceDet]"
                                                   class="form-control"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-sm-12 col-md-6">
                                <table class="table table-striped table-condensed flip-content">
                                    <thead>
                                    <tr>
                                        <td>Nazwa</td>
                                        <td>Wartość</td>
                                        <td>Jednostka</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>Waga odpadu</td>
                                        <td><input type="text" disabled="disabled"
                                                   value="<?= $data["outputData"]["remnant_unf_per"] ?>"
                                                   name="outputData[remnant_unf_per]" id="outputData[remnant_unf_per]"
                                                   class="form-control"></td>
                                        <td>g</td>
                                    </tr>
                                    <tr>
                                        <td>Cena odpadu</td>
                                        <td><input type="text" disabled="disabled"
                                                   value="<?= round($data["outputData"]["remnant_unf_value"], 2) ?>"
                                                   name="outputData[remnant_unf_value]"
                                                   id="outputData[remnant_unf_value]"
                                                   class="form-control"></td>
                                        <td>zł</td>
                                    </tr>
                                    <tr>
                                        <td>Powierzchnia pozorna detali</td>
                                        <td><input type="text" disabled="disabled"
                                                   value="<?= $data["outputData"]["details_ext_unf"] ?>"
                                                   name="outputData[details_ext_unf]" id="outputData[details_ext_unf]"
                                                   class="form-control"></td>
                                        <td>mm^2</td>
                                    </tr>
                                    <tr>
                                        <td>Waga odpadu</td>
                                        <td><input type="text" disabled="disabled"
                                                   value="<?= $data["outputData"]["remnant_unf"] ?>"
                                                   name="outputData[remnant_unf]" id="outputData[remnant_unf]"
                                                   class="form-control"></td>
                                        <td>kg</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="portlet box purple-sharp">
                    <div class="portlet-title">
                        <div class="caption">Checkboxy</div>
                    </div>
                    <div class="portlet-body flip-scroll">
                        <table class="table table-striped table-condensed flip-content">
                            <thead>
                            <tr>
                                <td></td>
                                <td>Nazwa</td>
                                <td>Detal</td>
                                <td>Komplet</td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $attributes = [
                                "Gięcie" => true,
                                "Projekt" => true,
                                "Spawanie" => true,
                                "Malowanie" => true,
                                "Ocynkowanie" => true,
                                "Gwintowanie" => true,
                                "Common Cut" => false
                            ];

                            $id = 1;
                            foreach ($attributes as $attribute => $value) {
                                $checked = "";
                                if ($attribute_values[$id]["checked"]) {
                                    $checked = 'checked="checked"';
                                }
                                echo '<tr><td><input type="checkbox" name="attribute[]" value="' . $id . '" style="width: 20px; height: 20px;" ' . $checked . ' /></td> <td>' . $attribute . '</td>';
                                if ($value) {
                                    $attribute_value = $attribute_values[$id]["value"];
                                    echo '
                                        <td><input type="text" name="a' . $id . 'i1" id="a' . $id . 'i1" value="' . $attribute_value . '" class="form-control ai"/></td>
                                        <td><input type="text" name="a' . $id . 'i2" id="a' . $id . 'i2" value="' . ($attribute_value * $data["inputData"]["part_count"]) . '" class="form-control aik"/></td>
                                    ';
                                } else {
                                    echo '<td></td><td></td>';
                                }
                                echo '</tr>';
                                $id++;
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="portlet box purple-sharp">
                    <div class="portlet-title">
                        <div class="caption">Cut Calculation</div>
                    </div>
                    <div class="portlet-body flip-scroll">
                        <table class="table table-striped table-condensed flip-content">
                            <thead>
                            <tr>
                                <td>Nazwa</td>
                                <td>Wartość</td>
                                <td>Jednostka</td>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Cut Time</td>
                                <td><input type="text" disabled="disabled"
                                           value="<?= $data["outputData"]["cut_time"] ?>"
                                           name="outputData[cut_time]" id="outputData[cut_time]" class="form-control">
                                </td>
                                <td>s</td>
                            </tr>
                            <tr>
                                <td>Clean Cut</td>
                                <td><input type="text" disabled="disabled"
                                           value="<?= round($data["outputData"]["clean_cut"], 2) ?>"
                                           name="outputData[clean_cut]" id="outputData[clean_cut]" class="form-control">
                                </td>
                                <td>zł</td>
                            </tr>
                            <tr>
                                <td>Cut Komp</td>
                                <td><input type="text" disabled="disabled"
                                           value="<?= round($data["outputData"]["cut_komp_n"], 2) ?>"
                                           name="outputData[cut_komp_n]" id="outputData[cut_komp_n]"
                                           class="form-control">
                                </td>
                                <td>zł</td>
                            </tr>
                            <tr>
                                <td>Cut Det</td>
                                <td><input type="text" disabled="disabled"
                                           value="<?= round($data["outputData"]["cut_detal_n"], 2) ?>"
                                           name="outputData[cut_detal_n]" id="outputData[cut_detal_n]"
                                           class="form-control">
                                </td>
                                <td>zł</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-2 col-lg-offset-10" style="text-align: right">
                <button id="saveCosting" type="button" class="btn btn-success">Zapisz</button>
            </div>
        </div>
</form>
<script type="text/javascript" src="/js/plateSinglePartForm/plateSinglePartForm.js"></script>