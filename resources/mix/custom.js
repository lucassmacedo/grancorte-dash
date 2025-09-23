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

    // Usar FormData para incluir arquivos
    const formData = new FormData(form[0]);

    $.ajax({
        url: $(this).attr('action'),
        type: $(this).attr('method'),
        data: formData,
        // Configurações adicionais necessárias para upload de arquivos
        processData: false,
        contentType: false,
        cache: false
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

$('#filterDataModal').on('show.bs.modal', function (e) {
    if ($('select[data-control="select2"]').length > 0) {
        $('select[data-control="select2"]').select2({
            dropdownParent: $('#filterDataModal')
        });
    }
    if ($('select[name="city_id"], select[name="endereco[city_id]"]').length > 0) {
        $('select[name="city_id"], select[name="endereco[city_id]"]').select2({
            dropdownParent: $('#filterDataModal'),
            language: 'pt-BR',
            minimumInputLength: 3,
            ajax: {
                url: function () {
                    return $(this).data('url');
                },
                type: 'get',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term // search term
                    };
                },
                processResults: function (response) {
                    return {
                        results: response
                    };
                },
                cache: true
            }
        });
    }
});

$(document).on('click', '[data-modal-delete-url]', function () {
    let url_id = $(this).data('modal-delete-url');
    Swal.fire({
        title: 'Tem certeza que deseja apagar?',
        html: '<b>O registro será apagado definitivamente.</b>',
        icon: 'warning',
        confirmButtonText: '<i class=\'fad fa-thumbs-up\'></i> Sim, apagar!',
        showCancelButton: true,
        cancelButtonText: '<i class=\'fad fa-thumbs-down\'></i> Não, obrigado!',
        confirmButtonColor: '#d33',
        customClass: {
            confirmButton: 'btn btn-danger',
            cancelButton: 'btn btn-default'
        }
    }).then((data) => {
        if (data.isConfirmed) {
            $.ajax({
                url: url_id,
                type: 'DELETE',
                success: function (data) {
                    Swal.fire(
                        'Registro Apagado!!',
                        'Registro apagado com sucesso!',
                        'success'
                    ).then(function () {
                        location.reload();
                    });

                }, error: function (xhr, ajaxOptions, thrownError) {
                    let data = JSON.parse(xhr.responseText);
                    Swal.fire(
                        'Erro!',
                        data.message,
                        'error'
                    ).then(function () {
                        location.reload();
                    });
                }
            });
        }
        $('#generalModal').modal('hide');
    });
});

$('[data-peso]').mask('###0.00', { reverse: true });
$('[data-money]').mask('#.##0,00', { reverse: true });
$('[data-cep]').mask('00000-000');
$('[data-cpf]').mask('000.000.000-00', { reverse: true });
$('[data-cnpj]').mask('00.000.000/0000-00', { reverse: true });
let behavior = function (val) {
        return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
    },
    options = {
        onKeyPress: function (val, e, field, options) {
            field.mask(behavior.apply({}, arguments), options);
        }
    };

$('[data-telefone]').mask(behavior, options);

function clearAddressFields () {
    // Limpa os campos de endereço
    $('input[name=\'endereco\'],input[name=\'endereco[endereco]\']').val('');
    $('input[name=\'bairro\']', 'input[name=\'endereco[bairro]\']').val('');
    $('select[name=\'city_id\'], select[name=\'endereco[city_id]\']').val('');
    $('input[name=\'estado\'], input[name=\'endereco[estado]\']').val('');
}

// on read
$(document).ready(function () {

    let city_id = $('select[name=\'city_id\'], select[name=\'endereco[city_id]\']');
    let cep = $('input[name=\'cep\'], input[name=\'endereco[cep]\']');
    if (city_id.length > 0) {
        city_id.select2({
            language: 'pt-BR',
            minimumInputLength: 3,
            ajax: {
                url: function () {
                    return $(this).data('url');
                },
                type: 'get',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term // search term
                    };
                },
                processResults: function (response) {
                    return {
                        results: response
                    };
                },
                cache: true
            }
        });
    }

    if (cep.length > 0) {
        cep.on('blur', function () {
            var cep = $(this).val().replace(/\D/g, '');

            // Verifica se o campo possui valor informado
            if (cep !== '') {

                // Expressão regular para validar o CEP
                var validacep = /^[0-9]{8}$/;

                // Valida o formato do CEP
                if (validacep.test(cep)) {

                    // Preenche os campos com "..." enquanto consulta a API
                    $('input[name=\'endereco\']', 'input[name=\'endereco[endereco]\']').val('...');
                    $('input[name=\'bairro\']', 'input[name=\'endereco[bairro]\']').val('...');
                    $('input[name=\'estado\']', 'input[name=\'endereco[estado]\']').val('...');

                    // Consulta o webservice viacep.com.br/
                    $.getJSON(`https://viacep.com.br/ws/${cep}/json/?callback=?`, function (dados) {

                        if (!('erro' in dados)) {
                            // Atualiza os campos com os valores da consulta
                            $('input[name=\'endereco\']', 'input[name=\'endereco[endereco]\']').val(dados.logradouro);
                            $('input[name=\'bairro\']', 'input[name=\'endereco[bairro]\']').val(dados.bairro);
                            // $('#city_id').val(dados.localidade);
                            // $("input[name='estado).val(dados.uf);

                            let city_idSelectData = {
                                id: dados.ibge,
                                text: `${dados.localidade} - ${dados.uf}`
                            };

                            // Encontrar o select com nome 'city_id' e inicializá-lo com Select2 se ainda não estiver
                            let $selectCity = $('select[name=\'city_id\'], select[name=\'endereco[city_id]\']');
                            if (!$selectCity.data('select2')) {
                                $selectCity.select2();
                            }

                            // Criando uma nova opção com os dados recebidos e selecionando-a
                            let novaOpcao = new Option(city_idSelectData.text, city_idSelectData.id, true, true);
                            $selectCity.append(novaOpcao).trigger('change');

                        } else {
                            // CEP pesquisado não encontrado
                            clearAddressFields();
                            alert('CEP não encontrado.');
                        }
                    });
                } else {
                    clearAddressFields();
                    alert('Formato de CEP inválido.');
                }
            } else {
                clearAddressFields();
            }
        });
    }
});

$('.singleDatePicker').mask("00/00/0000", {reverse: true});
$(".singleDatePicker").datepicker({
    format: "dd/mm/yyyy",
    language: "pt-BR",
    autoclose: true,
    todayHighlight: true
});