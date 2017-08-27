$(document).ready(function () {
    $('#time1').timepicker({
        minuteStep: 1,
        template: 'modal',
        appendWidgetTo: 'body',
        showSeconds: true,
        showMeridian: false,
        defaultTime: false
    });
    
    $("#saveProgram").on("click", function () {
        $.ajax({
            url: "?",
            method: "POST",
            data: $("#count").serialize() + "&save=true"
        }).done(function () {
            location.reload();
        });
    });
});