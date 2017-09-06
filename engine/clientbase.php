<?php
ob_start();
if (@$_GET["a"] != 0) {
    require_once '../config.php';
    require_once 'protect.php';
}
if (@$_GET["a"] == 1) { // AJAX New client
    $type = $_POST["type"];
    $name = str_replace('"', " ", $_POST["name"]);
    $nip = $_POST["nip"];
    $address1 = $_POST["address1"];
    $address2 = $_POST["address2"];
    $phone = $_POST["phone"];
    $person = $_POST["person"];
    $email = $_POST["email"];

    $address = $address1 . " " . $address2;
    $date = date("Y-m-d H:i:s");

    $query = $db->prepare("INSERT INTO `clients` (`nip`, `type`, `name`, `address`, `phone`, `person`, `email`, `date`) VALUES ('$nip', '$type', '$name', '$address', '$phone', '$person', '$email', '$date')");
    $query->execute();
    $_id = $db->lastInsertId();
    $directories = [];
    foreach (glob($data_src . "*", GLOB_ONLYDIR) as $directory) {
        $dirPath = explode("/", $directory);
        $dirName = end($dirPath);
        $directories[] = $dirName;
    }

    function pushClient()
    {
        global $data_src, $directories, $_id, $name, $user_name;
        $return = false;
        for ($i = 0; $i < count($directories); $i++) {
            $e_dir = explode("-", $directories[$i]);
            if (count($e_dir) > 1 && is_array($e_dir) == true) {
                $min = $e_dir[0];
                $max = $e_dir[1];
                if (is_numeric($min) == false) {
                    $min = intval($min);
                }
                if (is_numeric($max) == false) {
                    $max = intval($max);
                }
                if ($min <= $_id && $_id <= $max) {
                    $src = $data_src . $directories[$i] . "/" . $_id;
                    mkdir($src . "/PROJEKTY", 0777, true);
                    $return = true;
                }
            }
        }
        return $return;
    }

    if (pushClient() == false) {
        $min = $_id;
        if ($min > 1) {
            $max = $min + 49;
        } else {
            $max = 49;
        }
        $directory = $min . "-" . $max;
        if (mkdir($data_src . $directory, 0777, true) == false) {
            die($data_src . $directory);
        }

        mkdir($data_src . $directory . "/" . $id . "/PROJEKTY", 0777, true);
    }
    die($_id);
} else if (@$_GET["a"] == 2) { // DANE DO EDYCJI
    $_id = @$_GET["id"];
    $query = $db->prepare("SELECT * FROM `clients` WHERE `id` = '$_id'");
    $query->execute();

    $data = array();
    foreach ($query as $row) {
        setcookie("eclientid", $row['id']);
        $data["id"] = $row['id'];
        $data["name"] = $row['name'];
        $data["type"] = $row['type'];
        $data["email"] = $row['email'];
        $data["phone"] = $row['phone'];

        $date = explode(" ", $row['date']);
        $data["date"] = $date[0];

        $data["person"] = $row['person'];
        $data["nip"] = $row['nip'];
        $address = explode(",", $row['address']);
        $data["address1"] = $address[0];
        $data["address2"] = @$address[1];
    }
    die(json_encode($data));
} else if (@$_GET["a"] == 3) { //ZAPISZ EDYCJE
    $type = $_POST["type"];
    $name = str_replace('"', " ", $_POST["name"]);
    $nip = $_POST["nip"];
    $address1 = $_POST["address1"];
    $address2 = $_POST["address2"];
    $phone = $_POST["phone"];
    $person = $_POST["person"];
    $email = $_POST["email"];

    $address = $address1 . "," . $address2;

    $_id = $_COOKIE["eclientid"];
    $query = $db->prepare("UPDATE `clients` SET `name` = '$name', `type` = '$type', `nip` = '$nip', `address` = '$address', `phone` = '$phone', `person` = '$person', `email` = '$email' WHERE `id` = '$_id'");
    $query->execute();
    die($_id);
}
?>
<div class="row">
    <div class="col-lg-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject font-dark bold uppercase">Baza klientów</span>
                </div>
                <div class="actions">
                    <a href="#clientForm" data-toggle="modal" class="btn btn-info">Dodaj nowy</a>
                </div>
            </div>
            <div class="portlet-body">
                <div aria-hidden="true" role="dialog" tabindex="-1" id="clientForm" class="modal fade"
                     style="display: none;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
                            <h4 class="modal-title">Dodaj nowego klienta</h4>
                        </div>
                        <form id="_clientForm" method="POST" action="?" role="form">
                            <div class="modal-body form">
                                <div id="error" style="display: none;">
                                    <div class="alert alert-block alert-danger fade in">
                                        <strong>Błąd!</strong> Uzupełnij pole nazwy!
                                    </div>
                                </div>
                                <div id="doneMessage" style="display: none;">
                                    <div class="alert alert-success alert-block fade in">
                                        <h4><i class="icon-ok-sign"></i> Gotowe! </h4>
                                        <p>Klient został dodany do bazy danych!</p>
                                    </div>
                                </div>
                                <table style="margin: 0 auto; border-spacing: 2px; border-collapse: separate;">
                                    <tr>
                                        <td style="text-align: right;">Typ:</td>
                                        <td>
                                            <div class='md-radio-inline'>
                                                <div class='md-radio'>
                                                    <input type="radio" name="type" value="1" checked="checked"
                                                           id="rtype1" class="md-radiobtn"/>
                                                    <label for="rtype1"><span></span>
                                                        <span class="check"></span>
                                                        <span class="box"></span>
                                                        Firma</label>
                                                </div>
                                                <div class='md-radio'>
                                                    <input type="radio" name="type" value="2" id="rtype2"
                                                           class="md-radiobtn"/>
                                                    <label for="rtype2"><span></span>
                                                        <span class="check"></span>
                                                        <span class="box"></span>
                                                        Osoba prywatna</label>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right;">Nazwa:</td>
                                        <td><input type="text" name="name" id="name" class="form-control"/></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right;">NIP:</td>
                                        <td><input type="number" name="nip" class="form-control"/></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right;">Email:</td>
                                        <td><input type="text" name="email" class="form-control"/></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right;">Osoba kontaktowa:</td>
                                        <td><input type="text" name="person" class="form-control"/></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right;">Adres:</td>
                                        <td><input type="text" name="address1" class="form-control"/><input type="text"
                                                                                                            name="address2"
                                                                                                            class="form-control"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right;">Telefon:</td>
                                        <td><input type="text" name="phone" class="form-control"/></td>
                                    </tr>

                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>
                                <button type="submit" class="btn btn-success">Zapisz</button>
                            </div>
                        </form>
                    </div>
                </div>
                <table class="table" id="clients">
                    <thead>
                    <tr>
                        <td>Id</td>
                        <td>Nazwa</td>
                        <td>Email</td>
                        <td>Telefon</td>
                        <td>Data</td>
                        <td>Osoba</td>
                        <td>NIP</td>
                        <td>Adres</td>
                        <td>Typ</td>
                        <td>Akcja</td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $clientData = getClients();
                    ?>
                    <?php foreach ($clientData as $client): ?>
                        <tr style="cursor: pointer;" id="<?= $client["id"] ?>_id">
                            <td><?= $client["id"] ?></td>
                            <td><?= $client["name"] ?></td>
                            <td><?= $client["email"] ?></td>
                            <td><?= $client["phone"] ?></td>
                            <td><?= @reset(explode(" ", $client["date"])) ?></td>
                            <td><?= $client["person"] ?></td>
                            <td><?= $client["nip"] ?></td>
                            <td><?= $client["address"] ?></td>
                            <td><?= ($client["type"] == 1 ? "F" : "O") ?></td>
                            <td>
                                <a href="/client/<?= $client["id"] ?>/" data-toggle="modal" class="btn btn-xs btn-info">
                                    Karta
                                </a>
                                <a href="#clienteForm" data-toggle="modal" class="btn btn-xs btn-danger cEdit"
                                   id="<?= $client["id"] ?>_eid">
                                    Edytuj
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
<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="clienteForm" class="modal fade"
     style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
            <h4 class="modal-title">Edycja</h4>
        </div>
        <form id="_clienteFormE" method="POST" action="?">
            <div class="modal-body">
                <div id="eerror" style="display: none;">
                    <div class="alert alert-block alert-danger fade in">
                        <strong>Błąd!</strong> Uzupełnij pole nazwy!
                    </div>
                </div>
                <div id="edoneMessage" style="display: none;">
                    <div class="alert alert-success alert-block fade in">
                        <h4><i class="icon-ok-sign"></i> Gotowe! </h4>
                        <p>Klient został dodany do bazy danych!</p>
                    </div>
                </div>
                <table style="margin: 0 auto; border-spacing: 2px; border-collapse: separate;">
                    <tr>
                        <td style="text-align: right;">Typ:</td>
                        <td>
                            <div class='md-radio-inline'>
                                <div class='md-radio'>
                                    <input type="radio" name="type" value="1" checked="checked" id="ertype1"
                                           class="md-radiobtn"/>
                                    <label for="ertype1"><span></span>
                                        <span class="check"></span>
                                        <span class="box"></span>
                                        Firma</label>
                                </div>
                                <div class='md-radio'>
                                    <input type="radio" name="type" value="2" id="ertype2" class="md-radiobtn"/>
                                    <label for="ertype2"><span></span>
                                        <span class="check"></span>
                                        <span class="box"></span>
                                        Osoba prywatna</label>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right;">Nazwa:</td>
                        <td><input type="text" name="name" id="ename" class="form-control"/></td>
                    </tr>
                    <tr>
                        <td style="text-align: right;">NIP:</td>
                        <td><input type="number" name="nip" id="enip" class="form-control"/></td>
                    </tr>
                    <tr>
                        <td style="text-align: right;">Email:</td>
                        <td><input type="text" name="email" id="eemail" class="form-control"/></td>
                    </tr>
                    <tr>
                        <td style="text-align: right;">Osoba kontaktowa:</td>
                        <td><input type="text" name="person" id="eperson" class="form-control"/></td>
                    </tr>
                    <tr>
                        <td style="text-align: right;">Adres:</td>
                        <td><input type="text" name="address1" id="eaddress1" class="form-control"/><input type="text"
                                                                                                           name="address2"
                                                                                                           id="eaddress2"
                                                                                                           class="form-control"/>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right;">Telefon:</td>
                        <td><input type="text" name="phone" id="ephone" class="form-control"/></td>
                    </tr>

                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>
                <button type="submit" class="btn btn-success">Zapisz</button>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript" charset="utf-8">
    $(document).ready(function () {
        $('#clients').DataTable();

        $("table").on("click", ".cEdit", function () {
            var _id = parseInt($(this).attr("id"));
            $.ajax({
                url: "<?php echo $site_path; ?>/engine/clientbase.php?a=2&id=" + _id
            }).done(function (msg) {
                var data = jQuery.parseJSON(msg);

                $("#_clienteFormE [name=type]").val([data.type]);

                $("#_clienteFormE #ename").val(data.name);
                $("#_clienteFormE #enip").val(data.nip);
                $("#_clienteFormE #eemail").val(data.email);
                $("#_clienteFormE #eperson").val(data.person);
                $("#_clienteFormE #eaddress1").val(data.address1);
                $("#_clienteFormE #eaddress2").val(data.address2);
                $("#_clienteFormE #ephone").val(data.phone);
            });
        });
    });
    $("#_clienteFormE").submit(function (event) {
        $("#edoneMessage").fadeOut();
        if ($("#ename").val() == "") {
            $("#eerror").fadeIn();
        } else {
            $("#eerror").fadeOut();
            $.ajax({
                method: "POST",
                url: "<?php echo $site_path; ?>/engine/clientbase.php?a=3",
                data: $("#_clienteFormE").serialize()
            }).done(function () {
                $("#edoneMessage p").html("Zapisałem zmiany!");
                $("#edoneMessage").fadeIn();
                location.reload();
            });
        }
        event.preventDefault();
    });

    $("#_clientForm").submit(function (event) {
        $("#doneMessage").fadeOut();
        if ($("#name").val() == "") {
            $("#error").fadeIn();
        } else {
            $("#error").fadeOut();
            $.ajax({
                method: "POST",
                url: "<?php echo $site_path; ?>/engine/clientbase.php?a=1",
                data: $("#_clientForm").serialize()
            }).done(function (msg) {
                $("#doneMessage p").html("Klientowi został przyznany ID numer: " + msg);
                $("#doneMessage").fadeIn();
                $("input[type!='radio']").val("");
            });
        }
        event.preventDefault();
    });
</script>
<?php
ob_end_flush();
?>
