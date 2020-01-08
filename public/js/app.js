$(document).ready(function() {

    $('form.deleteItem').click(function (e) {
        var thisForm = this;
        var url = $(this).attr('href');
        bootbox.confirm({
            title: "Delete Item",
            message: "Are you sure you want to delete this item ?",
            buttons: {
                cancel: {
                    label: '<i class="fa fa-times"></i> Cancel'
                },
                confirm: {
                    label: '<i class="fa fa-check"></i> Confirm'
                }
            },
            callback: function (result) {
                if (result) {
                    // window.location.href=url;
                    thisForm.submit();
                }
            }
        });

        e.preventDefault();
    });


});