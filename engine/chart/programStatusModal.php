<?php
$firstDetail = reset($listItems);
$state = $firstDetail['state'];

$firstState = 0;
$maxState = count($listStatus) - 1;
?>

<div id="status-modal" class="modal" tabindex="-1" data-width="760">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3>Zmiana statusu</h3>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-lg-12">
                <select class="form-control" name="list-state">
                    <?php for ($s = $firstState; $s <= $maxState; $s++): ?>
                        <option value="<?= $s ?>"
                                <?php if ($s == $state): ?>selected<?php endif ?>><?= $listStatus[$s] ?></option>
                    <?php endfor ?>
                </select>
            </div>
        </div>
        <div class="row list-details" style="display: none">
            <div class="col-lg-12">
                <table class="table">
                    <thead>
                    <tr>
                        <td>Nazwa</td>
                        <td>Wyciętych</td>
                        <td>Do wyciecia</td>
                        <td>Odpad</td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($listItems as $detail): ?>
                        <tr>
                            <td>
                                <?= (strlen($detail['src']) > 0 ? $detail['src'] : '-') ?>
                            </td>
                            <td>
                                <input type="number" name="detail_<?= $detail['queue_detail_id'] ?>"
                                       value="<?= ($detail['state'] == 2 ? $detail['cutting'] : $detail['qantity']) ?>"
                                       data-detail-id="<?= $detail['queue_detail_id'] ?>"
                                class="form-control detail-count">
                            </td>
                            <td>
                                <?= $detail['qantity'] ?>
                            </td>
                            <td>
                                <input type="number" class="form-control">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn">Zamknij</button>
        <button type="button" class="btn btn-primary submit-status-change" data-list-id="<?= $firstDetail['list_id'] ?>">Zapisz</button>
    </div>
</div>