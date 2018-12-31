<div id="nmo" class="modal fade modal-scroll" tabindex="-1" data-width="760" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title">Szczegóły powiadomień</h4>
    </div>
    <div class="modal-body">

    </div>
    <div class="modal-footer">
        <button type="button" class="btn green" id="nmo_bs">Zapisz</button>
    </div>
</div>
<!-- BEGIN CONTAINER -->
<div class="wrapper">
    <!-- BEGIN HEADER -->
    <header class="page-header">
        <nav class="navbar mega-menu" role="navigation">
            <div class="container-fluid">
                <div class="clearfix navbar-fixed-top">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="toggle-icon">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </span>
                    </button>
                    <!-- End Toggle Button -->
                    <!-- BEGIN LOGO -->
                    <a id="index" class="page-logo" href="index.html"></a>
                    <!-- END LOGO -->
                    <!-- BEGIN SEARCH -->
                    <form class="search" action="extra_search.html" method="GET">
                        <input type="text" class="form-control" name="query" placeholder="Szukaj...">
                        <a href="javascript:;" class="btn submit">
                            <i class="fa fa-search"></i>
                        </a>
                    </form>
                    <!-- END SEARCH -->
                    <!-- BEGIN TOPBAR ACTIONS -->
                    <div class="topbar-actions">
                        <!-- BEGIN GROUP NOTIFICATION -->
                        <div class="btn-group-notification btn-group" id="header_notification_bar">
                            <?php
                            require_once dirname(__FILE__) . '/class/notification.php';
                            $notifi = getNotification();
                            ?>
                            <button type="button" class="btn btn-sm md-skip dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <i class="icon-bell"></i>
                                <span class="badge" id="nnl_1"><?= $notifi["size"] ?></span>
                            </button>
                            <ul class="dropdown-menu-v2">
                                <li class="external">
                                    <h3>
                                        <span class="bold"><span id="nnl_2"><?= $notifi["size"] ?></span> nowych</span> powiadomień</h3>
                                    <a href="#">pokaż więcej</a>
                                </li>
                                <li>
                                    <ul id="nnlist" class="dropdown-menu-list scroller" style="height: 250px; padding: 0;" data-handle-color="#637283">
                                        <?= $notifi["content"] ?>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <!-- END GROUP NOTIFICATION -->
                        <!-- BEGIN GROUP INFORMATION -->
                        <div class="btn-group-red btn-group">
                            <button type="button" class="btn btn-sm dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <i class="fa fa-plus"></i>
                            </button>
                            <ul class="dropdown-menu-v2" role="menu">
                                <li class="active">
                                    <a href="#">W budowie</a>
                                </li>
                            </ul>
                        </div>
                        <!-- END GROUP INFORMATION -->
                        <!-- BEGIN USER PROFILE -->
                        <div class="btn-group-img btn-group">
                            <button type="button" class="btn btn-sm dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <span>Witaj, <?= $_SESSION["nick"]; ?></span>
                                <img src="<?= $site_path ?>/assets/layouts/layout/img/avatar.png" alt=""> </button>
                            <ul class="dropdown-menu-v2" role="menu">
                                <li>
                                    <a href="<?= $site_path ?>/site/200/kalendarz">
                                        <i class="icon-calendar"></i>Kalendarz</a>
                                </li>
                                <li class="divider"> </li>
                                <li>
                                    <a href="page_user_lock_1.html">
                                        <i class="icon-lock"></i>Zablokuj</a>
                                </li>
                                <li>
                                    <a href="<?= $site_path ?>/engine/logout.php">
                                        <i class="icon-key"></i>Wyloguj</a>
                                </li>
                            </ul>
                        </div>
                        <!-- END USER PROFILE -->
                        <!-- BEGIN QUICK SIDEBAR TOGGLER -->
                        <button type="button" class="quick-sidebar-toggler" data-toggle="collapse">
                            <span class="sr-only">Toggle Quick Sidebar</span>
                            <i class="icon-logout"></i>
                        </button>
                        <!-- END QUICK SIDEBAR TOGGLER -->
                    </div>
                    <!-- END TOPBAR ACTIONS -->
                </div>
                <!-- BEGIN HEADER MENU -->
                <div class="nav-collapse collapse navbar-collapse navbar-responsive-collapse">
                    <ul class="nav navbar-nav">
                        <li class="dropdown dropdown-fw active open selected" id="mb1">
                            <a href="javascript:;" class="text-uppercase">
                                <i class="icon-home"></i> Dashboard </a>
                            <ul class="dropdown-menu dropdown-menu-fw">
                                <li><a href="<?= $site_path ?>/"><i class="icon-bar-chart"></i> Default </a></li>
                                <li><a href="<?= $site_path ?>/site/200/kalendarz"><i class="fa fa-calendar"></i> Kalendarz</a></li>
                                <li><a href="<?= $site_path ?>/site/18/zadania"><i class="fa fa-clock-o"></i> Zadania</a></li>
                                <li><a href="<?= $site_path ?>/site/19/statusy"><i class="fa fa-info-circle"></i> Statusy</a></li>
                            </ul>
                        </li>
                        <li class="dropdown dropdown-fw active" id="mb2">
                            <a href="javascript:;" class="text-uppercase">
                                <i class="icon-calculator"></i> Costing </a>
                            <ul class="dropdown-menu dropdown-menu-fw">
                                <li><a href="<?= $site_path ?>/site/3/blachy"><i class="fa fa-map-o"></i> Blachy</a></li>
                                <li><a href="<?= $site_path ?>/site/4/profile"><i class="fa fa-bars"></i> Profile</a></li>
                                <li><a href="<?= $site_path ?>/site/29/multipart"><i class="fa fa-eur"></i> Multipart</a></li>
                            </ul>
                        </li>
                        <li class="dropdown dropdown-fw active" id="mb3">
                            <a href="javascript:;" class="text-uppercase">
                                <i class="icon-users"></i> CRM </a>
                            <ul class="dropdown-menu dropdown-menu-fw">
                                <li><a href="<?= $site_path ?>/site/6/klienci"><i class="fa fa-male"></i> Baza klientów </a></li>
                                <li><a href="<?= $site_path ?>/site/7/projekty"><i class="fa fa-folder-open"></i> Projekty </a></li>
                                <li><a href="<?= $site_path ?>/site/8/detale"><i class="fa fa-th-large"></i> Spis detali</a></li>
                                <li><a href="<?= $site_path ?>/site/17/blachy"><i class="fa fa-clone"></i> Magazyn blach</a></li>
                                <li><a href="<?= $site_path ?>/site/31/zamowienia"><i class="fa fa-shopping-cart"></i> Lista zamówień</a></li>
                            </ul>
                        </li>
                        <li class="dropdown dropdown-fw active" id="mb4">
                            <a href="javascript:;" class="text-uppercase">
                                <i class="icon-screen-desktop"></i> Panel Operatora</a>
                            <ul class="dropdown-menu dropdown-menu-fw">
                                <li><a href="<?= $site_path ?>/site/15/operator"><i class="fa fa-play"></i> Programy pod cięcie</a></li>
                                <li><a href="<?= $site_path ?>/site/22/operator"><i class="fa fa-pause"></i> Historia wyciętych</a></li>
                            </ul>
                        </li>
                        <li class="dropdown dropdown-fw active" id="mb5">
                            <a href="#"><i class="icon-wrench"></i> Ustawienia</a>
                        </li>
                    </ul>

                </div>
                <!-- END HEADER MENU -->
            </div>
            <!--/container-->
        </nav>
    </header>
    <!-- END HEADER -->
    <div class="container-fluid">
        <div class="page-content">
            <?php

            function calendar_body() {
                global $render;
                echo '<div class="breadcrumbs"><button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".page-sidebar"><span class="sr-only">Toggle navigation</span><span class="toggle-icon"><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></span></button> </div><div class="page-content-container"><div class="page-content-row">';
                $render = true;
            }

            $mb = 1;
            $site = @$_GET["site"];

            $render = false;
            switch ($site) {
                case 0:
                    include __DIR__ . '/dashboard.php';
                    $mb = 1;
                    break;
                case 3:
                    $ct = 1; // blacha
                    include __DIR__ . '/costing.php';
                    $mb = 2;
                    break;
                case 4:
                    $ct = 2; // PROFILE ID = 2
                    include __DIR__ . '/costing.php';
                    $mb = 2;
                    break;
                case 6:
                    include __DIR__ . '/clientbase.php';
                    $mb = 3;
                    break;
                case 7:
                    $mb = 3;
                    include __DIR__ . '/projectbase.php';
                    break;
                case 8:
                    $mb = 3;
                    include __DIR__ . '/details.php';
                    break;
                case 11:
                    include __DIR__ . '/filemanager.php';
                    break;
                case 12:
                    $mb = 3;
                    include __DIR__ . '/galery.php';
                    break;
                case 13: //Order cart
                    $mb = 2;
                    include __DIR__ . '/chart/order.php';
                    break;
                case 14: //Detail cart
                    $mb = 3;
                    include __DIR__ . '/chart/detail.php';
                    break;
                case 15: //Operator cut
                    $mb = 4;
                    $list = 1;
                    include __DIR__ . '/operator.php';
                    break;
                case 16: //Program cart
                    $mb = 4;
                    include __DIR__ . '/chart/program.php';
                    break;
                case 17: //Plate warehouse
                    $mb = 3;
                    include __DIR__ . '/plateWarehouse.php';
                    break;
                case 18: //Task
                    include __DIR__ . '/task.php';
                    break;
                case 19: //Status
                    include __DIR__ . '/pstatus.php';
                    break;
				case 20: //Plate costing frame
					include __DIR__ . '/costing/plateFrame.php';
					break;
                case 21: //Client card
                    $mb = 3;
                    include __DIR__ . "/ClientCardRouter.php";
                    break;
                case 22: //Operator cut
                    $mb = 4;
                    $list = 2;
                    include __DIR__ . '/operator.php';
                    break;
                case 29: //Multipart
                    $mb = 2;
                    include __DIR__ . '/costing/multiPart.php';
                    break;
                case 30: //plate Program card
                    $mb = 2;
                    include __DIR__ . '/costing/plateMultiPart.php';
                    break;
                case 31: //order list
                    $mb = 3;
                    include __DIR__ . '/orderList.php';
                    break;
                case 32:
                    $mb = 3;
                    include __DIR__ . '/wz.php';
                    break;
                case 40:
                    $mb = 3;
                    include __DIR__ . '/materialCard.php';
                    break;
                case 200: //Calendar
                    calendar_body();
                    $active = 0;
                    require __DIR__ . '/calendar.php';
                    break;
                case 201:
                    calendar_body();
                    $active = 1;
                    require __DIR__ . '/calendar.php';
                    break;
                case 202:
                    calendar_body();
                    $active = 2;
                    require __DIR__ . '/calendar.php';
                    break;
                case 203:
                    calendar_body();
                    $active = 3;
                    require __DIR__ . '/calendar.php';
                    break;
                case 204:
                    calendar_body();
                    $active = 4;
                    require __DIR__ . '/calendar.php';
                    break;
                case 205:
                    calendar_body();
                    $active = 5;
                    require __DIR__ . '/calendar.php';
                    break;
                case 206:
                    calendar_body();
                    $active = 6;
                    require __DIR__ . '/calendar.php';
                    break;
                case 207:
                    calendar_body();
                    $active = 7;
                    require __DIR__ . '/calendar.php';
                    break;
                case 208:
                    calendar_body();
                    $active = 8;
                    require __DIR__ . '/calendar.php';
                    break;
                case 209:
                    calendar_body();
                    $active = 9;
                    require __DIR__ . '/calendar.php';
                    break;
                case 210:
                    calendar_body();
                    $active = 10;
                    require __DIR__ . '/calendar.php';
                    break;
                case 600: //COSTING
                    $mb = 2;
                    $url = $_GET["url"];
                    include __DIR__ . '/costing/' . $url . '.php';
                    break;
                case 601:
                    $mb = 2;
                    $nr = $_GET["add"];
                    include __DIR__ . '/costing/autoc.php';
                    break;
				case 602: //Plate single
					$mb = 2;
					$costingId = $_GET["add"];
					include __DIR__ . '/costing/plateSinglePartForm.php';
					break;
                default:
                    echo '<div class="alert alert-block alert-danger fade in"><strong>Błąd 404!</strong> Nie znalazłem podanej podstrony! ID: ' . $site . '</div>';
            }
            if ($render == true) {
                echo '</div></div>';
            }
            ?>
        </div>
        <p class="copyright">2015 © Metronic by keenthemes.</p>
        <a href="#index" class="go2top"><i class="icon-arrow-up"></i></a>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $(".selected").removeClass("open selected");
        $("#mb<?= $mb; ?>").addClass("open selected");
    });
</script>