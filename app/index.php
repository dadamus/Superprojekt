<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 14.11.2017
 * Time: 18:01
 */

require_once __DIR__ . '/config.php';

$details = DBConnector::connect()->query("SELECT * FROM app_details");
$data = $details->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="vendor/toastr.min.css">
    <title>ABL</title>
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
    <div class="row" style="margin-bottom: 10px;">
        <div class="col-lg-10" style="text-align: right;">
            <a href="<?= $root ?>raport.php" class="btn btn-success btn-sm">Raport</a>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-2 col-sm-0"></div>
        <div class="col-lg-8 col-sm-12">
            <div class="table">
                <table id="table">
                    <thead>
                    <tr>
                        <th style="width: 25%">ID</th>
                        <th style="width: 45%">Nazwa</th>
                        <th style="width: 30%">Button</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <td>
                                <?= makeId($row['id']) ?>
                            </td>
                            <td>
                                <?= $row['name'] ?>
                            </td>
                            <td data-row-id="<?= $row['id'] ?>" data-row-name="<?= $row['name'] ?>">
                                <a href="#" type="button" class="btn btn-sm btn-danger downloadAction">Pobierz</a>
                                <a href="#" type="button" class="btn btn-sm btn-success uploadAction">Dostawa</a>
                                <a href="<?= $root ?>history.php?did=<?= $row['id'] ?>" type="button"
                                   class="btn btn-sm btn-info hisotry">Historia</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form action="<?= $root ?>submit.php" id="modalForm">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>
                        <input type="text" name="name" class="form-control" readonly value="Nazwa"/>
                        <input type="text" name="id" hidden class="form-control"/>
                        <input type="text" name="action" hidden/>
                        <input type="number" name="quantity" class="form-control" value="0" style="margin-top: 10px"/>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Zapisz</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.19.2/moment.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js"
        integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ"
        crossorigin="anonymous"></script>
<script src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="vendor/toastr.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        let $modal = $("#myModal");
        let $pageLoader = $(".page-loader");

        let showLoader = function () {
            $pageLoader.removeClass('hidden');
        };

        let hideLoader = function () {
            $pageLoader.addClass('hidden');
        };

        let showModal = function (action, $container) {
            let rowId = $container.data('row-id');
            let rowName = $container.data('row-name');

            $modal.modal('show');
            $modal.find('input[name="name"]').val(rowName);
            $modal.find('input[name="id"]').val(rowId);
            $modal.find('input[name="action"]').val(action);
            $modal.find('input[name="quantity"]').val(0);
        };

        let $table = $('#table');

        $.fn.dataTable.ext.search.push(
            function( settings, data, dataIndex ) {
                var min = parseInt( $('#min').val(), 10 );
                var max = parseInt( $('#max').val(), 10 );
                var age = parseFloat( data[3] ) || 0; // use data for the age column

                if ( ( isNaN( min ) && isNaN( max ) ) ||
                    ( isNaN( min ) && age <= max ) ||
                    ( min <= age   && isNaN( max ) ) ||
                    ( min <= age   && age <= max ) )
                {
                    return true;
                }
                return false;
            }
        );

        $table.DataTable();
        $table.on('click', '.downloadAction', function (e) {
            e.preventDefault();

            let $container = $(this).parent();
            showModal('download', $container);
        }).on('click', '.uploadAction', function (e) {
            e.preventDefault();

            let $container = $(this).parent();
            showModal('upload', $container);
        });

        $("#modalForm").on('submit', function (e) {
            e.preventDefault();
            $modal.modal('hide');

            let data = $(this).serialize();
            showLoader();

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: data
            }).always(function () {
                hideLoader();
            }).done(function (response) {
                toastr.success(response);
            }).error(function () {
                toastr.danger("Wystapil blad!");
            });
        });
    });
</script>
</body>
</html>
