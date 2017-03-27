<?php
if (@$_GET["a"] != null) {
    require_once dirname(__FILE__) . '/../config.php';
    require_once dirname(__FILE__) . '/protect.php';
}

require_once dirname(__FILE__) . '/class/calendar.php';

$calendar = new calendar($site_path);

//Type colors | Pilnosc
$type_color = [
    0 => 'grey', //Niska
    1 => 'green', //Normalna
    2 => 'yellow', //Wysoka
    3 => 'red', //Bardzo duza
];

$action = @$_GET["a"];
if ($action == "1") { //Generate new event
    $ibAP = @$_POST["ibAP"]; //Projekt
    $ibAU = @$_POST["ibAU"]; //Uzytkownicy[]
    $ibAD = @$_POST["ibAD"]; //Detale[]
    $ibAPr = @$_POST["ibAPr"]; //Programy[]
    $ibAPl = @$_POST["ibAPl"]; //Pilnosc
    $ibAAm = @$_POST["ibAAm"]; //Maszyny

    $render = "<strong>" . str_replace("'", "\"", @$_POST["eventText"]) . "</strong><p>";
    $settings = [
        "projects" => "",
        "users" => "",
        "details" => "",
        "programs" => "",
        "machines" => "",
        "type" => $ibAPl,
    ];
    if ($ibAP > 0) {
        $q_p = $db->query("SELECT `nr`, `name` FROM `projects` WHERE `id` = '$ibAP'");
        $p = $q_p->fetch();
        $render .= "<br/><i class=\"fa fa-folder\"></i> " . $p["nr"] . "-" . $p["name"];
        $settings["project"] = $ibAP;
    }

    if ($ibAU > 0) {
        $_au = explode(",", $ibAU);
        foreach ($_au as $au) {
            $q_u = $db->query("SELECT `name` FROM `accounts` WHERE `id` = '$au'");
            $u = $q_u->fetch();

            $render .= "<br/><i class=\"fa fa-user\"></i> " . $u["name"];
            $settings["users"] .= $au . "|";
        }
    }
    if ($ibAD > 0) {
        $_ad = explode(",", $ibAD);
        foreach ($_ad as $ad) {
            $q_d = $db->query("SELECT `src` FROM `details` WHERE `id` = '$ad'");
            $d = $q_d->fetch();

            $render .= "<br/><i class=\"fa fa-cube\"></i> " . $d["src"];
            $settings["details"] .= $ad . "|";
        }
    }

    if ($ibAPr > 0) {
        $_apr = explode(",", $ibAPr);
        foreach ($_apr as $apr) {
            $q_pr = $db->query("SELECT `name` FROM `programs` WHERE `id` = '$apr'");
            $pr = $q_pr->fetch();

            $render .= "<br/><i class=\"fa fa-object-ungroup\"></i> " . $pr["name"];
            $settings["programs"] .= $pr . "|";
        }
    }
    if ($ibAAm != "null") {
        $_aam = explode(",", $ibAAm);
        foreach ($_aam as $aam) {
            $render .= "<br/><i class=\"fa fa-plug\"></i> " . $aam;
            $settings["machines"] .= $aam . "|";
        }
    }

    $_s = json_encode($settings);

    die('<div class="external-event label label-default">' . $render . '</p><div class="ecolor" style="display: none">' . $type_color[$ibAPl] . '</div><div class="edata" style="display: none">' . $_s . '</div></div>');
} else if ($action == "2") { //Add new event 
    $active = $_POST["active"];
    $data = json_decode($_POST["d"], true);
    $date = $_POST["stdate"];
    $active = $_POST["active"];
    $title = $_POST["title"];

    $uid = $_SESSION["login"];

    $db->query("INSERT INTO `calendar` (`title`, `project`, `type`, `ctype`, `user`, `startdate`, `allday`) VALUES ('$title', '" . $data["projects"] . "', '" . $data["type"] . "', '$active', '$uid', '$date', 'true')");
    $eid = $db->lastInsertId();
    
    //Add details
    //$data["details"] $data["programs"];
    $details = explode("|", $data["details"]);
    foreach($details as $detail) {
        if ($detail == "") {
            continue;
        }
        
        $db->query("INSERT INTO `calendar_details` (`cid`, `did`) VALUES ('$eid', '$detail')");
    }
    $users = explode("|", $data["users"]);
    foreach($users as $user) {
        if ($user == "") {
            continue;
        }
        
        $db->query("INSERT INTO `calendar_user` (`cid`, `uid`) VALUES ('$eid', '$user')");
    }
    $programs = explode("|", $data["programs"]);
    foreach($programs as $program) {
        if ($program == "") {
            continue;
        }
        
        $db->query("INSERT INTO `calendar_programs` (`cid`, `pid`) VALUES ('$eid', '$program')");
    }
    
    die($eid);
} else if ($action == "3") { //Change event position
    $eid = $_POST["eid"];
    $startdate = $_POST["ndate"];
    $enddate = $_POST["eend"];
    $allday = $_POST["allday"];

    $db->query("UPDATE `calendar` SET `startdate` = '$startdate', `enddate` = '$enddate', `allday` = '$allday' WHERE `id` = '$eid'");

    die("1");
} else if ($action == "4") { //Delete event
    $evid = $_GET["evid"];
    $db->query("DELETE FROM `calendar` WHERE `id` = '$evid'");
    die("1");
}
?>

<div class="page-sidebar">
    <nav class="navbar" role="navigation">
        <ul class="nav navbar-nav">
            <?php

            function createMenuRow($id, $text, $link, $class = "") {
                echo "<li id=\"$id\" class=\"$class\"><a href=\"$link\">$text</a></li>";
            }
            
            
            foreach ($calendar->menu as $key => $value) {
                $class = "";
                if ($active == $key) {
                    $class = "active";
                }
                createMenuRow($key, $value["text"], $value["link"], $class);
            }
            ?>
        </ul>
    </nav>
</div>
<div class="page-content-col">
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light portlet-fit bordered calendar">
                <div class="portlet-title">
                    <div class="caption">
                        <span class="caption-subject font-green sbold uppercase">
                            Kaledarz
                        </span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-12" style="margin-bottom: 50px;">
                            <div id="external-events">
                                <form class="inline-form" id="addNewEventForm">
                                    <div class="input-group">
                                        <input type="text" value="" class="form-control" placeholder="Tekst" name="eventText" id="event_title" />
                                        <div class="input-group-btn">
                                            <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-chain"></i>
                                                <i class="fa fa-angle-down"></i>
                                            </button>
                                            <ul class="dropdown-menu pull-right">
                                                <?php
                                                $s_menu = [
                                                    [
                                                        "id" => "bAP",
                                                        "text" => "Projekt",
                                                        "access" => [
                                                            2, 3, 8, 9
                                                        ],
                                                    ],
                                                    [
                                                        "id" => "bAU",
                                                        "text" => "Użytkownicy",
                                                        "access" => [
                                                            0, 3, 5, 7, 8, 9, 10
                                                        ],
                                                    ],
                                                    [
                                                        "id" => "bAD",
                                                        "text" => "Detale",
                                                        "access" => [
                                                            3, 5, 9
                                                        ],
                                                    ],
                                                    [
                                                        "id" => "bAPr",
                                                        "text" => "Programy",
                                                        "access" => [
                                                            2
                                                        ],
                                                    ],
                                                    [
                                                        "id" => "bAPl",
                                                        "text" => "Pilność",
                                                        "access" => [
                                                            0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10
                                                        ],
                                                    ],
                                                    [
                                                        "id" => "bAAm",
                                                        "text" => "Maszyny",
                                                        "access" => [
                                                            10
                                                        ],
                                                    ],
                                                ];
                                                foreach ($s_menu as $mrow) {
                                                    if (array_search($active, $mrow["access"]) !== false) {
                                                        echo '<li><a href="javascript:;" id="' . $mrow["id"] . '">' . $mrow["text"] . '</li>';
                                                    }
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                    <div id="eattach">
                                        <div id="ibAP" class="idiv" style="display: none; margin-top: 10px;">
                                            <div class="input-group">
                                                <select style="width: 100%;" name="ibAP" class="form-control select2" id="ibAP-input">
                                                    <option value="" disabled="disabled" selected="selected">Projekty</option>
                                                    <?php
                                                    $p = $db->query("SELECT `id`, `name` FROM `projects`");
                                                    foreach ($p as $value) {
                                                        echo '<option value="' . $value["id"] . '">' . $value["name"] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn red closeIA" id="ibAP-r"><i class="fa fa-close"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="ibAU" class="idiv" style="display: none; margin-top: 10px;">
                                            <div class="input-group">
                                                <select style="width: 100%;" id="ibAU-input" name="ibAU[]" class="form-control select2" multiple="multiple" data-placeholder="User">
                                                    <?php
                                                    $p = $db->query("SELECT `id`, `name` FROM `accounts`");
                                                    foreach ($p as $value) {
                                                        echo '<option value="' . $value["id"] . '">' . $value["name"] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn red closeIA" id="ibAU-r"><i class="fa fa-close"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="ibAD" class="idiv" style="display: none; margin-top: 10px;">
                                            <div class="input-group">
                                                <select style="width: 100%;" id="ibAD-input" name="ibAD[]" class="form-control select2" multiple="multiple" data-placeholder="Detale">
                                                    <?php
                                                    $p = $db->query("SELECT `id`, `src` FROM `details`");
                                                    foreach ($p as $value) {
                                                        echo '<option value="' . $value["id"] . '">' . $value["src"] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn red closeIA" id="ibAD-r"><i class="fa fa-close"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="ibAPr" class="idiv" style="display: none; margin-top: 10px;">
                                            <div class="input-group">
                                                <select style="width: 100%;" id="ibAPr-input" name="ibAPr[]" class="form-control select2" multiple="multiple" data-placeholder="Programy">
                                                    <?php
                                                    $p = $db->query("SELECT `id`, `name` FROM `programs`");
                                                    foreach ($p as $value) {
                                                        echo '<option value="' . $value["id"] . '">' . $value["name"] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn red closeIA" id="ibAPr-r"><i class="fa fa-close"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="ibAPl" class="idiv" style="display: none; margin-top: 10px;">
                                            <div class="input-group">
                                                <select style="width: 100%;" id="ibAPl-input" name="ibAPl" class="form-control" data-placeholder="Pilność">
                                                    <option value="0">Niska</option>
                                                    <option value="1">Normalna</option>
                                                    <option value="2">Wysoka</option>
                                                    <option value="3">Bardzo duża</option>
                                                </select>
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn red closeIA" id="ibAPl-r"><i class="fa fa-close"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="ibAAm" class="idiv" style="display: none; margin-top: 10px;">
                                            <div class="input-group">
                                                <select style="width: 100%;" id="ibAPl-input" name="ibAAm[]" class="select2 form-control" data-placeholder="Maszyny" multiple="multiple">
                                                    <option>Gilotyna</option>
                                                    <option>Walcarka</option>
                                                    <option>Piła taśmowa</option>
                                                </select>
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn red closeIA" id="ibAAm-r"><i class="fa fa-close"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <br/>
                                    <a href="javascript:;" id="event_add" class="btn green"> Stwórz</a>
                                </form>
                                <hr/>
                                <div id="event_box" class="margin-bottom-10">
                                </div>
                            </div>
                        </div>
                        <hr class="visible-xs" /> 
                        <div class="col-md-9 col-sm-12">
                            <div id="calendar" class="has-toolbar"> </div>
                        </div>
                    </div>

                    <div id="context-menu">
                        <ul class="dropdown-menu" role="menu">
                            <li class="cmView">
                                <a href="#">
                                    <i class="fa fa-search"></i>
                                    Podgląd
                                </a>
                            </li>
                            <li class="cmEdit">
                                <a href="#">
                                    <i class="fa fa-pencil"></i>
                                    Edycja
                                </a>
                            </li>
                            <li class="cmDelete">
                                <a href="#">
                                    <i class="fa fa-close"></i>
                                    Usuń
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

    var menu_event_id = null;

    $(document).ready(function () {
        $('.select2').select2();
        $('#calendar').fullCalendar('destroy');

        //--------------ADD NEW EVENT
        //add bap
        $("#bAP").on("click", function () {
            $("#ibAP").fadeIn();
        });
        $("#bAU").on("click", function () {
            $("#ibAU").fadeIn();
        });
        $("#bAD").on("click", function () {
            $("#ibAD").fadeIn();
        });
        $("#bAPr").on("click", function () {
            $("#ibAPr").fadeIn();
        });
        $("#bAPl").on("click", function () {
            $("#ibAPl").fadeIn();
        });
        $("#bAAm").on("click", function () {
            $("#ibAAm").fadeIn();
        });
        $(".closeIA").on("click", function () {
            $(this).parent().parent().parent().fadeOut();
            $(this).parent().parent().find(".select2").select2().val(null).trigger("change");
        });

        $("#event_add").on("click", function () {
            $("#addNewEventForm").submit();
            $(".idiv").fadeOut();
            $(".select2").select2().val(null).trigger("change");
        });

        $("#addNewEventForm").on("submit", function (e) {
            e.preventDefault();
            App.blockUI({boxed: !0});
            $.ajax({
                method: "POST",
                data: "ibAP=" + $("select[name='ibAP']").val() +
                        "&eventText=" + $("input[name='eventText']").val() +
                        "&ibAU=" + $("select[name='ibAU[]']").val() +
                        "&ibAD=" + $("select[name='ibAD[]']").val() +
                        "&ibAPr=" + $("select[name='ibAPr[]']").val() +
                        "&ibAPl=" + $("select[name='ibAPl']").val() +
                        "&ibAAm=" + $("select[name='ibAAm[]']").val(),
                url: "<?php echo $site_path; ?>/engine/calendar.php?a=1"
            }).done(function (msg) {
                $("#event_box").append(msg);
                $('.external-event').draggable({
                    revert: true, // immediately snap back to original position
                    revertDuration: 0  //
                });
                App.unblockUI();
            });
        });

        $('#event_box div').draggable({
            revert: true, // immediately snap back to original position
            revertDuration: 0  //
        });

        $("#calendar").fullCalendar({
            header:
                    {
                        left: "title",
                        center: "",
                        right: "prev,next,today,month,agendaWeek,agendaDay"
                    },
            lang: 'pl',
            droppable: true,
            editable: true,
            slotMinutes: 15,
            events: [
<?php
if ($active == 1) {
    $uid = $_SESSION["login"];
    $events = $db->query("SELECT * FROM `calendar` WHERE `ctype` = '$active' AND `user` = '$uid'");
} else {
    $events = $db->query("SELECT * FROM `calendar` WHERE `ctype` = '$active'");
}
foreach ($events as $event) {

    $enddate = "";
    if (strlen($event["enddate"]) > 1) {
        $enddate = "end: '" . $event["enddate"] . "', ";
    }

    echo "{id: '" . $event["id"] . "', title: '" . $event["title"] . '<div class="ce_db_id" style="display: none;">' . $event["id"] . '</div>' . "', start: '" . $event["startdate"] . "',$enddate allday: '" . $event["allday"] . "', backgroundColor: App.getBrandColor('" . $type_color[$event["type"]] . "')},";
}
?>
            ],
            drop: function (date) { //new event
                App.blockUI({boxed: !0});

                var _title = $(this).html();
                var _t = this;
                var e_color = $(this).find('.ecolor').text();
                var tp_data = $(this).find('.edata').html();
                var originalEventObject = $(this).data('eventObject');
                $.ajax({
                    method: "POST",
                    data: "title=" + _title + "&d=" + tp_data + "&stdate=" + date.format() + "&active=<?php echo $active; ?>",
                    url: "<?php echo $site_path; ?>/engine/calendar.php?a=2"
                }).done(function (msg) {
                    var copiedEventObject = $.extend({}, originalEventObject);
                    console.log("new Event: " + msg);
                    copiedEventObject.id = msg;
                    copiedEventObject.start = date;
                    copiedEventObject.title = _title + '<div class="ce_db_id" style="display: none;">' + msg + '</div>';
                    copiedEventObject.backgroundColor = App.getBrandColor(e_color);
                    $('#calendar').fullCalendar('renderEvent', copiedEventObject, true);
                    $(_t).remove();
                    App.unblockUI();
                });

            },
            eventDrop: function (event, delta, revertFunc) {
                App.blockUI({boxed: !0});
                var eend = "";
                if (event.end > 0) {
                    eend = event.end.format();
                }

                console.log("event drop: " + event.id);

                $.ajax({
                    method: "POST",
                    data: "eid=" + event.id + "&ndate=" + event.start.format() + "&eend=" + eend + "&allday=" + event.allday,
                    url: "<?php echo $site_path; ?>/engine/calendar.php?a=3"
                }).done(function (msg) {
                    App.unblockUI();
                });
            },
            eventResize: function (event, jsEvent, ui, view) {
                App.blockUI({boxed: !0});
                var eend = "";
                if (event.end > 0) {
                    eend = event.end.format();
                }

                $.ajax({
                    method: "POST",
                    data: "eid=" + event.id + "&ndate=" + event.start.format() + "&eend=" + eend + "&allday=" + event.allday,
                    url: "<?php echo $site_path; ?>/engine/calendar.php?a=3"
                }).done(function (msg) {
                    App.unblockUI();
                });
            },
            eventRender: function (event, element, view) {
                element.find('.fc-title').html(event.title);
            },
            eventAfterAllRender: function () {
                $('.fc-event').contextmenu({
                    target: '#context-menu',
                    before: function (n) {
                        //return n.preventDefault(), "SPAN" == n.target.tagName ? (n.preventDefault(), this.closemenu(), !1) : !0
                        menu_event_id = parseInt($(n.target).find(".ce_db_id").html());

                        n.preventDefault();
                        return true;
                    },
                    onItem: function (context, e) {
                    }
                });
            }
        });

        //----------------Context menu
        $(".cmDelete").on("click", function () {
            if (confirm("Usunąć wpis?")) {
                App.blockUI({boxed: !0});
                $.ajax({
                    url: "<?php echo $site_path; ?>/engine/calendar.php?a=4&evid=" + menu_event_id
                }).done(function () {
                    $("#calendar").fullCalendar('removeEvents', menu_event_id);
                    App.unblockUI();
                });
            }
        });
    });
</script>