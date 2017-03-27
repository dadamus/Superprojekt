$("#loginform").on("submit", function (event) {
    event.preventDefault();
    var login = $("#login-username").val();
    var pass = $("#login-password").val();
    $.ajax({
        type: "POST",
        url: site_path + "/engine/login.php",
        context: document.body,
        data: {
            login: login,
            pass: pass
        }
    }).done(function (msg) {
        window.location.href = site_path + "/index.php";
    });
});