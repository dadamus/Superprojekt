<?php
$systemArray = [
    WarehouseLogService::BADGE_ABL => 'ABL',
    WarehouseLogService::BADGE_ABE => 'ABE',
    WarehouseLogService::BADGE_EDIT => 'EDIT',
];

$colorArray = [
    WarehouseLogService::NEW_ROW_TYPE => '#E26A6A',
    WarehouseLogService::QUANTITY_CHANGED_TYPE => '#5C9BD1',
    WarehouseLogService::EXTERNAL_DISPATCH_TYPE => '#F4D03F',
    WarehouseLogService::INTERNAL_DISPATCH_TYPE => '#F4D03F',
    WarehouseLogService::POSITIVE_CORRECTION_TYPE => '#F4D03F',
    WarehouseLogService::NEGATIVE_CORRECTION_TYPE => '#F4D03F',
    WarehouseLogService::LOSS_TYPE => '#F4D03F',
    WarehouseLogService::SCRAPPING_TYPE => '#F4D03F',
];
?>

<div class="row">
    <div class="col-lg-12">
        <div class="portlet box green-soft">
            <div class="portlet-title">
                <div class="caption">
                    Logi
                </div>
                <div class="actions">
                    <a href="/material/<?= $data['sheetCode'] ?>/" class="btn btn-default">Powrót</a>
                </div>
            </div>
            <div class="portlet-body">
                <table class="table table-striped table-bordered dt-responsive dataTable no-footer dtr-inline">
                    <thead>
                    <tr>
                        <th>Komunikat</th>
                        <th>Data</th>
                        <th>User</th>
                        <th>System</th>
                    </tr>
                    </thead>
                    <tbody>
                    <? var_dump($data['logs'])?>

                    <? foreach ($data['logs'] as $log): ?>
                        <tr style="background-color: <?= $colorArray[$log['type']] ?>">
                            <td><?= $log['text'] ?></td>
                            <td><?= $log['date'] ?></td>
                            <td><?= $log['name'] ?></td>
                            <td>
                                <?= $systemArray[$log['system']] ?>
                            </td>
                        </tr>
                    <? endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>