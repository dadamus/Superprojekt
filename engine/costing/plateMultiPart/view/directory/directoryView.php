<div class="row">
    <div class="col-lg-12">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="ID/MIESIĄC/ROK" id="multiDirectoryInput" value="<?= $data["folderName"] ?>"/>
            <span class="input-group-btn">
                <button class="btn green" type="button" disabled="disabled" id="addNewMultiDirectory">
                    <i class="fa fa-plus-circle"></i>
                </button>
            </span>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>Nazwa</th>
            </tr>
            </thead>
            <tbody id="multiDirectoryViewContainer">

            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript" src="/js/plateMultiPartForm/directoryView.js"></script>