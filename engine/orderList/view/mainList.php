<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 28.09.2017
 * Time: 19:52
 */

$orders = $data['orders'];

?>

<div class="row">
    <div class="col-lg-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    Lista zamówień
                </div>
            </div>
            <div class="portlet-body">
                <table id="orders" class="table table-striped table-bordered table-hover table-checkable order-column">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Firma</th>
                        <th>Status</th>
                        <th>Deadline</th>
                        <th>Priorytet</th>
                        <th>Data utworzenia</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= $order['id'] ?></td>
                            <td><strong><?= $order['client_id'] ?></strong> - <?= $order['client_name'] ?></td>
                            <td><?= $order['state'] ?></td>
                            <td>
                                <?= ($order['deadline_on'] ? $order['deadline'] : '-') ?>
                                <?php if ($order['deadline_on'] > 0 && strtotime($order['deadline']) < time()): ?>
                                    <span style="color: red">
                                    <i class="fa fa-exclamation-triangle"></i>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <?php
                            $status = $text = "";
                            switch ($order['priority']) {
                                case 1:
                                    $text = "Niski";
                                    $status = 'default';
                                    break;
                                case 2:
                                    $text = "Normalny";
                                    $status = 'info';
                                    break;
                                case 3:
                                    $text = "Wysoki";
                                    $status = 'warning';
                                    break;
                                case 4:
                                    $text = "Bardzo wysoki";
                                    $status = 'danger';
                                    break;
                            }
                            ?>
                            <td><span class="label label-<?= $status ?>"><?= $text ?></span></td>
                            <td><?= $order['created_at'] ?></td>
                            <td>
                                <a href="/order/<?= $order['id'] ?>/">
                                    <i class="fa fa-sign-in fa-2x"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#orders').DataTable();
    });
</script>