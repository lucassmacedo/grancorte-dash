$(document).on('click', '[data-open-model-ajax]', function (e) {
    e.preventDefault();
    let modal_name = 'generalModal';
    let content = 'modal_content';
    const _this = $(this);

    content = $('#' + modal_name + ' #' + content);
    content.html('');

    $.get($(this).data('url'), function (data) {
        $(content).html(data, modal_name);
    }).done(function () {
        $('#' + modal_name).modal('show');
    }).fail(function (xhr) {
        $('#' + modal_name).modal('hide');
    });
});

$(document).on('submit', '[data-modal-form-submit]', function (e) {
    e.preventDefault();
    $(this).find('button[type=submit]').prop('disabled', true).prepend('<i class="fa fa-spinner fa-spin"></i>');
    $(this).find('button[type=button]').prop('disabled', true);

    const form = $(this);

    $.ajax({
        url: $(this).attr('action'),
        type: $(this).attr('method'),
        data: $(this).serialize()
    }).always(function (response) {
        // Check for errors.
        if (response.status === 422) {
            processFormResponse(form, response);
        } else {
            $('#generalModal').modal('hide');
            location.reload();
        }

        form.find('button[type=submit]').prop('disabled', false).find('.fa-spinner').remove();
        form.find('button[type=button]').prop('disabled', false);
    });
});

function processFormResponse (form, response) {
    let response_body = $.parseJSON(response.responseText);
    let errors = response_body.errors;

    form.find('.invalid-feedback').remove();
    form.find('.is-invalid').removeClass('is-invalid');

    $.each(errors, function (field, message) {
        let formControl = $('[name=' + field + ']', form).addClass('is-invalid');

        if (formControl.parent().hasClass('form-group')) {
            formControl.closest('.form-group').append('<div class="invalid-feedback">' + message + '</div>');
        } else {
            formControl.after('<div class="invalid-feedback">' + message + '</div>');
        }

        let formGroup = formControl.closest('.form-group');
        formGroup.addClass('has-error');
    });
    form.find('button[type=submit]').prop('disabled', false).find('#loader').remove();
    form.find('button[type=button]').prop('disabled', false);
}