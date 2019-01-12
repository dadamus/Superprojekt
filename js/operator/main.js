var _plist = "";
$(document).ready(function () {
    chlp = false;

    $('#nestable').nestable().on('change', function () {
        $("#slbuttons").slideDown();
        _plist = JSON.stringify($("#nestable").nestable('serialize'));
    });
    $(".bPinfo").on("click", function () {
        let extended;
        if ($(this).data('extended')) {
            extended = 1;
        } else {
            extended = 0;
        }

        var prId = $(this).parent().parent().attr("data-id");
        App.blockUI({target: "#pcontent"});
        $.ajax({
            url: "/engine/operator.php?action=2&prId=" + prId + "&extended=" + extended
        }).done(function (content) {
            $("#pcontent").html(content);
            App.unblockUI();
        });
    });
    $("#slbuttons").on("click", function () {
        App.blockUI();
        $.ajax({
            method: "POST",
            data: "plis=" + _plist,
            url: "<?php echo $site_path; ?>/engine/operatorCut.php?action=1"
        }).done(function (msg) {
            App.unblockUI();
            window.location.reload();
        });
    });

    $("#pcontent").on('click', '.ajax-modal', function (e) {
        //Modal do zmiany statusu
        e.preventDefault();

        App.blockUI();
        let url = $(this).data('url');
        $.ajax({
            method: 'GET',
            url: url
        }).done(function (response) {
            $('#modal-container').html(response);
            initModal();
        }).always(function () {
            App.unblockUI();
        });
    });

    let initModal = function () {
        $('#correction-program-select2').select2();
        $('#modal-container').find('#status-modal').modal('show');
    };

    $(document).on('change', 'select[name="list-state"]', function () {
        //Tu przy statusie 2,3 otwieramy pole do wpisanie sztuk detali
        let $statusSelect = $('select[name="list-state"]');
        let state = $statusSelect.val();

        let $detailsRow = $('.list-details');
        let $correctionRow = $('.correction');

        console.log(state);

        if (state == 2 || state == 3) {
            $detailsRow.show();
        } else {
            $detailsRow.hide();
        }

        if (state == 7) {
            $correctionRow.show();
        } else {
            $correctionRow.hide();
        }

    });
    $(document).on('click', '.submit-status-change', function () {
        //A tu juz zmiana statusu
        var listId = $(this).data('list-id');
        var $statusSelect = $('select[name="list-state"]');
        var state = $statusSelect.val();
        var optionText = $statusSelect.find('option[value="' + state + '"]').text();

        var postData = "state=" + state + "&list-id=" + listId;

        //Liczmy detale
        var details = "";
        var $detailsRow = $('.list-details');

        $detailsRow.find('.detail-count').each(function () {
            var detailId = $(this).data('detail-id');

            if (details !== "") {
                details += ",";
            }

            details += detailId;
            postData += "&detail_" + detailId + "=" + $(this).val();
            postData += "&detail_waste_" + detailId + "=" + $detailsRow.find('input[name="detail_waste_' + detailId + '"]').val();
        });

        postData += "&details=" + details;

        postData += "&correctionId=" + $("#correction-program-select2").val();

        $('#status-modal').modal('hide');
        App.blockUI();

        $.ajax({
            method: 'POST',
            url: '/engine/chart/program.php?action=3',
            data: postData
        }).done(function () {
            toastr.success('Status zmieniony!');
            //Zmiamy status zeby widzieli ladne
            $('.list-item-state[data-item-id="' + listId + '"]').html(optionText);
        }).error(function () {
            toastr.error('Wystąpił błąd!');
            $('#status-modal').modal('hide');
        }).always(function () {
            App.unblockUI();
        });
    });
});