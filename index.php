<?php
ob_start();
session_start();
require_once dirname(__FILE__) . '/config.php';
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="pl">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->
    <head>
        <meta charset="utf-8" /> 
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport"/>
        <meta content="" name="description"/>
        <meta content="" name="author"/>
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="//fonts.googleapis.com/css?family=Oswald:400,300,700" rel="stylesheet" type="text/css" />
        <link href="<?php echo $site_path; ?>/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $site_path; ?>/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $site_path; ?>/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $site_path; ?>/assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $site_path; ?>/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGIN STYLES -->
        <link href="<?php echo $site_path; ?>/assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $site_path; ?>/assets/global/css/components-md.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="<?php echo $site_path; ?>/assets/global/css/plugins-md.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $site_path; ?>/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $site_path; ?>/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $site_path; ?>/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $site_path; ?>/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $site_path; ?>/assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $site_path; ?>/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $site_path; ?>/assets/global/plugins/clockface/css/clockface.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $site_path; ?>/assets/global/plugins/clockface/css/clockface.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $site_path; ?>/assets/global/plugins/typeahead/typeahead.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $site_path; ?>/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $site_path; ?>/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $site_path; ?>/assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $site_path; ?>/assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $site_path; ?>/assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $site_path; ?>/assets/global/plugins/bootstrap-toastr/toastr.min.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $site_path; ?>/assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css"/>
        <!-- END PAGE LEVEL PLUGIN STYLES -->
        <link href="<?php echo $site_path; ?>/assets/global/plugins/fullcalendar/fullcalendar.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $site_path; ?>/assets/global/plugins/jquery-nestable/jquery.nestable.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $site_path; ?>/assets/global/plugins/dropzone/dropzone.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $site_path; ?>/assets/global/plugins/dropzone/basic.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $site_path; ?>/assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="<?php echo $site_path; ?>/assets/global/css/plugins.min.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $site_path; ?>/style/style.css" rel="stylesheet" type="text/css" />
        <?php
        if (@$_SESSION["login"] == null) {
            echo '<!-- BEGIN PAGE LEVEL STYLES -->
    <link href="' . $site_path . '/assets/pages/css/login-5.min.css" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL STYLES -->';
        } else {
            echo '<!-- BEGIN THEME STYLES -->
    <link href="' . $site_path . '/assets/layouts/layout5/css/layout.min.css" rel="stylesheet" type="text/css"/>
    <link href="' . $site_path . '/assets/layouts/layout5/css/custom.min.css" rel="stylesheet" type="text/css"/>';
        }
        ?>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/fullcalendar/lib/moment.min.js" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="<?php echo $site_path; ?>/assets/global/plugins/jquery-nestable/jquery.nestable.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/clockface/js/clockface.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/morris/morris.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/morris/raphael-min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/counterup/jquery.waypoints.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/counterup/jquery.counterup.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/amcharts/amcharts/amcharts.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/amcharts/amcharts/serial.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/amcharts/amcharts/pie.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/amcharts/amcharts/radar.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/amcharts/amcharts/themes/light.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/amcharts/amcharts/themes/patterns.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/amcharts/amcharts/themes/chalk.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/amcharts/ammap/ammap.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/amcharts/ammap/maps/js/worldLow.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/amcharts/amstockcharts/amstock.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/flot/jquery.flot.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/flot/jquery.flot.resize.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/flot/jquery.flot.categories.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/jquery-easypiechart/jquery.easypiechart.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/jquery.sparkline.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/typeahead/handlebars.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/typeahead/typeahead.bundle.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/ckeditor/ckeditor.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/bootstrap-markdown/lib/markdown.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/bootstrap-markdown/js/bootstrap-markdown.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/dropzone/dropzone.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="<?php echo $site_path; ?>/assets/global/scripts/app.min.js" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->
        <!-- BEGIN PAGE LEVEL SCRIPTS -->
		<script src="<?php echo $site_path; ?>/assets/global/plugins/bootstrap-growl/jquery.bootstrap-growl.min.js" type="text/javascript"></script>
		<script src="<?php echo $site_path; ?>/assets/global/plugins/bootstrap-contextmenu/bootstrap-contextmenu.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/bootstrap-toastr/toastr.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/pages/scripts/ui-toastr.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/bootstrap-editable/bootstrap-editable/js/bootstrap-editable.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/pages/scripts/form-editable.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/pages/scripts/form-validation.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/pages/scripts/dashboard.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/pages/scripts/ui-extended-modals.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/js/js.cookie.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/js/lightbox.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/jquery-knob/js/jquery.knob.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/fullcalendar/fullcalendar.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/fullcalendar/lang-all.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/global/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
		<script src="<?php echo $site_path; ?>/assets/pages/scripts/ui-bootstrap-growl.min.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL SCRIPTS -->
        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <script src="<?php echo $site_path; ?>/assets/global/plugins/holder.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/layouts/layout5/scripts/layout.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
        <script src="<?php echo $site_path; ?>/assets/layouts/global/scripts/quick-nav.min.js" type="text/javascript"></script>
        <!-- END THEME LAYOUT SCRIPTS -->

        <title>ABL</title>
    </head>
    <?php
    if (@$_SESSION["login"] == null) {
        echo '<body class="login">';
        require_once dirname(__FILE__) . '/login.php';
    } else {
        echo '<body class="page-header-fixed page-sidebar-closed-hide-logo page-md">';
        require_once dirname(__FILE__) . '/engine/wrapper.php'; //Ja pierdole nie mam pojecia czemu to nei dziala
    }
    ?>


    <!--[if lt IE 9]>
    <script src="../assets/global/plugins/respond.min.js"></script>
    <script src="../assets/global/plugins/excanvas.min.js"></script> 
    <![endif]-->
    <!-- BEGIN CORE PLUGINS -->
    <script type="text/javascript">
        var site_path = "<?php echo $site_path; ?>";
    </script>
    <script src="<?php echo $site_path; ?>/js/refreshMail.js" type="text/javascript"></script>
</body>
</html>
<?php
ob_end_flush();
?>