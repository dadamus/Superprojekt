<div class="alert alert-info">
    <div style="float: right;"><a href=""><i style="cursor: pointer;" class="fa fa-external-link"></i></a></div>
    <div style="clear: both;"></div>
</div>
<table class="table table-striped">
    <tbody>
    <tr>
        <td>Nazwa:</td>
        <td><?= $data['program']['name'] ?></td>
    </tr>
    <tr>
        <td>SheetCode:</td>
        <td><?= $data['mpwData']["SheetCode"] ?></td>
    </tr>
    <tr>
        <td>ChildSheetCode:</td>
        <td><?= $data['mpwData']["ChildSheetCode"] ?></td>
    </tr>
    <tr>
        <td>Nazwa materiału:</td>
        <td><?= $data['mpwData']["MaterialTypeName"] ?></td>
    </tr>
    <tr>
        <td>LaserMatName:</td>
        <td><?= $data['mpwData']["LaserMatName"] ?></td>
    </tr>
    <tr>
        <td>Grubość:</td>
        <td><?= $data['mpwData']["Thickness"] ?></td>
    </tr>
    <tr>
        <td>Obrazek:</td>
        <td><img src="<?= str_replace('/var/www/html', '', $data['mpwData']['imageSrc']) ?>" width="200px"></td>
    </tr>
    </tbody>
</table>

<table class="table table-striped">
    <thead>
    <tr>
        <th>Numer</th>
        <th>Status</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($data['list'] as $listItem): ?>
        <tr>
            <td>
                <?= $listItem['lp'] ?>
            </td>
            <td class="list-item-state" data-item-id="<?= $listItem['id'] ?>">
                <?= $data['statusList'][$listItem['state']] ?>
            </td>
            <td>
                <a data-url="<?= '/engine/chart/program.php?action=2&p=' . $data['mpwData']['id'] . '&lp=' . $listItem['lp'] ?>"
                   data-toggle="modal" class="ajax-modal">
                    <i class="fa fa-pencil"></i>
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php if (isset($data['details'])): ?>

    <table class="table">
        <thead>
        <tr>
            <th>Nazwa</th>
            <th>Licznik</th>
            <th>Waga detalu</th>
            <th>Waga odpadu</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data['details'] as $detail): ?>
        <tr>
            <td><?= $detail['detail_name'] ?></td>
            <td>
                <?= $detail['cutting'] ?> / <?= $detail['quantity'] ?> / <?= $detail['waste'] ?>
            </td>
            <td>
                <?= $detail['details_cutted'] ?>
            </td>
            <td>
                <?= $detail['details_remnant'] ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<div id="modal-container"></div>