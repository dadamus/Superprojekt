<?php
/** @var WZObject $wz */
$wz = $data['wz'];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>WZ</title>
    <style>
        h1 {
            font-weight: bold;
        }
        #header {
            width: 100%;
            padding-top: 20px;
        }

        .row {
            clear: both;
            margin: 10px;
        }

        .col-lg-3 {
            width: 18%;
            margin: 2%;
            padding: 1%;
            float: left;
        }

        .col-lg-6 {
            width: 48%;
            padding: 1%;
            float: left;
        }

        .col-lg-12 {
            width: 98%;
            padding: 1%;
            float: left;
        }
        .col-header {
            width: 100%;
            font-size: xx-large;
            font-weight: bold;
            border-bottom: 1px black solid;
            clear: both;
        }
        .to-right {
            text-align: right;
        }

        p {
            margin: 0;
        }

        th {
            font-weight: bold;
        }

        th, td {
            padding: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #848484;
        }

        .text-center {
            text-align: center;
        }

        .dashed {
            border-top: 1px dashed #848484;
        }
    </style>
</head>
<body>
<div id="header" class="row">
    <div class="col-lg-6">
        <h1>
            WZ oryginał nr:
            <b><?= $wz->getName() ?></b>
        </h1>
    </div>
    <div class="col-lg-6 to-right">
        <p>
            Miejsce wystawienia: <b>Szczyrzyc</b>
        </p>
        <p>
            Data wystawienia: <b><?= $wz->getCreateDate() ?></b>
        </p>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        <?php
        $seller = $wz->getSellerAddress();
        ?>
        <div class="col-header">
            Sprzedawca
        </div>
        <p>
            <b><?= $seller->getName() ?></b>
        </p>
        <p>
            <b><?= $seller->getAddress1() ?></b>
        </p>
        <p>
            <b><?= $seller->getAddress2() ?></b>
        </p>
        <p>
            <b><?= $seller->getNip() ?></b>
        </p>
    </div>
    <div class="col-lg-6">
        <?php
        $buyer = $wz->getBuyerAddress();
        ?>
        <div class="col-header">
            Nabywca
        </div>
        <p>
            <b><?= $buyer->getName() ?></b>
        </p>
        <p>
            <b><?= $buyer->getAddress1() ?></b>
        </p>
        <p>
            <b><?= $buyer->getAddress2() ?></b>
        </p>
        <p>
            <b><?= $buyer->getNip() ?></b>
        </p>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <table>
            <thead>
            <tr>
                <th>Lp</th>
                <th>Nazwa towaru/usługi</th>
                <th>Ilość</th>
                <th>Jm</th>
                <th>Uwagi</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $lp = 1;
            ?>
            <?php foreach ($wz->getItems() as $item): ?>
            <tr>
                <td><?= $lp ?></td>
                <td><?= $item->getName() ?></td>
                <td class="to-right"><?= $item->getQuantity()?></td>
                <td class="to-right">Szt</td>
                <td></td>
            </tr>
                <?php $lp++ ?>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="row" style="padding-left: 18%; margin-top: 150px">
    <div class="col-lg-3 text-center dashed">
        Towar odebrał
    </div>
    <div class="col-lg-3 text-center dashed">
        Towar wydał
    </div>
    <div class="col-lg-3 text-center dashed">
        Zatwierdził
    </div>
</div>
<script type="text/javascript">
    window.print();
</script>
</body>
</html>