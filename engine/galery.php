<?php
require_once dirname(__FILE__) . '/../config.php';
$action = @$_GET["a"];
if (!empty($action)) {
    require_once dirname(__FILE__) . "/../config.php";
    require_once dirname(__FILE__) . "/protect.php";
    require_once dirname(__FILE__) . "/class/resize.php";
}
$pid = $_GET["pid"];
$qname = $db->query("SELECT `name` FROM `projects` WHERE `id` = '$pid'");
$fqname = $qname->fetch();
$pname = $fqname["name"];

if (@$_GET["a"] == 1) { // AJAX New client
    $storeFolder = $data_src . 'detale/img/' . date("m-Y") . "/";
    $ds = DIRECTORY_SEPARATOR;

    //Get cid
    $qcid = $db->prepare("SELECT `cid` FROM `projects` WHERE `id` = :id");
    $qcid->bindValue(":id", $pid, PDO::PARAM_INT);
    $qcid->execute();
    $fcid = $qcid->fetch();
    $cid = $fcid["cid"];

    //Upload folder exists?
    if (file_exists($storeFolder) == false) {
        make_dir($storeFolder, 0777, true);
        make_dir($storeFolder . "mini/");
    }
    if (!empty($_FILES)) {
        $tempfile = $_FILES["file"]["tmp_name"];
        $ext = pathinfo(basename($_FILES["file"]["name"]), PATHINFO_EXTENSION);

        //File is image?
        if (getimagesize($tempfile) === false) {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-type: text/plain');
            die('{"error": "nie obrazek"}');
        }

        $udate = date("Y-m-d H:i:s");

        $dgquery = $db->prepare("INSERT INTO `pgalery` (`cid`, `pid`, `update`) VALUES (:cid, :pid, :update)");
        $dgquery->bindValue(":cid", $cid, PDO::PARAM_INT);
        $dgquery->bindValue(":pid", $pid, PDO::PARAM_INT);
        $dgquery->bindValue(":update", $udate, PDO::PARAM_STR);
        $dgquery->execute();

        //Get image id
        $iid = $db->lastInsertId();

        //Move full image
        $fullimage = $storeFolder . $iid . "." . $ext;
        $miniimage = $storeFolder . "mini/" . $iid . "." . $ext;

        move_uploaded_file($tempfile, $fullimage);

        //Change size
        $imagesizex = 150;
        $imagesizey = 150;
        $resize = new resize($fullimage);
        $resize->resizeImage($imagesizex, $imagesizey);
        $resize->saveImage($miniimage);

        //EXIF data
        $exif = @exif_read_data($fullimage);
        $pdate = $exif["DateTime"];

        $uquery = $db->prepare("UPDATE `pgalery` SET `pdate` = :pdate, `src` = :src WHERE `id` = :id");
        $uquery->bindValue(":pdate", $pdate, PDO::PARAM_STR);
        $uquery->bindValue(":src", $fullimage, PDO::PARAM_STR);
        $uquery->bindValue(":id", $iid, PDO::PARAM_INT);
        $uquery->execute();
    }

    die(json_encode('{"success": "cid: ' . $cid . ', pid: ' . $pid . '"}'));
}
?>
<div class="row">
    <div class="col-lg-12">
        <h2 class="page-title">Galeria <small><?php echo $pname; ?></small></h2>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-lg-12">
                <div class="widget">
                    <div id="accordion" class="panel-group">
                        <div class="panel">
                            <div class="panel-heading">
                                <a href="#collapseone" data-toggle="collapse" class="accordion-toggle">Dodaj zdjęcie</a>
                            </div>
                            <div class="panel-collapse collapse" id="collapseone" style="height: 0px;">
                                <div class="panel-body">
                                    <div id="dropzone">
                                        <form action="<?php echo $site_path; ?>/engine/galery.php?a=1&pid=<?php echo $pid; ?>" class="dropzone dropzone-file-area dz-clickable" id="my-dropzone">
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="widget">
                    <div class="widget-header">
                        <h3>Galeria</h3>
                    </div>
                    <div class="widget-content">
                        <div id="examples" class="section examples-section">
                            <?php
                            $q = $db->prepare("SELECT * FROM `pgalery` WHERE `pid` = :pid");
                            $q->bindValue(":pid", $pid, PDO::PARAM_INT);
                            $q->execute();
                            foreach ($q as $row) {
                                $pdate = $row["pdate"];
                                $tsrc = explode("/", $row["src"]);
                                $src = "";
                                $msrc = "";
                                for ($i = 4; $i < count($tsrc); $i++) {
                                    $src .= "/" . $tsrc[$i];
                                    if ($i == 8) {
                                        $msrc .= "/mini/" . $tsrc[$i];
                                    } else {
                                        $msrc .= "/" . $tsrc[$i];
                                    }
                                }

                                $msrc = str_replace('data', 'DATA', $msrc);

                                echo '<div class="col-sm-4 col-md-2">
                                    <div class="image-row">
                                    <div class="image-set"> <a href="' . str_replace('data', 'DATA', $src) . '" class="lightbox" title="' . $pdate . '"> <img class="example-image" src="' . $msrc . '" alt="detail" width="150" height="150"/></a> </div>
                                    </div>
                                    </div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $(".widget").on("click", ".lightbox", function (e) {
            e.preventDefault();
            var img = $(this).attr("href");
            if ($("#lightbox").length > 0) {
                $("#lightbox_img").attr('src', img);
                $("#lightbox").fadeIn("fast");
            } else {
                $("body").append('<div id="lightbox"><div id="lightbox_close"></div><img id="lightbox_img" src="' + img + '" alt="obrazek"/></div>');
            }
        });
        $("body").on("click", "#lightbox_close", function () {
            $("#lightbox").fadeOut("fast");
        });
    });

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
            this.on("success", function (file, responseText) {
                console.log(responseText);
            });
        }
    };
</script>