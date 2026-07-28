function showSwalSuccess(title, text) {
    swal({
        title: title,
        text: text,
        buttonsStyling: false,
        confirmButtonClass: "btn btn-success",
        type: "success",
        confirmButtonText: "باشه",
        timer: 1500
    });
}
function swalError(title, text) {
    swal({
        title: title,
        text: text,
        buttonsStyling: false,
        confirmButtonClass: "btn btn-success",
        type: "error",
        confirmButtonText: "باشه",
        timer: 1500
    });
}

$(function (e) {
    $('body').on('click', '.swal_confirm', function (e) {
        e.preventDefault();
        let $label = $(this).data('label');
        let $id = $(this).data('id');
        let $action = $(this).data('action');
        swal({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false,
                title: "از حذف این مورد اطمینان دارید؟",
                text: "آیا می‌خواهید " + $label + " را حذف کنید؟",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn btn-danger",
                confirmButtonText: "بله حذف شود",
                cancelButtonText: "خیر",
                closeOnConfirm: true
            },
            function () {
                Livewire.dispatch($action, {model: $id});
                //show loading animation
            });
    });
});
$(function (e) {
    $('body').on('click', '.delete_confirm_alert', function (e) {
        e.preventDefault();
        let $label = $(this).data('label');
        let $id = $(this).data('id');
        swal({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false,
                title: "از حذف این مورد اطمینان دارید؟",
                text: "آیا می‌خواهید " + $label + " را حذف کنید؟",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn btn-danger",
                confirmButtonText: "بله حذف شود",
                cancelButtonText: "خیر",
                closeOnConfirm: true
            },
            function () {
                Livewire.dispatch('delete', {model: $id});
                //show loading animation

            });
    });

    $('.admin_sweet_alert').click(function (e) {
        e.preventDefault();
        let $title = $(this).data('title');
        let $description = $(this).data('description');
        let $type = $(this).data('type');
        $('body').removeClass('timer-alert');
        swal({
            title: "" + $title,
            text: "" + $description,
            type: "" + $type,
            showCancelButton: true,
            allowOutsideClick: true,
            showConfirmButton: false,
            cancelButtonText: "متوجه شدم",
            closeOnConfirm: false
        });
    });
});
