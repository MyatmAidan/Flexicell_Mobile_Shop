$(document).ready(function () {
    let token = document.head.querySelector('meta[name="csrf-token"]');
    if (token) {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": token.content,
            },
        });
    } else {
        console.error("CSRF Token not found.");
    }
    $.extend(true, $.fn.dataTable.defaults, {
        processing: true,
        serverSide: true,
        responsive: true,
        mark: true,
        columnDefs: [
            {
                targets: [0],
                class: "control",
            },
            {
                targets: "no-sort",
                orderable: false,
            },
            {
                targets: "no-search",
                searchable: false,
            },
            {
                targets: "hidden",
                visible: false,
            },
        ],
        lengthMenu: [
            [10, 25, 50, 100, 2000, -1],
            ["10 rows", "25 rows", "50 rows", "100 rows", "2000 rows", "All"],
        ],
        language: {
            emptyTable: "No data available in table",
            paginate: {
                previous: '<i class="fa fa-circle-left"></i>',
                next: '<i class="fa fa-circle-right"></i>',
            },
        },
    });
    // Inline Color Toggles (Shared Modal)
    $(document).on('click', '#modal-btn-toggle-new-color', function() {
        $('#modal-existing-color-group').addClass('d-none');
        $('#modal-new-color-group').removeClass('d-none');
        $('#modal_color_option_id').val('');
    });

    $(document).on('click', '#modal-btn-cancel-new-color', function() {
        $('#modal-new-color-group').addClass('d-none');
        $('#modal-existing-color-group').removeClass('d-none');
        $('#modal_new_color_name').val('');
    });
});