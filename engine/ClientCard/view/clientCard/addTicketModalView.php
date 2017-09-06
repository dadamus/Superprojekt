<?php
$mode = "insert";
$ticketData = [];

if (isset($data["mode"])) {
    if ($data["mode"] == "edit") {
        $mode = "edit";
        $ticketData = $data["ticketData"];
    }
}
?>
<div class="modal fade" role="dialog" id="newTicketModal">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"><?= ($mode == "insert" ? 'Nowy ticket' : 'Edytuj ticket') ?></h4>
        </div>
        <form class="form-horizontal" role="form" id="ticket-form">
            <div class="modal-body">
                <div class="form-body">
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="realization-date">Termin realizacji</label>
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="realization-date" id="realization-date"
                                   value="<?= (isset($ticketData["deadline"]) ? $ticketData["deadline"] : "") ?>">
                        </div>
                        <div class="col-md-3">
                            <?php
                            $checked = '';

                            if (isset($ticketData["deadline_on"])) {
                                if ($ticketData["deadline_on"] == 1) {
                                    $checked = 'checked';
                                }
                            }
                            ?>
                            <input type="checkbox" name="realization-date-checkbox" class="make-switch"
                                   data-on-text="<i class='fa fa-check'></i>"
                                <?= $checked ?>
                                   data-size="small" data-off-text="<i class='fa fa-times'></i>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="priority">Pilność</label>
                        <div class="col-md-8">
                            <select name="priority" id="priority" class="form-control">
                                <?php
                                $selected[2] = "selected";

                                if (isset($ticketData["priority"])) {
                                    unset($selected[2]);
                                    $selected[$ticketData["priority"]] = "selected";
                                }
                                ?>
                                <option value="1" <?= (isset($selected[1]) ? $selected[1] : '') ?>>Niska</option>
                                <option value="2" <?= (isset($selected[2]) ? $selected[2] : '') ?>>Normalna</option>
                                <option value="3" <?= (isset($selected[3]) ? $selected[3] : '') ?>>Wysoka</option>
                                <option value="4" <?= (isset($selected[4]) ? $selected[4] : '') ?>>Bardzo wysoka
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <?php if ($mode == "insert"): ?>
                    <button type="button" class="btn btn-success" id="new-ticket-submit" data-dismiss="modal"
                            data-client-id="<?= $data["clientId"] ?>">Dodaj
                    </button>
                <?php else: ?>
                    <button type="button" class="btn btn-success" id="edit-ticket-submit" data-dismiss="modal"
                            data-ticket-id="<?= $ticketData["id"] ?>">Zapisz
                    </button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $(".make-switch").bootstrapSwitch();

        $("#realization-date").datetimepicker({
            minView: 2,
            language: 'pl',
            format: "yyyy-mm-dd"
        });

        $("#new-ticket-submit").on("click", function () {
            App.blockUI({boxed: !0});

            var clientId = $(this).data("client-id");

            $.ajax({
                method: "POST",
                data: $(this).parent().parent().serialize(),
                url: "/engine/ClientCardRouter.php?action=addNewTicket&client_id=" + clientId
            }).done(function (response) {
                App.unblockUI();
                swal("Dodałem", "Ticket został dodany! Jego id: " + response, "success");
            });
        });

        $("#edit-ticket-submit").on("click", function () {
            App.blockUI({boxed: !0});

            var ticketId = $(this).data("ticket-id");
            $.ajax({
                method: "POST",
                data: $(this).parent().parent().serialize(),
                url: "/engine/ClientCardRouter.php?action=saveTicket&ticket_id=" + ticketId
            }).done(function (response) {
                App.unblockUI();
                swal("Dodałem", "Ticket został zapisany!", "success");
            });
        });
    });
</script>