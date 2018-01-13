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
$passQuery = $connector->query("SELECT `value` FROM app_settings WHERE name = 'pass'");
$passData = $passQuery->fetch();
$pass = $passData['value'];

if (isset($_POST['pass'])) {
    if ($pass === $_POST['pass']) {
        $_SESSION['pass'] = $pass;
    }
}
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
            <?php
            $passCheck = false;

            if (isset($_SESSION['pass'])) {
                if ($_SESSION['pass'] === $pass) {
                    $passCheck = true;
                }
            }
            ?>

            <?php if ($passCheck): ?>
                <?php if (isset($_POST['date-from']) || isset($_POST['date-to'])): ?>
                    <?php
                    $from = @$_POST['date-from'] . " 00:00:00";
                    $to = @$_POST['date-to'] . " 24:00:00";

                    if ($from === null) {
                        $from = date("Y-m-d H:i:s");
                    }

                    if ($to === null) {
                        $to = date("Y-m-d H:i:s");
                    }

                    $historyQuery = $connector->prepare("
                            SELECT
                            h.operation,
                            h.param,
                            d.name,
                            d.id as detail_id
                            FROM
                            app_history h
                            LEFT JOIN app_details d ON d.id = h.detail_id
                            WHERE
                            h.log_date >= :dfrom
                            and h.log_date <= :dto
                        ");
                    $historyQuery->bindValue(':dfrom', $from, PDO::PARAM_STR);
                    $historyQuery->bindValue(':dto', $to, PDO::PARAM_STR);
                    $historyQuery->execute();

                    $history = [];
                    while ($row = $historyQuery->fetch()) {
                        if (!isset($history[$row['detail_id']])) {
                            $history[$row['detail_id']] = [
                                'name' => $row['name'],
                                'download' => 0,
                                'upload' => 0
                            ];
                        }

                        switch ($row['operation']) {
                            case HistoryService::DOWNLOAD_TYPE:
                                $history[$row['detail_id']]['download'] += $row['param'];
                                break;

                            case HistoryService::UPLOAD_TYPE:
                                $history[$row['detail_id']]['upload'] += $row['param'];
                                break;
                        }
                    }
                    ?>
                    <?php foreach ($history as $row): ?>
                        <h3><?= $row['name'] ?></h3>
                        <ul>
                            <li>Przyjęcia: <b><?= $row['upload'] ?></b></li>
                            <li>Pobrania: <b><?= $row['download'] ?></b></li>
                            <li>Bilans: <b><?= ($row['upload'] - $row['download']) ?></b></li>
                        </ul>
                        <hr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="row">
                        <div class="col-lg-5" style="text-align: right">
                            <h3>Zakres dat:</h3>
                        </div>
                        <div class="col-lg-7">
                            <form action="?" method="post">
                                <input type="text" name="date-from" class="cdatepicker" id="date-from" placeholder="Od">
                                <input type="text" name="date-to" class="cdatepicker" id="date-to" placeholder="Do">
                                <button type="submit" class="btn btn-success">Generuj</button>
                            </form>
                        </div>
                    </div>
                <?php endif ?>
            <?php else: ?>
                <div class="row">
                    <div class="col-lg-5" style="text-align: right">
                        <h3>Wpisz hasło:</h3>
                    </div>
                    <div class="col-lg-7">
                        <form action="?" method="post">
                            <input type="password" style="width: 200px; float: left;" class="form-control" name="pass">
                            <button class="btn" type="submit">Loguj</button>
                        </form>
                    </div>
                </div>
            <?php endif ?>
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
        $('.cdatepicker').datepicker({
            dateFormat: 'yy-mm-dd'
        });

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
