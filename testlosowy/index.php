<!DOCTYPE html 
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <script src="../assets/global/plugins/jquery.min.js" type="text/javascript"></script>
        <title>Test ciągów losowych</title>
    </head>
    <body>
        <div style="width: 600px; margin: 0 auto; padding-top: 5%; text-align: center;">
            <b>Test świadomości generatora znaków losowych</b>
            <p id="cuter" style="font-size: 64px;">
                <button type="button">Start</button>
            </p>
        </div>
    </body>
</html>
<script type="text/javascript">
    var start = false;
    var time = 3;
    var digits = [];

    var mainl;
    var gl;

    function generate()
    {
        digits.push(Math.floor((Math.random() * 2) + 1));
    }

    function doIt()
    {
        if (start == false) {
            if (time > 1) {
                time -= 1;
                $("#cuter").html("<b>" + time + "</b>");
            } else {
                $("#cuter").html("<b>Start!</b>");
                start = true;
                time = 5;
                gl = setInterval(generate, 100);
            }
        } else {
            if (time >= 1) {
                time -= 1;
            } else {
                window.clearInterval(mainl);
                window.clearInterval(gl);

                console.log(digits.length);

                var _c1 = 0;
                var _c2 = 0;
                for (var i = 0; i < digits.length; i++)
                {
                    if (digits[i] == 1) {
                        _c1 += 1;
                    } else
                    {
                        _c2 += 1;
                    }
                }
                console.log(digits.length);
                var pc1 = _c1 * digits.length / 100;
                var pc2 = 100 - pc1;
                console.log(_c1);
                $("#cuter").html(pc1 + "/" + pc2);
            }
        }
    }

    $(document).ready(function () {
        $("#cuter button").on("click", function () {
            $("#cuter").html("<b>3</b>");
            mainl = setInterval(doIt, 1000);
        });
    });
</script>