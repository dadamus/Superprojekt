
<!-- BEGIN : LOGIN PAGE 5-1 -->
<div class="user-login-5">
    <div class="row bs-reset">
        <div class="col-md-6 bs-reset">
            <div class="login-bg" style="background-image:url(<?php echo $site_path; ?>/assets/pages/img/login/bg1.jpg)">
                <!--<img class="login-logo" src="" />--> </div>
        </div>
        <div class="col-md-6 login-container bs-reset">
            <div class="login-content">
                <h1>Panel logowania manager</h1>
                <p>Wprowadz swój login oraz hasło.</p>
                <div id="alert" style="display: none;">
                    <div class="alert alert-block alert-danger fade in">
                        <strong>Błąd</strong> Login lub hasło nieprawidłowe!
                    </div>
                </div>
                <form method="POST" action="?" id="loginform">
                    <div class="row">
                        <div class="col-xs-6">
                            <input type="text" placeholder="Login" class="form-control login-username" id="login-username" name="login" /> </div>
                        <div class="col-xs-6">
                            <input type="password" placeholder="Hasło" class="form-control login-password" id="login-password" name="pass" /> </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <input type="submit" class="btn blue" value="Loguj"/>
                        </div>
                    </div>
                </form>
            </div>
            <div class="login-footer">
                <div class="row bs-reset">
                    <div class="col-xs-8 bs-reset">
                        <div class="login-copyright text-right">
                            <p>Copyright &copy; Adamus 2015</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END : LOGIN PAGE 5-1 -->


<script src="<?php echo $site_path; ?>/js/login.js" type="text/javascript"></script> 