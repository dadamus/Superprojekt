<?php
if (@$_GET["a"] == 1) { // AJAX New client
    header("Access-Control-Allow-Origin: *");
    header('Content-type: application/json');
    require_once '../config.php';
    require_once 'protect.php';

    $ds = DIRECTORY_SEPARATOR;
    $pid = $_COOKIE["plProjectId"];
    if (!empty($_FILES)) {
        $srcfile = $_FILES['file']['name'];
        $date = date("Y-m-d H:i:s");
        $query = $db->prepare("INSERT INTO `details` (`pid`, `src`, `date`) VALUES ('$pid', '$srcfile', '$date')");
        $query->execute();
        
        $did = $db->lastInsertId();
        insertStatus($did, $STATUS_NEW);
    }

    die(json_encode("success"));
}
?>
<div class="row">
    <div class="col-lg-12">
        <h2 class="page-title">File Menager</h2>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="progress">
            <div id="progress" class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 10%;"></div>
        </div>
    </div>
</div>
<div class="row" id="cClients">
    <div class="col-lg-3">
        <div class="widget">
            <div class="widget-header"> <i class="icon-search"></i>
                <h3>Wyszukaj firmę</h3>
            </div>
            <div class="widget-content">
                <form id="sform">
                    <input type="search" placeholder="ID lub nazwa" class="form-control" id="search-input" name="scontent">
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-9">
        <div class="widget">
            <div class="widget-header"> <i class="icon-book"></i>
                <h3>Lista firmy</h3>
            </div>
            <div class="widget-content">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>Nazwa</td>
                            <td>Typ</td>
                        </tr>
                    </thead>
                    <tbody id="clist">
                        <tr><td>Brak wyników</td><td></td><td></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row" id="cProjects" style="display: none;">
    <div class="col-lg-3">
        <div class="widget">
            <div class="widget-content" style="text-align: center;">
                <a href="#" id="aClientsList" data-toggle="modal" class="btn btn-success">Lista klientów</a>
            </div>
        </div>
    </div>
    <div class="col-lg-9">
        <div class="widget">
            <div class="widget-header"> <i class="icon-copy"></i>
                <h3>Projekty</h3>
            </div>
            <div class="widget-content" id="pcontent"></div>
        </div>
    </div>
</div>
<div class="row" id="cUpload" style="display: none">
    <div class="col-lg-3">
        <div class="widget">
            <div class="widget-content" style="text-align: center;">
                <a href="#" id="aProjectsList" data-toggle="modal" class="btn btn-success">Lista projektów</a>
            </div>
        </div>
    </div>
    <div class="col-lg-9">
        <div class="widget">
            <div class="widget-header"> <i class="icon-folder-open-alt"></i>
                <h3>Uploader</h3>
            </div>
            <div class="widget-content">
                <div class="panel-body">
                    <div id="dropzone">
                        <form action="<?php echo $site_path; ?>/engine/filemenager.php?a=1" class="dropzone dropzone-file-area dz-clickable" id="my-dropzone">
                        </form>
                    </div>
                </div>
            </div>
        </div> 
    </div>
</div>

<script src="/javascript/dropzone.js"></script> 
<script type="text/javascript">
    var files = 0;
    Dropzone.options.myDropzone = {
        init: function () {
            var self = this;
            // Send file starts
            self.on("addedfile", function () {
                files++;
                $("#progress").animate({width: "75%"}, "fast");
            });

            // File upload Progress
            self.on("success", function () {
                $("#progress").animate({width: "100%"}, "fast");
                files--;
                if (files <= 0) {
                    this.removeAllFiles();
                }
            });
        }
    };

    function search() {
        if ($("#sform input").val() !== "") {
            $.ajax({
                method: "POST",
                url: "<?php echo $site_path; ?>/engine/projectbase.php?a=1",
                data: $("#sform").serialize()
            }).done(function (msg) {
                $("#clist").html(msg);
            });
        }
    }
    function pList(_id) {
        $("#cClients").fadeOut("fast", function () {
            $("#progress").animate({width: "25%"}, "fast");
            $("#cProjects").fadeIn();
        });
        $.ajax({
            url: "<?php echo $site_path; ?>/engine/projectbase.php?a=2&name=" + _id
        }).done(function (msg) {
            Cookies.set("plClientName", _id);
            $("#pcontent").html('<table cellpadding="0" cellspacing="0" border="0" id="plist" class="display"><thead><tr><td>Nr</td><td>Data</td><td>Nazwa</td><td>Zawartość</td></tr></thead><tbody id="plistc"></tbody></table>');
            $("#plistc").html(msg);
            table = $('#plist').dataTable({
                "sPaginationType": "full_numbers"
            });
        });
    }

    $(document).ready(function () {
        $("#clist").on("click", "tr", function () {
            var _id = $(this).attr("id");
            pList(_id);
        });
        $("#pcontent").on("click", "#plist tbody tr", function () {
            var _id = parseInt($(this).attr("id"));
            Cookies.set("plProjectId", _id);
            $("#cProjects").fadeOut("fast", function () {
                $("#progress").animate({width: "50%"}, "fast");
                $("#cUpload").fadeIn();
            });
        });
        $("#search-input").keyup(function () {
            search();
        });
        $("#aClientsList").on("click", function () {
            $("#cProjects").fadeOut("fast", function () {
                $("#progress").stop().animate({width: "10%"}, "fast");
                $("#cClients").fadeIn();
            });
        });
        $("#aProjectsList").on("click", function () {
            $("#cUpload").fadeOut("fast", function () {
                $("#progress").stop().animate({width: "25%"}, "fast");
                $("#cProjects").fadeIn();
            });
        });
    });
    $("#sform").submit(function (event) {
        search();
        event.preventDefault();
    });
</script>