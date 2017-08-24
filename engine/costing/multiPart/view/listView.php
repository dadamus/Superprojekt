<div class="row">
    <div class="col-lg-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    Multipart
                </div>
            </div>
            <div class="portlet-body">
                <table class="table table-striped table-bordered table-hover table-checkable order-column" id="multipart">
                    <thead>
                    <tr>
                        <th>Nazwa</th>
                        <th>Stworzony</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($data["rows"] as $row): ?>
                    <tr>
                        <td><?= $row["dir_name"] ?></td>
                        <td><?= $row["created_at"] ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#multipart').DataTable();
    });
</script>