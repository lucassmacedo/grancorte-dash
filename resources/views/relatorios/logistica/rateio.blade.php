<x-default-layout>
    @section('breadcrumbs-actions')
        <div>
            <a class="btn btn-success btn-sm" id="btn-exportar" href="{{ request()->fullUrl() . '&exportar=excel' }}">
                <i class="fa fa-file-excel"></i>
                Exportar EXCEL
            </a>
        </div>
    @endsection

    @section('title')
        Atualizar Produtos
    @endsection


    <div class="card card-flush mb-5">
        <div class="card-body ">
            <!--begin::Card title-->

            <form action="" method="get">
                <div class="row">
                    <div class="form-group col-5 col-sm-2">
                        <label for="nome" class="required">Filial</label>
                        {{ Form::select('cod_filial', \App\Models\Filial::listFiliais(), $data['cod_filial'],['class' => 'form-control  ' . ($errors->has('cod_filial') ? ' is-invalid ' : ''),'data-control'=>'select2','placeholder'=>'Todos']) }}
                        @if($errors->has('cod_filial'))
                            <span class="invalid-feedback">
                                <i class="fa fa-fw fa-triangle-exclamation"></i>
                                {{ $errors->first('cod_filial') }}
                            </span>
                        @endif
                    </div>
                    <div class="form-group col-9 col-sm-2">
                        <label for="nome" class="required">Período</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-calendar fs-2"></i></span>
                            {{ Form::text('periodo', $data['periodo'],['class' => 'form-control  ' . ($errors->has('periodo') ? ' is-invalid ' : ''),'placeholder'=>'Escolha um período','id'=>'kt_ecommerce_report_sales_daterangepicker']) }}
                            {{--                            <input class="form-control" placeholder="Escolha um período" id="kt_ecommerce_report_sales_daterangepicker"/>--}}
                        </div>
                    </div>
                    <div class="form-group col-5 col-sm-2">
                        <label for="nome" class="required">Agrupador</label>
                        {{ Form::select('cod_agrupador', \App\Models\VLogisticaRoterizacaoRateio::$agrupadores, $data['cod_agrupador'],['class' => 'form-control  ' . ($errors->has('cod_agrupador') ? ' is-invalid ' : '')]) }}
                        @if($errors->has('cod_agrupador'))
                            <span class="invalid-feedback">
                                <i class="fa fa-fw fa-triangle-exclamation"></i>
                                {{ $errors->first('cod_agrupador') }}
                            </span>
                        @endif
                    </div>
                    <div class="form-group col-12 col-sm-2 pt-6">
                        <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i></button>
                        @if(request()->query())
                            <a class="btn btn-danger" href="{{ route('relatorios.vendas.clientes') }}"><i class="fa fa-trash"></i></a>
                        @endif

                    </div>

                </div>


            </form>

        </div>
    </div>
    <div class="">
        <!--begin::Card header-->
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body pt-0  pb-0">
            <div class="row gy-5 gx-xl-5">
                <!--begin::Col-->
                <div class="col-sm-6 col-xl-2">

                    <!--begin::Card widget 2-->
                    <div class="card">
                        <!--begin::Body-->
                        <div class="card-body d-flex justify-content-between align-items-start flex-column pb-0 pt-0">
                            <!--begin::Icon-->

                            <!--begin::Section-->
                            <div class="d-flex flex-column my-7">
                                <!--begin::Number-->
                                <span class="fw-semibold fs-2x text-gray-800 lh-1 ls-n2">{{ formatMoedaReal($data['items']->sum('peso_total')) }}</span>
                                <!--end::Number-->

                                <!--begin::Follower-->
                                <div class="m-0">
                                        <span class="fw-semibold fs-6 text-gray-500">
                                            Peso Total
                                        </span>
                                </div>
                                <!--end::Follower-->
                            </div>
                            <!--end::Section-->
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Card widget 2-->


                </div>
                <div class="col-sm-6 col-xl-2">

                    <!--begin::Card widget 2-->
                    <div class="card">
                        <!--begin::Body-->
                        <div class="card-body d-flex justify-content-between align-items-start flex-column pb-0 pt-0">
                            <!--begin::Icon-->

                            <!--begin::Section-->
                            <div class="d-flex flex-column my-7">
                                <!--begin::Number-->
                                <span class="fw-semibold fs-2x text-gray-800 lh-1 ls-n2">{{ formatMoedaReal($data['items']->sum('valor_descarga'),true) }}</span>
                                <!--end::Number-->

                                <!--begin::Follower-->
                                <div class="m-0">
                                            <span class="fw-semibold fs-6 text-gray-500">
                                                Valor Valor Descarga
                                            </span>

                                </div>
                                <!--end::Follower-->
                            </div>
                            <!--end::Section-->
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Card widget 2-->
                </div>

                <div class="col-sm-6 col-xl-2 ">

                    <!--begin::Card widget 2-->
                    <div class="card">
                        <!--begin::Body-->
                        <div class="card-body d-flex justify-content-between align-items-start flex-column pb-0 pt-0">
                            <!--begin::Icon-->

                            <!--begin::Section-->
                            <div class="d-flex flex-column my-7">
                                <!--begin::Number-->
                                <span class="fw-semibold fs-2x text-gray-800 lh-1 ls-n2">{{ formatMoedaReal($data['items']->sum('valor_pedagio'),true) }}</span>
                                <!--end::Number-->

                                <!--begin::Follower-->
                                <div class="m-0">
                                            <span class="fw-semibold fs-6 text-gray-500">Valor
                                            </span> Pedágio

                                </div>
                                <!--end::Follower-->
                            </div>
                            <!--end::Section-->
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Card widget 2-->
                </div>
                <div class="col-sm-6 col-xl-2 ">

                    <!--begin::Card widget 2-->
                    <div class="card h-lg-100">
                        <!--begin::Body-->
                        <div class="card-body d-flex justify-content-between align-items-start flex-column pb-0 pt-0">
                            <!--begin::Icon-->

                            <!--begin::Section-->
                            <div class="d-flex flex-column my-7">
                                <!--begin::Number-->
                                <span class="fw-semibold fs-2x text-gray-800 lh-1 ls-n2">{{ formatMoedaReal($data['items']->sum('valor_escolta'),true) }}</span>
                                <!--end::Number-->

                                <!--begin::Follower-->
                                <div class="m-0">
                                            <span class="fw-semibold fs-6 text-gray-500">
                                                Valor Escolta
                                            </span>

                                </div>
                                <!--end::Follower-->
                            </div>
                            <!--end::Section-->
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Card widget 2-->


                </div>

                <div class="col-sm-6 col-xl-2 ">
                    <!--begin::Card widget 2-->
                    <div class="card h-lg-100">
                        <!--begin::Body-->
                        <div class="card-body d-flex justify-content-between align-items-start flex-column pb-0 pt-0">
                            <!--begin::Icon-->

                            <!--begin::Section-->
                            <div class="d-flex flex-column my-7">
                                <!--begin::Number-->
                                <span class="fw-semibold fs-2x text-gray-800 lh-1 ls-n2">{{ formatMoedaReal($data['items']->sum('valor_despesa_extra'),true) }}</span>
                                <!--end::Number-->

                                <!--begin::Follower-->
                                <div class="m-0">
                                            <span class="fw-semibold fs-6 text-gray-500">
                                                Valor Despesa Extra
                                            </span>

                                </div>
                                <!--end::Follower-->
                            </div>
                            <!--end::Section-->
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Card widget 2-->
                </div>
                <div class="col-sm-6 col-xl-2 ">
                    <!--begin::Card widget 2-->
                    <div class="card h-lg-100">
                        <!--begin::Body-->
                        <div class="card-body d-flex justify-content-between align-items-start flex-column pb-0 pt-0">
                            <!--begin::Icon-->

                            <!--begin::Section-->
                            <div class="d-flex flex-column my-7">
                                <!--begin::Number-->
                                <span class="fw-semibold fs-2x text-gray-800 lh-1 ls-n2">{{ formatMoedaReal($data['items']->sum('valor_acrescimo'),true) }}</span>
                                <!--end::Number-->

                                <!--begin::Follower-->
                                <div class="m-0">
                                            <span class="fw-semibold fs-6 text-gray-500">
                                                Valor Acrescimo
                                            </span>

                                </div>
                                <!--end::Follower-->
                            </div>
                            <!--end::Section-->
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Card widget 2-->
                </div>

                <div class="col-sm-6 col-xl-2 ">
                    <!--begin::Card widget 2-->
                    <div class="card h-lg-100">
                        <!--begin::Body-->
                        <div class="card-body d-flex justify-content-between align-items-start flex-column pb-0 pt-0">
                            <!--begin::Icon-->

                            <!--begin::Section-->
                            <div class="d-flex flex-column my-7">
                                <!--begin::Number-->
                                <span class="fw-semibold fs-2x text-gray-800 lh-1 ls-n2">{{ formatMoedaReal($data['items']->sum('valor_desconto'),true) }}</span>
                                <!--end::Number-->

                                <!--begin::Follower-->
                                <div class="m-0">
                                <span class="fw-semibold fs-6 text-gray-500">
                                    Valor Desconto
                                </span>
                                </div>
                                <!--end::Follower-->
                            </div>
                            <!--end::Section-->
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Card widget 2-->
                </div>
                <div class="col-sm-6 col-xl-2 ">
                    <!--begin::Card widget 2-->
                    <div class="card h-lg-100">
                        <!--begin::Body-->
                        <div class="card-body d-flex justify-content-between align-items-start flex-column pb-0 pt-0">
                            <!--begin::Icon-->

                            <!--begin::Section-->
                            <div class="d-flex flex-column my-7">
                                <!--begin::Number-->
                                <span class="fw-semibold fs-2x text-gray-800 lh-1 ls-n2">{{ formatMoedaReal($data['items']->sum('valor_total_carga'),true) }}</span>
                                <!--end::Number-->

                                <!--begin::Follower-->
                                <div class="m-0">
                                        <span class="fw-semibold fs-6 text-gray-500">
                                            Valor Total Carga
                                        </span>
                                </div>
                                <!--end::Follower-->
                            </div>
                            <!--end::Section-->
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Card widget 2-->
                </div>

                <!--end::Col-->
            </div>
        </div>
    </div>
    <div class="card card-flush mt-8">
        <!--begin::Card header-->
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body pt-0">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_ecommerce_report_sales_table">
                <!--begin::Table head-->
                <thead>
                <!--begin::Table row-->
                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                    @switch($data['cod_agrupador'])
                        @case('filial')
                            <th class="min-w-100px">@sortablelink('filial', 'Filial', ['parameter' => 'smile'], ['rel' => 'nofollow'])</th>
                            @break
                        @case('transportadora')
                            <th class="min-w-100px">@sortablelink('transportadora', 'Transportadora', ['parameter' => 'smile'], ['rel' => 'nofollow'])</th>
                            @break
                    @endswitch

                    <th class="text-center min-w-75px">@sortablelink('peso_total', 'Peso Total', ['parameter' => 'smile'], ['rel' => 'nofollow'])</th>
                    <th class="text-center min-w-75px">@sortablelink('valor_descarga', 'Valor Descarga', ['parameter' => 'smile'], ['rel' => 'nofollow'])</th>
                    <th class="text-center min-w-75px">@sortablelink('valor_pedagio', 'Valor Pedágio', ['parameter' => 'smile'], ['rel' => 'nofollow'])</th>
                    <th class="text-center min-w-75px">@sortablelink('valor_escolta', 'Valor Escolta', ['parameter' => 'smile'], ['rel' => 'nofollow'])</th>
                    <th class="text-center min-w-75px">@sortablelink('valor_despesa_extra', 'Valor Despesa Extra', ['parameter' => 'smile'], ['rel' => 'nofollow'])</th>
                    <th class="text-center min-w-75px">@sortablelink('valor_acrescimo', 'Valor Acrescimo', ['parameter' => 'smile'], ['rel' => 'nofollow'])</th>
                    <th class="text-center min-w-75px">@sortablelink('valor_desconto', 'Valor Desconto', ['parameter' => 'smile'], ['rel' => 'nofollow'])</th>
                    <th class="text-center min-w-75px">@sortablelink('valor_total_carga', 'Valor Total Carga', ['parameter' => 'smile'], ['rel' => 'nofollow'])</th>
                </tr>
                <!--end::Table row-->
                </thead>
                <!--end::Table head-->
                <!--begin::Table body-->
                <tbody class="fw-semibold text-gray-600">
                <!--begin::Table row-->
                @foreach($data['items'] as $item)
                    <tr>
                        <td>{{$item->agrupador}}</td>
                        <td class="text-center">{{formatMoedaReal($item->peso_total)}}</td>
                        <td class="text-center">{{formatMoedaReal($item->valor_descarga,true)}}</td>
                        <td class="text-center">{{formatMoedaReal($item->valor_pedagio,true)}}</td>
                        <td class="text-center">{{formatMoedaReal($item->valor_escolta,true)}}</td>
                        <td class="text-center">{{formatMoedaReal($item->valor_despesa_extra,true)}}</td>
                        <td class="text-center">{{formatMoedaReal($item->valor_acrescimo,true)}}</td>
                        <td class="text-center">{{formatMoedaReal($item->valor_desconto,true)}}</td>
                        <td class="text-center">{{formatMoedaReal($item->valor_total_carga,true)}}</td>
                    </tr>
                @endforeach
                <!--end::Table row-->
                </tbody>
                <!--end::Table body-->
            </table>
            <div class="d-none flex-sm-fill d-sm-flex align-items-sm-center justify-content-sm-between">
                <div>
                    <p class="small text-muted">
                        Mostrando <span class="fw-semibold">{{ count($data['items']) }}</span> resultados
                    </p>
                </div>
            </div>
            <!--end::Table-->
        </div>
        <!--end::Card body-->
    </div>
    <!-- Modal-->
    @section('scripts')
        <script>

            $(document).ready(function () {
                "use strict";


                var initDaterangepicker = () => {
                    var start = moment().subtract(29, "days");
                    var end = moment();
                    var input = $("#kt_ecommerce_report_sales_daterangepicker");


                    input.daterangepicker({
                        locale: {
                            "format": "DD/MM/YYYY",
                            "separator": " - ",
                            "applyLabel": "Aplicar",
                            "cancelLabel": "Cancelar",
                            "fromLabel": "De",
                            "toLabel": "Até",
                            "customRangeLabel": "Personalizar",
                            "daysOfWeek": [
                                "Dom",
                                "Seg",
                                "Ter",
                                "Qua",
                                "Qui",
                                "Sex",
                                "Sáb"
                            ],
                            "monthNames": [
                                "Janeiro",
                                "Fevereiro",
                                "Março",
                                "Abril",
                                "Maio",
                                "Junho",
                                "Julho",
                                "Agosto",
                                "Setembro",
                                "Outubro",
                                "Novembro",
                                "Dezembro"
                            ],
                            "firstDay": 0
                        },

                        ranges: {
                            "Hoje": [moment(), moment()],
                            "Ontem": [moment().subtract(1, "days"), moment().subtract(1, "days")],
                            "Últimos 7 Dias": [moment().subtract(6, "days"), moment()],
                            "Últimos 30 dias": [moment().subtract(29, "days"), moment()],
                            "Este Mês": [moment().startOf("month"), moment().endOf("month")],
                            "Últimos Mês": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
                        }
                    });

                }

                initDaterangepicker();
            });

        </script>
    @endsection

</x-default-layout>
