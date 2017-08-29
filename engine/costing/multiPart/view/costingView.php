<div class="row" id="dirContainer" data-id="<?= $data["dirId"] ?>">
    <div class="col-lg-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    Wycena: <?= $data["dirName"] ?>
                </div>
            </div>
            <div class="portlet-body">
                <?php
                $lp = 0;
                ?>
                <?php foreach ($data["mpw"] as $mpw): ?>
                    <?php
                    $lp++;
                    ?>
                    <!-- MPW: START -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="portlet box blue-hoki">
                                <div class="portlet-title">
                                    <div class="caption">
                                        Grupa <?= $lp ?>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <!-- MPW.Body: START -->
                                    <div class="row">
                                        <div class="col-lg-8">
                                            <div class="portlet box grey-cascade">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        Detale:
                                                    </div>
                                                </div>
                                                <div class="portlet-body">
                                                    <div class="table-scrollable">
                                                        <table class="table">
                                                            <thead>
                                                            <tr>
                                                                <th>Id</th>
                                                                <th>Nazwa detalu</th>
                                                                <th>Nazwa</th>
                                                                <th></th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php foreach ($mpw["details"] as $detail): ?>
                                                                <!-- MPW.Body.Detail: START -->
                                                                <tr>
                                                                    <td><?= $detail["detail_id"] ?></td>
                                                                    <td><?= $detail["real_detail_name"] ?></td>
                                                                    <td><?= $detail["detail_name"] ?></td>
                                                                    <td>
                                                                        <a
                                                                                href="javascript:;"
                                                                                data-mpw-id="<?= $detail["id"] ?>"
                                                                                data-detail-id="<?= $detail["detail_id"] ?>"
                                                                                class="mpw-detail-delete"
                                                                        >
                                                                            <i class="fa fa-times"></i>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                                <!-- MPW.Body.Detail: END -->
                                                            <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="portlet box grey-mint">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        Ustawienia:
                                                    </div>
                                                </div>
                                                <div class="portlet-body">
                                                    <!-- MPW.Body.Settings: START -->
                                                    <div class="table-scrollable">
                                                        <table class="table">
                                                            <?php
                                                            $mpwData = reset($mpw["details"]);
                                                            ?>
                                                            <tbody>
                                                            <tr>
                                                                <td>Wersja:</td>
                                                                <td>
                                                                    <b><?= $mpwData["version"] ?></b>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Materiał:</td>
                                                                <td>
                                                                    <b><?= $mpwData["material_name"] ?></b>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Grubość:</td>
                                                                <td>
                                                                    <b><?= $mpwData["thickness"] ?></b>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Sztuk:</td>
                                                                <td>
                                                                    <b><?= $mpwData["pieces"] ?></b>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Atrybuty:</td>
                                                                <td>
                                                                    <?php
                                                                    $attributes = json_decode($mpwData["atribute"], true);
                                                                    ?>
                                                                    <?php foreach ($attributes as $attribute): ?>
                                                                        <b><?= _getChecboxText($attribute) ?></b>
                                                                    <?php endforeach; ?>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <!-- MPW.Body.Settings: END -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- MPW.Body: END -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- MPW: END -->
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="/js/multiPart/multiPartCosting.js"></script>