$('#dropdownFilter').on('click', function (e) {
    e.stopPropagation();
});

// Variáveis globais
var typingTimer;
var doneTypingInterval = 500; // Tempo em ms para considerar que o usuário parou de digitar

// Função para verificar se a tabela está dentro de um modal
function isInsideModal ($element) {
    // Verifica se algum dos pais do elemento é um modal (Bootstrap, Foundation, etc.)
    return $element.closest('.modal, .modal-dialog, [role="dialog"], .ui-dialog, .fancybox-container').length > 0;
}

// Função para obter a URL base a partir do contexto (modal ou página)
function getBaseUrlFromContext ($element) {
    if (!isInsideModal($element)) {
        return null; // Usa comportamento padrão
    }

    // Procura por elementos com data-url na área do modal
    var $modal = $element.closest('.modal, .modal-dialog, [role="dialog"], .ui-dialog, .fancybox-container');

    // Primeiro tenta encontrar o .per_page com data-url
    var $perPage = $modal.find('.per_page[data-url]').first();
    if ($perPage.length && $perPage.data('url')) {
        var fullUrl = $perPage.data('url');
        var url = new URL(fullUrl, window.location.origin);
        return url.origin + url.pathname;
    }

    // Se não encontrar, tenta encontrar qualquer link de paginação
    var $paginationLink = $modal.find('.pagination a').first();
    if ($paginationLink.length) {
        var paginationUrl = new URL($paginationLink.attr('href'), window.location.origin);
        return paginationUrl.origin + paginationUrl.pathname;
    }

    // Se não encontrar, tenta encontrar um link sortable
    var $sortableLink = $modal.find('.sortable').first();
    if ($sortableLink.length) {
        var sortableUrl = new URL($sortableLink.attr('href'), window.location.origin);
        return sortableUrl.origin + sortableUrl.pathname;
    }

    // Fallback: usa a URL atual
    return window.location.origin + window.location.pathname;
}

// Função para coletar todos os parâmetros de busca do formulário
function getSearchParams ($contextElement = null) {
    // Se temos um elemento de contexto e está em modal, começamos vazio
    // Senão, começa com os parâmetros da URL atual
    let baseParams;
    if ($contextElement && isInsideModal($contextElement)) {
        baseParams = new URLSearchParams();
    } else {
        baseParams = new URLSearchParams(window.location.search);
    }

    // Substitui/adiciona com os valores do formulário
    $('.form-table-search :input').each(function () {
        var name = $(this).attr('name');
        var value = $(this).val();
        var type = $(this).attr('type');

        if (name) {
            if (type === 'checkbox' || type === 'radio') {
                if ($(this).prop('checked')) {
                    baseParams.append(name, value); // cuidado: pode adicionar múltiplos
                } else {
                    baseParams.delete(name); // remove se não estiver checado
                }
            } else if (value !== null && value !== '') {
                baseParams.set(name, value);
            } else {
                baseParams.delete(name); // limpa se o campo estiver vazio
            }
        }
    });

    return baseParams.toString();
}

// Função para busca via texto
$(document).on('keyup', '.form-table-search input[type="text"]', function (e) {
    clearTimeout(typingTimer);
    var $this = $(this);
    typingTimer = setTimeout(() => {
        var baseUrl = getBaseUrlFromContext($this);
        fetchData(1, getSearchParams($this), false, $this, baseUrl); // Volta para a primeira página ao buscar
    }, doneTypingInterval);
});

// Se o usuário ainda estiver digitando (keydown), limpa o timer
$(document).on('keydown', '.form-table-search input[type="text"]', function (e) {
    clearTimeout(typingTimer);
});

// Previne o envio tradicional do formulário de busca
$(document).on('submit', '.form-table-search', function (e) {
    e.preventDefault();
    var $this = $(this);
    var baseUrl = getBaseUrlFromContext($this);
    fetchData(1, getSearchParams($this), false, $this, baseUrl); // Volta para a primeira página ao submeter
});

// Intercepta cliques na paginação
$(document).on('click', '.table-ajax .pagination a', function (e) {
    e.preventDefault();

    var $this = $(this);
    var clickedUrl = new URL($this.attr('href'), window.location.origin);
    var clickedPage = clickedUrl.searchParams.get('page');

    // Se estiver em modal, preserva a URL base da paginação
    if (isInsideModal($this)) {
        var formParams = new URLSearchParams(getSearchParams($this));

        // Adiciona todos os parâmetros da URL clicada, exceto page que já temos
        clickedUrl.searchParams.forEach(function (value, key) {
            if (key !== 'page') {
                formParams.set(key, value);
            }
        });

        formParams.set('page', clickedPage);

        // Passa a URL base da paginação para o fetchData
        var baseUrl = clickedUrl.origin + clickedUrl.pathname;
        fetchData(clickedPage, formParams.toString(), false, $this, baseUrl);
    } else {
        // Comportamento original para páginas normais
        fetchData(clickedPage, getSearchParams($this), false, $this);
        $('html, body').animate({ scrollTop: 0 }, 200);
    }
});

// Intercepta cliques nos cabeçalhos de ordenação
$(document).on('click', '.table-ajax .sortable', function (e) {
    e.preventDefault();

    var $this = $(this);
    var url = $this.attr('href');
    var sortParams = '';
    if (url.indexOf('sort=') > -1) {
        sortParams = url.split('?')[1]; // Pega toda a query string de ordenação
    }

    var allSearchParams = new URLSearchParams(getSearchParams($this));
    var sortURLParams = new URLSearchParams(sortParams);

    // Mantém os parâmetros de ordenação
    sortURLParams.forEach(function (value, key) {
        allSearchParams.set(key, value);
    });

    // Se estiver em modal, usa a URL base do próprio link clicado
    var baseUrl = null;
    if (isInsideModal($this)) {
        var clickedUrl = new URL(url, window.location.origin);
        baseUrl = clickedUrl.origin + clickedUrl.pathname;
    }

    fetchData(1, allSearchParams.toString(), false, $this, baseUrl); // Volta para a primeira página ao ordenar
});

// Quando .per_page muda, recarrega com o novo parâmetro
$(document).on('change', '.per_page', function () {
    var $this = $(this);
    var selectedPerPage = $this.val();

    var dataUrl = new URL($this.data('url'), window.location.origin);
    var dataUrlParams = new URLSearchParams(dataUrl.search);

    // Junta parâmetros do formulário
    var formParams = new URLSearchParams(getSearchParams($this));

    // Mescla os parâmetros de data-url (caso não estejam no formulário)
    dataUrlParams.forEach(function (value, key) {
        if (!formParams.has(key)) {
            formParams.set(key, value);
        }
    });

    // Atualiza ou adiciona per_page
    formParams.set('per_page', selectedPerPage);

    var baseUrl = dataUrl.origin + dataUrl.pathname;

    fetchData(1, formParams.toString(), false, $this, baseUrl);

    if (!isInsideModal($this)) {
        $('html, body').animate({ scrollTop: 0 }, 200);
    }
});


$(document).on('click', '.close-form-filter', function (e) {
    var $this = $(this);
    var baseUrl = getBaseUrlFromContext($this);
    fetchData(1, '', false, $this, baseUrl); // Ao fechar o filtro, volta para a página 1 sem parâmetros de filtro
    $('.close-form-filter').hide();
    $('.form-table-search').trigger('reset');
});

$(document).on('change', '.form-table-search select', function (e) {
    clearTimeout(typingTimer);
    var $this = $(this);
    typingTimer = setTimeout(() => {
        var baseUrl = getBaseUrlFromContext($this);
        fetchData(1, getSearchParams($this), false, $this, baseUrl); // Recarrega com todos os parâmetros do formulário
    }, doneTypingInterval);
});

function fetchData (page, additionalParams = '', scrollTop = false, $triggerElement = null, customBaseUrl = null) {
    const params = new URLSearchParams(additionalParams);

    // Garante que só exista um "page"
    params.set('page', page);

    // Verifica se está dentro de um modal
    const isModal = $triggerElement ? isInsideModal($triggerElement) : false;

    // Usa URL customizada se fornecida (para casos de modal), senão usa a URL atual
    let baseUrl;
    if (customBaseUrl) {
        baseUrl = customBaseUrl;
    } else {
        baseUrl = window.location.origin + window.location.pathname;
    }

    const url = `?${params.toString()}`;
    const fullUrl = baseUrl + url;
    const hasFilterParams = params.toString().length > 0 && params.toString() !== 'page=1';

    $.ajax({
        url: fullUrl,
        type: 'get',
        beforeSend: function () {
            KTApp.showPageLoading();
        }
    }).done(function (data) {
        $('#posts-container').html(data);
        KTApp.hidePageLoading();

        // Só atualiza a URL se NÃO estiver em modal
        if (!isModal) {
            window.history.pushState('', '', fullUrl);
        }

        $('.close-form-filter').toggle(hasFilterParams); // Mostra/oculta o botão de fechar filtro

        if (scrollTop && !isModal) {
            $('html, body').animate({ scrollTop: 0 }, 200); // Mantendo a opção de scroll para o topo
        }
    }).fail(function () {
        KTApp.hidePageLoading();
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Não foi possível carregar os dados.'
        });
    });
}