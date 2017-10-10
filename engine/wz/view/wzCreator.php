<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 07.10.2017
 * Time: 10:39
 */

$clientData = $data['clientData'];
$address = explode(',', $clientData['address']);
if (strlen($address[0]) > 1 || strlen($address[1]) > 1) {
    $clientData['address'] = $address;
} else {
    $clientData['address'] = [null, null];
}
?>

<form class="form" id="wz-form">
    <div class="row">
        <div class="col-md-6">
            <div class="portlet box blue-dark">
                <div class="portlet-title">
                    <div class="caption">
                        Sprzedawca
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <input class="form-control" name="seller_name" placeholder="Nazwa"/>
                        </div>
                        <div class="form-group col-md-12">
                            <input class="form-control" name="seller_address1" placeholder="Adres"/>
                        </div>
                        <div class="form-group col-md-12">
                            <input class="form-control" name="seller_address2" placeholder="Adres"/>
                        </div>
                        <div class="form-group col-md-12">
                            <input class="form-control" name="seller_nip" placeholder="Nip"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="portlet box blue-madison">
                <div class="portlet-title">
                    <div class="caption">
                        Nabywca
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <input class="form-control" name="buyer_name" placeholder="Nazwa"
                                   value="<?= $clientData['name'] ?>"/>
                        </div>
                        <div class="form-group col-md-12">
                            <input class="form-control" name="buyer_address1" placeholder="Adres"
                                   value="<?= $clientData['address'][0] ?>"/>
                        </div>
                        <div class="form-group col-md-12">
                            <input class="form-control" name="buyer_address2" placeholder="Adres"
                                   value="<?= $clientData['address'][1] ?>"/>
                        </div>
                        <div class="form-group col-md-12">
                            <input class="form-control" name="buyer_nip" placeholder="Nip"
                                   value="<?= $clientData['nip'] ?>"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="portlet box green-turquoise">
                <div class="portlet-title">
                    <div class="caption">
                        Towary
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>
                                <input type="checkbox" class="col-enabled[]" checked value="1"> Kod
                            </th>
                            <th>
                                <input type="checkbox" class="col-enabled[]" checked value="2"> Nazwa
                            </th>
                            <th>
                                <input type="checkbox" class="col-enabled[]" checked value="3"> Cena [zł]
                            </th>
                            <th>
                                <input type="checkbox" class="col-enabled[]" checked value="4"> Ilość
                            </th>
                            <th width="10%"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($data['orderItems'] as $item): ?>
                            <tr class="zwitem hidden" data-oitem-id="<?= $item['oitem_id'] ?>">
                                <td>
                                    <input type="checkbox" name="oitems[]" value="<?= $item['oitem_id'] ?>" style="display: none"/>
                                    <?= $item['src'] ?>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="oitem-name-<?= $item['oitem_id'] ?>"
                                           value=""/>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="oitem-price-<?= $item['oitem_id'] ?>"
                                           value="<?= $item['price'] ?>"/>
                                </td>
                                <td>
                                    <input type="text" class="form-control"
                                           name="oitem-quantity-<?= $item['oitem_id'] ?>"
                                           value="<?= $item['stored'] ?>">
                                </td>
                                <td>
                                    <a class="btn btn-danger btn-sm remove-zw-item">Usuń</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="portlet box green-dark">
                <div class="portlet-title">
                    <div class="caption">
                        Twoary zamówienie
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>Serial</th>
                            <th>Kod</th>
                            <th>Cena [zł]</th>
                            <th>Na magazynie</th>
                            <th width="10%">
                                <a class="btn btn-info btn-sm" id="show-all">Wszystkie</a>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($data['orderItems'] as $item): ?>
                            <tr class="oitem" data-oitem-id="<?= $item['oitem_id'] ?>">
                                <td>
                                    <?= $item['code'] ?>
                                </td>
                                <td>
                                    <?= $item['src'] ?>
                                </td>
                                <td>
                                    <?= $item['price'] ?>
                                </td>
                                <td>
                                    <?= $item['stored'] ?>
                                </td>
                                <td>
                                    <a class="btn btn-success choose-item">Dodaj</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-2 pull-right">
            <button type="submit" class="btn btn-success">Zapisz</button>
        </div>
    </div>
</form>

<script type="text/javascript">
    let oitems = [];

    function adZwItem(oitemId) {
        $('tr.oitem[data-oitem-id="' + oitemId + '"]').addClass('hidden');
        oitems[oitemId] = true;
        let $zwitem = $('tr.zwitem[data-oitem-id="' + oitemId + '"]');
        $zwitem.removeClass('hidden');
        $zwitem.find('input[name="oitems[]"]').prop('checked', true);
    }

    function hideZwItem(oitemId) {
        $('tr.oitem[data-oitem-id="' + oitemId + '"]').removeClass('hidden');
        oitems[oitemId] = false;
        let $zwitem = $('tr.zwitem[data-oitem-id="' + oitemId + '"]');
        $zwitem.addClass('hidden');
        $zwitem.find('input[name="oitems[]"]').prop('checked', false);
    }

    $('a.choose-item').on('click', function () {
        let $row = $(this).closest('tr');
        let oitemId = $row.data('oitem-id');
        adZwItem(oitemId);
    });

    $('a.remove-zw-item').on('click', function () {
        let $row = $(this).closest('tr');
        let oitemId = $row.data('oitem-id');
        hideZwItem(oitemId);
    });

    $('#show-all').on('click', function () {
        $('tr.oitem').each(function () {
            if (!$(this).hasClass('hidden')) {
                let oitemId = $(this).data('oitem-id');
                adZwItem(oitemId);
            }
        });
    });

    $('#wz-form').on('submit', function(e) {
        e.preventDefault();

        let data = $(this).serialize();

        App.blockUI();
        $.ajax({
            url: '/engine/wz.php?action=create',
            method: 'POST',
            data: data
        }).always(function () {
            App.unblockUI();
        });
    });
</script>