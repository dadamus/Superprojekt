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

setMaterialName = function ($tr) {
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
    loadLaserMaterial($tr, material, thickness);
});

$details.on('change', '.material-picker', function () {
    let material = $(this).find(':selected').text().trim();
    let $tr = $(this).closest('tr');
    let thickness = $tr.find('.thickness-picker').val();
    loadLaserMaterial($tr, material, thickness);
});

let loadLaserMaterial = function ($tr, material, thickness) {
    $.ajax({
        'method': 'POST',
        'data': 'material=' + material + "&thickness=" + thickness,
        'url': plateMultiPartUrl + "?action=getMaterialLaser"
    }).done(function (responseData) {
        let response = JSON.parse(responseData);
        if (response.length > 0) {
            $tr.find('.material-name').html(response[0]['MaterialName']);
            $tr.find('.laser-material-name-picker').html(function () {
                console.log(response);
                let data = '';
                for (let item in response) {
                    data += "<option value='" + response[item].ccId + "'>" + response[item].laserMaterialName + "</option>";
                }
                return data;
            });
        }
    });
};

$details.on('change', '.material-picker', function () {
    let value = $(this).val();
    let $pickerTr = $(this).parent().parent();
    $.ajax({
        'method': 'POST',
        'data': 'material_id=' + value,
        'url': plateMultiPartUrl + "?action=getMaterialThickness"
    }).done(function (response) {
        let thicknesses = JSON.parse(response);
        let html = '';

        thicknesses.forEach(function (item) {
            html += "<option data-material-name='" + item.material_name + "'>" + item.thickness + "</option>";
        });

        $pickerTr.find('.thickness-picker').html(html);
    });
});

$details.on('change', '.thickness-picker', function () {
    let name = $(this).find(':selected').data('material-name');
    $(this).parent().parent().find('.material-name').html(name);
});

$("#saveMpwEdit").on('click', function (e) {
    e.preventDefault();

    let data = $("#mpwEdit").serialize();
    App.blockUI();

    $.ajax({
        'url': '/multipart/plate/csv/' + dirId + '/'
    });

    $.ajax({
        'data': data,
        'method': 'POST',
        'url': '/index.php?site=30&action=edit'
    }).done(function (response) {
        //window.location.reload();
    }).fail(function () {
        swl("Błąd", "Wystąpił błąd!", "error");
    });
});