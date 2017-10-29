<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 07.10.2017
 * Time: 10:39
 */

$clientData = $data['clientData'];
$address = explode(',', $clientData['address']);
if (count($address) > 1) {
    if (strlen($address[0]) > 1 || strlen($address[1]) > 1) {
        $clientData['address'] = $address;
    } else {
        $clientData['address'] = [null, null];
    }
} else {
    $clientData['address'] = [null, null];
}

$defaultSeller = $data['defaultSeller'];
?>
<form class="form" id="wz-form" method="POST" action="/engine/wz.php?action=generate">
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
                            <input type="text" name="seller_default_id" data-default-id="<?= $defaultSeller['id'] ?>" value="<?= $defaultSeller['id'] ?>" style="display: none">
                            <input class="form-control seller" name="seller_name" placeholder="Nazwa" value="<?= $defaultSeller['address_name'] ?>"/>
                        </div>
                        <div class="form-group col-md-12">
                            <input class="form-control seller" name="seller_address1" placeholder="Adres" value="<?= $defaultSeller['address1'] ?>"/>
                        </div>
                        <div class="form-group col-md-12">
                            <input class="form-control seller" name="seller_address2" placeholder="Adres" value="<?= $defaultSeller['address2'] ?>"/>
                        </div>
                        <div class="form-group col-md-12">
                            <input class="form-control seller" name="seller_nip" placeholder="Nip" value="<?= $defaultSeller['nip'] ?>"/>
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
                                <input type="checkbox" name="col-enabled[]" class="col-enabled[]" checked value="code"> Kod
                            </th>
                            <th>
                                <input type="checkbox" name="col-enabled[]" class="col-enabled[]"  checked value="name"> Nazwa
                            </th>
                            <th>
                                <input type="checkbox" name="col-enabled[]" class="col-enabled[]"  checked value="price"> Cena [zł]
                            </th>
                            <th>
                                <input type="checkbox" name="col-enabled[]" class="col-enabled[]" checked value="quantity"> Ilość
                            </th>
                            <th width="10%"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($data['orderItems'] as $item): ?>
                            <tr class="zwitem hidden" data-oitem-id="<?= $item['oitem_id'] ?>">
                                <td>
                                    <input type="checkbox" name="oitems[]" value="<?= $item['oitem_id'] ?>"
                                           style="display: none"/>
                                    <input type="text" style="display: none" name="oitem-code-<?= $item['oitem_id'] ?>" value="<?= $item['src'] ?>"/>
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
                    <div class="row">
                        <div class="col-lg-2 pull-right">
                            <button type="submit" class="btn btn-success">Generuj</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
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

    $('input.seller').on('change', function() {
        $('input[name="seller_default_id"]').val(0);
    });

    $('#wz-form').on('submit', function (e) {
        e.preventDefault();

        let data = $(this).serialize();

        App.blockUI();
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: data
        }).always(function () {
            App.unblockUI();
        }).done(function(response) {
            location.href = '/wz/' + response + "/";
        });
    });
</script>