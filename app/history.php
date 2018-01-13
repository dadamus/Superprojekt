<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 14.11.2017
 * Time: 18:01
 */

require_once __DIR__ . '/config.php';

$did = $_GET['did'];

$connector = DBConnector::connect();
$data = $connector->query("SELECT * FROM app_history WHERE detail_id = $did");
$detailDataQuery= $connector->query("SELECT * FROM app_details WHERE id = $did");
$detailData = $detailDataQuery->fetch();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="vendor/toastr.min.css">
    <link rel="stylesheet" href="vendor/jquery-ui/jquery-ui.min.css">
    <title>ABL - Hisotria</title>
    <style>
        .hidden {
            display: none;
        }

        .page-loader {
            width: 100%;
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 10000000000;
            background-color: white;
            opacity: 0.9;
        }

        .loader {
            margin: 20% auto;
            border: 16px solid #f3f3f3;
            border-radius: 50%;
            border-top: 16px solid #3498db;
            width: 120px;
            height: 120px;
            -webkit-animation: spin 2s linear infinite;
            animation: spin 2s linear infinite;
        }

        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<body>
<div class="page-loader hidden">
    <div class="loader"></div>
</div>
<div class="container-fluid" style="padding-top: 50px">
    <div class="row" style="margin-top: 20px">
        <div class="col-lg-2 col-sm-0"></div>
        <div class="col-lg-8 col-sm-12">
            <div class="row">
                <div class="col-lg-10">
                    <h1><?= $detailData['name'] ?></h1>
                </div>
                <div class="col-lg-2">
                    <a href="<?= $root ?>" class="btn btn-default">Powrót</a>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <h3>Bilans: <?= $detailData['quantity'] ?></h3>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <input type="text" name="date-from" class="cdatepicker" id="date-from" placeholder="Od">
                    <input type="text" name="date-to" class="cdatepicker" id="date-to" placeholder="Do">
                    <select name="type" id="type">
                        <option value="" selected>Wszystkie typy</option>
                        <option>Pobranie</option>
                        <option>Dostawa</option>
                    </select>
                </div>
            </div>
            <div class="table">
                <table id="table">
                    <thead>
                    <tr>
                        <th>Sztuk</th>
                        <th>Data</th>
                        <th>Akcja</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <td>
                                <?= $row['param'] ?>
                            </td>
                            <td>
                                <?= $row['log_date'] ?>
                            </td>
                            <td>
                                <?php if (HistoryService::DOWNLOAD_TYPE == $row['operation']): ?>
                                    Pobranie
                                <?php else: ?>
                                    Dostawa
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12" style="text-align: center">
            <img src="<?= $root ?>barcode-viewer.php?n=<?= makeId($detailData['id']) ?>" width="300px">
        </div>
    </div>
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="vendor/jquery-ui/jquery-ui.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js"
        integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ"
        crossorigin="anonymous"></script>
<script src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="vendor/toastr.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $.fn.dataTable.ext.search.push(
            function (settings, data, dataIndex) {
                var min = new Date($('#date-from').val()).getTime();
                var max = new Date($('#date-to').val()).getTime();

                var date = new Date(data[1]).getTime();

                if (( isNaN(min) && isNaN(max) ) ||
                    ( isNaN(min) && date <= max ) ||
                    ( min <= date && isNaN(max) ) ||
                    ( min <= date && date <= max )) {
                    return true;
                }
                return false;
            }
        );

        $('.cdatepicker').datepicker({
            dateFormat: 'yy-mm-dd'
        });
        let $table = $('#table');

        let table = $table.DataTable();
        let $pageLoader = $(".page-loader");

        let showLoader = function () {
            $pageLoader.removeClass('hidden');
        };

        let hideLoader = function () {
            $pageLoader.addClass('hidden');
        };

        $("#type").on('change', function () {
            table.column(2).search($(this).val()).draw();
        });

        $('#date-from, #date-to').change(function () {
            table.draw();
        });
    });
</script>
</body>
</html>
