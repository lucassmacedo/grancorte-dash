$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$(document).on('click', '[data-modal-delete-url]', function () {
    let url_id = $(this).data("modal-delete-url");
    Swal.fire({
        title: "Tem certeza que deseja apagar?",
        html: "<b>O registro será apagado definitivamente.</b>",
        icon: "warning",
        confirmButtonText: "<i class='fa fa-thumbs-up'></i> Sim, apagar!",
        showCancelButton: true,
        cancelButtonText: "<i class='fa fa-thumbs-down'></i> Não, obrigado!",
        confirmButtonColor: '#d33',
        customClass: {
            confirmButton: "btn btn-danger",
            cancelButton: "btn btn-default"
        }
    }).then((data) => {
        if (data.isConfirmed) {
            $.ajax({
                url: url_id,
                type: 'DELETE',
                success: function (data) {
                    Swal.fire(
                        "Registro Apagado!!",
                        "Registro apagado com sucesso!",
                        "success"
                    ).then(function () {
                        location.reload();
                    })

                }, error: function (xhr, ajaxOptions, thrownError) {
                    let data = JSON.parse(xhr.responseText);
                    Swal.fire(
                        "Erro!",
                        data.message,
                        "error"
                    ).then(function () {
                        location.reload();
                    })
                }
            });
        }
    });
});
