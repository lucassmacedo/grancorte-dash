<!--begin::sidebar menu-->
<div class="app-sidebar-menu overflow-hidden flex-column-fluid">
    <!--begin::Menu wrapper-->
    <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper hover-scroll-overlay-y my-5" data-kt-scroll="true"
        data-kt-scroll-activate="true" data-kt-scroll-height="auto"
        data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
        data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px">
        <!--begin::Menu-->
        <div class="menu menu-column menu-rounded menu-sub-indention px-3" id="#kt_app_sidebar_menu" data-kt-menu="true"
            data-kt-menu-expand="false">
            <!--begin:Menu item-->
            <div class="menu-item pt-5">
                <!--begin:Menu content-->
                <div class="menu-content">
                    <span class="menu-heading fw-semibold text-uppercase fs-7">Menu</span>
                </div>
                <!--end:Menu content-->
            </div>
            <!--end:Menu item-->
            <a class="menu-item menu-accordion" href="{{ route('home') }}">
                <!--begin:Menu link-->
                <span class="menu-link {{ Route::is('home') ? 'active' : '' }}">
                    <span class="menu-icon">{!! getSvgIcon('duotune/general/gen025.svg', 'svg-icon svg-icon-2') !!}</span>
                    <span class="menu-title">Dashboard</span>
                </span>
            </a>
            @hasanyrole(['admin', 'supervisor','Supervisor Vendedores'])

                <a class="menu-item menu-accordion" href="{{ route('relatorios.estoque.saldo-tunel') }}">
                    <!--begin:Menu link-->
                    <span class="menu-link {{ Route::is('relatorios.estoque.saldo-tunel') ? 'active' : '' }}">
                        <span class="menu-icon"><i class="fa fa-warehouse"></i></span>
                        <span class="menu-title">Estoque</span>
                    </span>
                </a>
            @endhasanyrole

            <!--begin:Menu item-->
            @hasanyrole(['admin', 'supervisor'])
                <a class="menu-item menu-accordion" href="{{ route('users.index') }}">
                    <!--begin:Menu link-->
                    <span class="menu-link {{ Route::is('users.*') ? 'active' : '' }}">
                        <span class="menu-icon">{!! getSvgIcon('duotune/communication/com006.svg', 'svg-icon svg-icon-2') !!}</span>
                        <span class="menu-title">Usuários</span>
                    </span>
                </a>
            @endhasanyrole



            @hasanyrole(['admin', 'supervisor', 'vendedor', 'Supervisor Vendedores'])

                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion {{ Route::is('clientes.*') ? 'show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-icon">{!! getSvgIcon('duotune/communication/com005.svg', 'svg-icon svg-icon-2') !!}</span>
                        <span class="menu-title">Clientes</span>
                        <span class="menu-arrow"></span>
                    </span>

                    <div class="menu-sub menu-sub-accordion menu-active-bg">
                        <div class="menu-item">
                            <a class="menu-link {{ Route::is('clientes.index') ? 'active' : '' }}"
                                href="{{ route('clientes.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Lista de Clientes</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a class="menu-link {{ Route::is('clientes.mapa') ? 'active' : '' }}"
                                href="{{ route('clientes.mapa') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Mapa de Clientes</span>
                            </a>
                        </div>
                    </div>
                </div>
                <a class="menu-item menu-accordion" href="{{ route('clientes.pendencias.index') }}">
                    <!--begin:Menu link-->
                    <span class="menu-link {{ Route::is('clientes.pendencias.*') ? 'active' : '' }}">
                        <span class="menu-icon">{!! getSvgIcon('duotune/communication/com009.svg', 'svg-icon svg-icon-2') !!}</span>
                        <span class="menu-title">Pendências Financeiras</span>
                    </span>
                </a>
                <a class="menu-item menu-accordion" href="{{ route('clientes.prospecao.index') }}">
                    <!--begin:Menu link-->
                    <span class="menu-link {{ Route::is('clientes.prospecao.*') ? 'active' : '' }}">
                        <span class="menu-icon">{!! getSvgIcon('duotune/communication/com007.svg', 'svg-icon svg-icon-2') !!}</span>
                        <span class="menu-title">Prospecção</span>
                    </span>
                </a>

                <a class="menu-item menu-accordion" href="{{ route('produtos.index') }}">
                    <!--begin:Menu link-->
                    <span class="menu-link {{ Route::is('produtos.*') ? 'active' : '' }}">
                        <span class="menu-icon">{!! getSvgIcon('duotune/ecommerce/ecm002.svg', 'svg-icon svg-icon-2') !!}</span>
                        <span class="menu-title">Produtos</span>
                    </span>
                </a>
                <a class="menu-item menu-accordion" href="{{ route('pedidos.index') }}">
                    <!--begin:Menu link-->
                    <span
                        class="menu-link {{ Route::is('pedidos.*') && !Route::is('pedidos.corte') && !Route::is('pedidos.faturados.index') ? 'active' : '' }}">
                        <span class="menu-icon">{!! getSvgIcon('duotune/ecommerce/ecm001.svg', 'svg-icon svg-icon-2') !!}</span>
                        <span class="menu-title">Pedidos</span>
                    </span>
                </a>
                <a class="menu-item menu-accordion" href="{{ route('pedidos.faturados.index') }}">
                    <!--begin:Menu link-->
                    <span
                        class="menu-link {{ Route::is('pedidos.faturados.index') && !Route::is('pedidos.faturados.index') ? 'active' : '' }}">
                        <span class="menu-icon">{!! getSvgIcon('duotune/ecommerce/ecm003.svg', 'svg-icon svg-icon-2') !!}</span>
                        <span class="menu-title">Pedidos X Notas</span>
                    </span>
                </a>
            @endhasanyrole


            @hasanyrole(['admin', 'supervisor', 'Supervisor Vendedores'])


                <a class="menu-item menu-accordion" href="{{ route('pedidos.corte') }}">
                    <span class="menu-link {{ Route::is('pedidos.corte') ? 'active' : '' }}">
                        <span class="menu-icon">{!! getSvgIcon('duotune/ecommerce/ecm006.svg', 'svg-icon svg-icon-2') !!}</span>
                        <span class="menu-title">Corte</span>
                    </span>
                </a>
            @endhasanyrole




            @hasanyrole('admin')
                <a class="menu-item menu-accordion" href="{{ route('logs.index') }}">
                    <!--begin:Menu link-->
                    <span class="menu-link {{ Route::is('logs.*') ? 'active' : '' }}">
                        <span class="menu-icon">{!! getSvgIcon('duotune/technology/teh004.svg', 'svg-icon svg-icon-2') !!}</span>
                        <span class="menu-title">Logs</span>
                    </span>
                </a>


                <a class="menu-item menu-accordion" href="{{ route('filiais.index') }}">
                    <!--begin:Menu link-->
                    <span class="menu-link {{ Route::is('filiais.*') ? 'active' : '' }}">
                        <span class="menu-icon">{!! getSvgIcon('duotune/general/gen024.svg', 'svg-icon svg-icon-2') !!}</span>
                        <span class="menu-title">Filiais</span>
                    </span>
                </a>

                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion {{ Route::is('command-executor.*') ? 'show' : '' }}">
                    <!--begin:Menu link-->
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="fa fa-chart-bar"></i>
                        </span>
                        <span class="menu-title">Ferramentas</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <!--end:Menu link--><!--begin:Menu sub-->
                    <div class="menu-sub menu-sub-accordion menu-active-bg">
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <!--begin:Menu link-->

                            <a class="menu-item menu-accordion" href="{{ route('command-executor.index') }}">
                                <!--begin:Menu link-->
                                <span class="menu-link {{ Route::is('command-executor.*') ? 'active' : '' }}">
                                    <span class="menu-icon"><i class="fa fa-terminal"></i></span>
                                    <span class="menu-title">Executor de Comandos</span>
                                </span>
                            </a>
                            <!--end:Menu link-->
                        </div>
                    </div>
                </div>

            @endhasanyrole

            @hasanyrole(['admin', 'supervisor', 'vendedor', 'Supervisor Vendedores', 'logistica'])
                <a class="menu-item menu-accordion" href="{{ route('logistica.index') }}">
                    <!--begin:Menu link-->
                    <span class="menu-link {{ Route::is('logistica.index') ? 'active' : '' }}">
                        <span class="menu-icon"><i class="fa fa-truck"></i></span>
                        <span class="menu-title">Gestão de Entregas</span>
                    </span>
                </a>
            @endhasanyrole


            @hasanyrole(['admin', 'supervisor', 'vendedor', 'Supervisor Vendedores'])
                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion {{ Route::is('relatorios.*') && !Route::is('relatorios.estoque.saldo-tunel') ? 'show' : '' }}">
                    <!--begin:Menu link-->
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="fa fa-chart-bar"></i>
                        </span>
                        <span class="menu-title">Relatórios</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <!--end:Menu link--><!--begin:Menu sub-->
                    <div
                        class="menu-sub menu-sub-accordion  {{ Route::is('relatorios.*') && !Route::is('relatorios.estoque.saldo-tunel') ? 'show' : '' }} menu-active-bg ">

                            <div data-kt-menu-trigger="click" class="menu-item {{ Route::is('relatorios.*') && !Route::is('relatorios.estoque.saldo-tunel') ? 'show' : '' }} menu-accordion">
                                <!--begin:Menu link-->
                                <span class="menu-link">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Faturamento</span>
                                    <span class="menu-arrow"></span>
                                </span>

                                <div
                                    class="menu-sub menu-sub-accordion {{ Route::is('relatorios.*') && !Route::is('relatorios.estoque.saldo-tunel') ? 'show' : '' }}">
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ Route::is('relatorios.vendas.vendedores') ? 'active' : '' }}"
                                            href="{{ route('relatorios.vendas.vendedores') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Vendedores</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ Route::is('relatorios.vendas.produtos') ? 'active' : '' }}"
                                            href="{{ route('relatorios.vendas.produtos') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Produtos</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ Route::is('relatorios.vendas.clientes') ? 'active' : '' }}"
                                            href="{{ route('relatorios.vendas.clientes') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Clientes</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                </div>
                            </div>
                            {{-- <div data-kt-menu-trigger="click" class="menu-item {{ Route::is('relatorios.*') && !Route::is('relatorios.estoque.saldo-tunel') ? 'show' : '' }} menu-accordion">
                                <!--begin:Menu link-->
                                <span class="menu-link">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Recebimento</span>
                                    <span class="menu-arrow"></span>
                                </span>

                                <div
                                    class="menu-sub menu-sub-accordion {{ Route::is('relatorios.*') && !Route::is('relatorios.estoque.saldo-tunel') ? 'show' : '' }}">
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ Route::is('relatorios.recebimento.vendedores') ? 'active' : '' }}"
                                            href="{{ route('relatorios.recebimento.vendedores') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Vendedores</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                </div>
                            </div> --}}
                        @hasanyrole(['admin', 'supervisor', 'Supervisor Vendedores'])

                            <div data-kt-menu-trigger="click" class="menu-item menu-accordion"><!--begin:Menu link-->
                                <span class="menu-link">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Logística</span>
                                    <span class="menu-arrow"></span>
                                </span>

                                <div class="menu-sub menu-sub-accordion menu-active-bg">
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link" href="{{ route('relatorios.logistica.roterizacao') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Fretes (Rateio)</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                @endhasanyrole

                        </div>
                    </div>

            @endhasanyrole

            @hasanyrole(['admin', 'logistica'])

                    <div data-kt-menu-trigger="click"
                        class="menu-item menu-accordion {{ Route::is('logistica.armazens.*') || Route::is('logistica.transportadoras.*') || Route::is('logistica.roterizacao.*') || Route::is('logistica.rotas.*') || Route::is('logistica.veiculos.*') ? 'show' : '' }}">
                        <!--begin:Menu link-->
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="fa fa-shipping-fast"></i>
                            </span>
                            <span class="menu-title">Logística</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <!--end:Menu link--><!--begin:Menu sub-->
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link {{ Route::is('logistica.roterizacao.*') ? 'active' : '' }}"
                                    href="{{ route('logistica.roterizacao.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Roterização</span>
                                </a>
                            </div>
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link {{ Route::is('logistica.rotas.*') ? 'active' : '' }}"
                                    href="{{ route('logistica.rotas.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Rotas</span>
                                </a>
                                <!--end:Menu link-->
                            </div>

                            <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link {{ Route::is('logistica.veiculos.*') ? 'active' : '' }}"
                                    href="{{ route('logistica.veiculos.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Veículos</span>
                                </a>
                                <!--end:Menu link-->
                            </div>
                            <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link {{ Route::is('logistica.transportadoras.*') ? 'active' : '' }}"
                                    href="{{ route('logistica.transportadoras.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Transportadoras</span>
                                </a>
                                <!--end:Menu link-->
                            </div>
                            <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link {{ Route::is('logistica.armazens.*') ? 'active' : '' }}"
                                    href="{{ route('logistica.armazens.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Armazens</span>
                                </a>
                                <!--end:Menu link-->
                            </div>
                        </div>
                    </div>
                @endhasanyrole

            </div>
            <!--end::Menu-->
        </div>
        <!--end::Menu wrapper-->
    </div>
    <!--end::sidebar menu-->
