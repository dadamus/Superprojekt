<?php
$client = $data["data"];
?>

    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-title">Klient <?= $client["name"] ?></h2>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="portlet box blue-dark">
                <div class="portlet-title">
                    <div class="caption">
                        Informacje
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nazwa</th>
                                <th>NIP</th>
                                <th>Typ</th>
                                <th>Telefon</th>
                                <th>Adres</th>
                                <th>Email</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><?= $client["id"] ?></td>
                                <td><?= $client["name"] ?></td>
                                <td><?= $client["nip"] ?></td>
                                <td><?= ($client["type"] == 1 ? "F" : "O") ?></td>
                                <td><?= $client["phone"] ?></td>
                                <td><?= $client["address"] ?></td>
                                <td><?= $client["email"] ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="portlet box yellow-casablanca">
                <div class="portlet-title">
                    <div class="caption">
                        Tickety
                    </div>
                    <div class="actions">
                        <a href="#newTicketModal" class="btn btn-sm dark" data-toggle="modal"
                           data-target="#newTicketModal">
                            Dodaj <i class="fa fa-plus" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Termin</th>
                                <th>Ważność</th>
                                <th>Data dodania</th>
                                <th>Dodane przez</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($data["tickets"] as $ticket): ?>
                                <tr>
                                    <td>#<?= $ticket["id"] ?></td>
                                    <td><?= $ticket["deadline"] ?></td>
                                    <td>
                                        <?php
                                        $status = $text = "";
                                        switch ($ticket["priority"]) {
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
                                        <span class="label label-<?= $status ?>"><?= $text ?></span>
                                    </td>
                                    <td><?= $ticket["created_at"] ?></td>
                                    <td><?= $ticket["user_name"] ?></td>
                                    <td>
                                        <a data-url="/engine/ClientCardRouter.php?action=editTicket&ticket_id=<?= $ticket["id"] ?>"
                                           data-toggle="modal"
                                           class="ajax-modal">
                                            <i class="fa fa-pencil"></i>
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
    </div>
    <div id="ajax-modal" tabindex="-1"></div>
    <script type="text/javascript">
        $(".ajax-modal").on("click", function () {
            $("body").modalmanager("loading");
            var d = $(this);
            var a = $("#ajax-modal");
            setTimeout(function () {
                a.load(d.attr("data-url"), "", function () {
                    a.find(".modal").modal("show");
                })
            }, 1e3)
        });
    </script>

<?= $data["modal"] ?>