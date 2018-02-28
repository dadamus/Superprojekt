<div class="row">
    <div class="col-lg-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    Multipart
                </div>
            </div>
            <div class="portlet-body">
                <table class="table table-striped table-bordered table-hover table-checkable order-column"
                       id="multipart">
                    <thead>
                    <tr>
                        <th>Nazwa</th>
                        <th>Stworzony</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($data["rows"] as $row): ?>
                        <tr>
                            <td><?= $row["dir_name"] ?></td>
                            <td><?= $row["created_at"] ?></td>
                            <td>
                                <?php
                                $status = "";
                                $text = "";
                                $multiType = '';

                                switch ($row["type"]) {
                                    case OT::AUTO_WYCENA_BLACH_MULTI_KROK_1:
                                        $text = 'Brak wyceny';
                                        $status = 'info';
                                        $multiType = "plate";
                                        break;

                                    case OT::AUTO_WYCENA_BLACH_MULTI_KROK_2:
                                        $text = 'Brak ramki';
                                        $status = 'warning';
                                        $multiType = "plate";
                                        break;
                                    default:
                                        $text = "Brak detali";
                                        $status = 'default';
                                        $multiType = "none";
                                        break;
                                }

                                //Dla multipartu blach wszystkie statusy
                                switch ($row["type"]) {
                                    case OT::AUTO_WYCENA_BLACH_MULTI_KROK_1:
                                    case OT::AUTO_WYCENA_BLACH_MULTI_KROK_2:
                                    case OT::AUTO_WYCENA_BLACH_MULTI_ZABLOKOWANE:
                                    case OT::AUTO_WYCENA_BLACH_MULTI_ZATWIERDZONA:
                                    case OT::AUTO_WYCENA_BLACH_MULTI_ANULOWANA:
                                    case OT::AUTO_WYCENA_BLACH_MULTI_DODANE_DO_ZAMOWIENIA:
                                        $multiType = "plate";
                                        break;
                                }

                                if ($row["price"] > 0) {
                                    $text = "Wycenione";
                                    $status = "success";
                                }
                                ?>
                                <span class="label label-<?= $status ?>"><?= $text ?></span>
                            </td>
                            <td>
                                <?php if ($status == "success"): ?>
                                    <?php if ($multiType == "plate"): ?>
                                        <a href="/plateMulti/<?= $row["id"] ?>/">
                                            <i class="fa fa-sign-in fa-2x"></i>
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php if ($multiType != "none"): ?>
                                        <a href="/multipart/<?= $multiType ?>/<?= $row["id"] ?>/">
                                            <i class="fa fa-sign-in fa-2x"></i>
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
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
        $('#multipart').DataTable();
    });
</script>