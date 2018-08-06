const $dirContainer = $("#dirContainer");
const dirId = $dirContainer.data("id");
var plateMultiPartUrl = "/engine/costing/plateMultiPart.php";
const $details = $("#details");

//Tabelka do csv
$("#mpwToCsv").on('click', function (e) {
    e.preventDefault();


});

$details.DataTable({
    columnDefs: [{
        orderable: false,
        searchable: false,
        className: 'select-checkbox',
        targets: 0,
    }],
    select: {
        style: 'multi',
        selector: 'td:first-child'
    },
    order: [[1, 'asc']]
});

let shiftPressed = false;

$(document).on('keydown', function (e) {
    if (e.which === 16) {
        shiftPressed = true;
    }
}).on('keyup', function (e) {
    if (e.which === 16) {
        shiftPressed = false;
    }
});

$details.on('click', '.select-checkbox', function (e) {
    if (shiftPressed) {
        shiftPressed = false;
        let $clickedTr = $(this).closest('tr');
        let $selected = $details.find('tr.selected');
        let last = ($selected.length - 2);

        let $lastSelected = $details.find('tr.selected:eq(' + last + ')');

        for (let i = 1; i < 10000; i++) {
            if (!$lastSelected.hasClass('selected')) {
                $lastSelected.find('.select-checkbox').trigger('click');
            }
            $lastSelected = $lastSelected.next();
            if ($lastSelected === $clickedTr) {
                break;
            }
        }
    }
});

$details.on('keyup', 'input', function () {
    let $tr = $(this).closest('tr');
    if (!$tr.hasClass('selected')) {
        return true;
    }

    let name = $(this).data('name');
    let value = $(this).val();

    let $selected = $details.find('tr.selected');
    $selected.find('input[data-name="' + name + '"]').val(value);
    $selected.each(function () {
        setMaterialName($(this));
    });
    setMaterialName($tr);
});

$details.on('change', 'select', function () {
    let $tr = $(this).closest('tr');
    if (!$tr.hasClass('selected')) {
        return true;
    }

    let name = $(this).data('name');
    let value = $(this).val();

    let $selected = $details.find('tr.selected');
    $selected.find('select[data-name="' + name + '"]').val(value);
});

let setMaterialName = function ($tr) {
    let material = $tr.find('select[name="material[]"]').find('option:selected').text();
    let thickness = $tr.find('input[name="thickness[]"]').val();
};

$("a.mpw-detail-delete").on("click", function () {
    let $detail = $(this).parent().parent();
    let mpwId = $(this).data("mpw-id");
    let detailId = $(this).data("detail-id");

    swal({
            title: "Jesteś pewien?",
            text: "Chcesz usunąc ten detal z wyceny?",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Tak usuń!",
            cancelButtonText: "Anuluj",
            closeOnConfirm: false
        },
        function () {
            $.ajax({
                url: "/engine/costing/multiPart.php?action=deleteMpwItem",
                method: "POST",
                data: "mpw=" + mpwId + "&detail=" + detailId + "&dir=" + dirId
            }).done(function () {
                swal("Usunięto", "Detal został usunięty z wyceny!", "success");
                $detail.hide();
            });
        });
});

$details.on('change', '.thickness-picker', function () {
    let thickness = $(this).val();
    let $tr = $(this).closest('tr');
    let material = $tr.find('.material-picker').find(':selected').html().trim();
    loadTMaterial($tr, material, thickness);
    loadLaserMaterial($tr, material, thickness);
});

$details.on('change', '.material-picker', function () {
    let material = $(this).find(':selected').text().trim();
    let $tr = $(this).closest('tr');
    let thickness = $tr.find('.thickness-picker').val();
    loadTMaterial($tr, material, thickness);
    loadLaserMaterial($tr, material, thickness);
});

let loadTMaterial = function ($tr, material, thickness) {
    let $picker = $tr.find('.t-material-picker');

    let $selected = $details.find('tr.selected').find('.t-material-picker');
    if ($tr.hasClass('selected')) {
        $selected.prop('disabled', true);
    }

    $picker.prop('disabled', true);

    $.ajax({
        'method': 'POST',
        'data': 'material=' + material + "&thickness=" + thickness,
        'url': plateMultiPartUrl + "?action=getTMaterial"
    }).done(function (responseData) {
        let response = JSON.parse(responseData);

        if (response.length > 0) {
            let data = '';
            for (let item in response) {
                data += "<option>" + response[item].MaterialName + "</option>";
            }
            $picker.html(data);

            if ($tr.hasClass('selected')) {
                $selected.html(data);
                $selected.prop('disabled', false);
            }

            $picker.prop('disabled', false);
        } else {
            $picker.html('');
            if ($tr.hasClass('selected')) {
                $selected.html('')
            }
        }
    });
};

let loadLaserMaterial = function ($tr, material, thickness) {
    let $picker = $tr.find('.laser-material-name-picker');

    let $selected = $details.find('tr.selected').find('.laser-material-name-picker');
    if ($tr.hasClass('selected')) {
        $selected.prop('disabled', true);
    }

    $picker.prop('disabled', true);
    $.ajax({
        'method': 'POST',
        'data': 'material=' + material + "&thickness=" + thickness,
        'url': plateMultiPartUrl + "?action=getMaterialLaser"
    }).done(function (responseData) {
        let response = JSON.parse(responseData);
        if (response.length > 0) {
            let data = '';
            for (let item in response) {
                data += "<option value='" + response[item].ccId + "'>" + response[item].laserMaterialName + "</option>";
            }

            $picker.html(data);

            if ($tr.hasClass('selected')) {
                $selected.html(data);
                $selected.prop('disabled', false);
            }
            $picker.prop('disabled', false);
        } else {
            toastr.error('Brak materiału lasera dla typu materiału: ' + material + ' i grubosci: ' + thickness);
            $picker.html('');
            if ($tr.hasClass('selected')) {
                $selected.html('')
            }
        }
    });
};

$details.on('change', '.material-picker', function () {
    let value = $(this).val();
    let $pickerTr = $(this).parent().parent();
    let $thicknessPicker = $pickerTr.find('.thickness-picker');

    $.ajax({
        'method': 'POST',
        'data': 'material_id=' + value,
        'url': plateMultiPartUrl + "?action=getMaterialThickness"
    }).done(function (response) {
        let thicknesses = JSON.parse(response);
        let html = '';

        thicknesses.forEach(function (item) {
            let selected = '';
            if (item.thickness === $thicknessPicker.val()) {
                selected = 'selected="selected"'
            }
            html += "<option " + selected + " data-material-name='" + item.material_name + "'>" + item.thickness + "</option>";
        });

        if ($pickerTr.hasClass('selected')) {
            $details.find('tr.selected').find('.thickness-picker').html(html);
        }
        $thicknessPicker.html(html);
    });
});

$details.on('change', '.thickness-picker', function () {
    let name = $(this).find(':selected').data('material-name');
});

$("#saveMpwEdit").on('click', function (e) {
    e.preventDefault();

    let data = $("#mpwEdit").serialize();
    App.blockUI();

    $.ajax({
        'data': data,
        'method': 'POST',
        'url': '/index.php?site=30&action=edit'
    }).done(function (response) {
        $.ajax({
            'url': '/multipart/plate/csv/' + dirId + '/'
        }).done(function () {
            window.location.reload();
        });
    }).fail(function () {
        swl("Błąd", "Wystąpił błąd!", "error");
    });
});