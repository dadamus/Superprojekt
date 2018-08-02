<style>
    #details.table td {
        font-size: x-small !important;
    }
</style>

<div class="row" id="dirContainer" data-id="<?= $data["dirId"] ?>">
    <form id="mpwEdit">
        <input type="text" hidden="hidden" value='<?= $data['detailsDataJson'] ?>' name="details"/>
        <div class="col-lg-12">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        Wycena: <?= $data["dirName"] ?>
                    </div>
                    <div class="actions">
                        <a class="btn btn-success" id="saveMpwEdit">Zapisz</a>
                    </div>
                </div>
                <table id="details" class="table table-striped">
                    <thead>
                    <tr>
                        <th></th>
                        <th>ID</th>
                        <th>Nazwa detalu</th>
                        <th>Nazwa</th>
                        <th>Sztuk</th>
                        <th>Wersja</th>
                        <th>Material Type</th>
                        <th>Material Name</th>
                        <th>L Material Name</th>
                        <th>Grubość</th>
                        <th>Atrubuty</th>
                        <th></th>
                    </tr>
                    </thead>
                    <?php
                    $lp = 0;
                    ?>
                    <?php foreach ($data["mpw"] as $mpw): ?>
                        <?php foreach ($mpw['details'] as $detail): ?>
                            <tr>
                                <td></td>
                                <td><?= $detail['detail_id'] ?></td>
                                <td><?= $detail['real_detail_name'] ?></td>
                                <td><?= $detail['detail_name'] ?></td>
                                <td><input type="number" value="<?= $detail['pieces'] ?>"
                                           name="pieces[<?= $detail['id'] ?>]"
                                           data-name="pieces" class="form-control"/></td>
                                <td><?= $detail['version'] ?></td>
                                <td>
                                    <select data-name="material" name="material[<?= $detail['id'] ?>]"
                                            class="form-control material-picker">
                                        <?php foreach ($data['materials'] as $material): ?>
                                            <option
                                                    value="<?= $material['id'] ?>"
                                                <?= ($material['name'] == $detail['material_name'] ? 'selected="selected"' : '') ?>
                                            >
                                                <?= $material['name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td class="material-name">
                                    <?= $detail['material_type_name'] ?>
                                </td>
                                <td>
                                    <select name="laser-material-name[<?= $detail['id'] ?>]" class="form-control laser-material-name-picker">
                                        <option value="<?= $detail['laser_material_id'] ?>"><?= $detail['laser_material_name'] ?></option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control thickness-picker"
                                            name="thickness[<?= $detail['id'] ?>]"
                                            data-name="thickness">
                                        <?php foreach ($detail['material_thickness_info'] as $t): ?>
                                            <option <?php if ($t['Thickness'] == $detail['thickness']): ?>selected="selected"<?php endif; ?>>
                                                <?= $t['Thickness'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                <td>
                                    <?php if (strlen($detail['attributes']) > 0): ?>
                                        <?php foreach (json_decode($detail['attributes'], true) as $attribute): ?>
                                            <b><?= _getChecboxText($attribute); ?></b>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="javascript:;"
                                       class="mpw-detail-delete"
                                       data-mpw-id="<?= $detail['mpw_id'] ?>"
                                       data-detail-id="<?= $detail['id'] ?>"
                                    >
                                        <i class="fa fa-times"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript" src="/js/multiPart/multiPartCosting.js"></script>