<x-default-layout>
    @section('breadcrumbs-actions')
        {{--            <livewire:produto-update/>--}}
    @endsection
    @section('title')
        Atualizar Produtos
    @endsection
    <div class="card card-flush mb-10">
        <div class="card-body ">
            <!--begin::Card title-->

            <form action="" method="get">
                <div class="row">

                    <div class="form-group col-5 col-sm-2">
                        <label for="nome" class="required">Filial</label>
                        {{ Form::select('cod_filial', \App\Models\Filial::listFiliais(), $data['cod_filial'],['class' => 'form-control  ' . ($errors->has('cod_filial') ? ' is-invalid ' : ''),'data-control'=>'select2','placeholder'=>'Selecione uma filial']) }}
                        @if($errors->has('cod_filial'))
                            <span class="invalid-feedback">
                                <i class="fa fa-fw fa-triangle-exclamation"></i>
                                {{ $errors->first('cod_filial') }}
                            </span>
                        @endif
                    </div>

                    @hasanyrole('admin|supervisor')
                    <div class="form-group col-9 col-sm-2">
                        <label for="nome" class="required">Vendedor</label>
                        {{ Form::select('cod_vendedor', \App\Models\User::vendedores(), $data['cod_vendedor'],['class' => 'form-control  ' . ($errors->has('cod_vendedor') ? ' is-invalid ' : ''),'data-control'=>'select2','placeholder'=>'Selecione um vendedor']) }}
                        @if($errors->has('cod_vendedor'))
                            <span class="invalid-feedback">
                                <i class="fa fa-fw fa-triangle-exclamation"></i>
                                {{ $errors->first('cod_vendedor') }}
                            </span>
                        @endif
                    </div>
                    @endhasanyrole

                    @hasanyrole('admin')
                    <div class="form-group col-9 col-sm-2">
                        <label for="nome" class="required">Supervisor</label>
                        {{ Form::select('cod_supervisor', \App\Models\User::supervisores(), $data['cod_supervisor'],['class' => 'form-control  ' . ($errors->has('cod_supervisor') ? ' is-invalid ' : ''),'data-control'=>'select2','placeholder'=>'Selecione um supervisor']) }}
                        @if($errors->has('cod_supervisor'))
                            <span class="invalid-feedback">
                                <i class="fa fa-fw fa-triangle-exclamation"></i>
                                {{ $errors->first('cod_supervisor') }}
                            </span>
                        @endif
                    </div>
                    @endhasanyrole


                    <div class="form-group col-9 col-sm-2">
                        <label for="nome" class="required">Período</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-calendar fs-2"></i></span>
                            {{ Form::text('periodo', $data['periodo'],['class' => 'form-control  ' . ($errors->has('periodo') ? ' is-invalid ' : ''),'placeholder'=>'Escolha um período','id'=>'kt_ecommerce_report_sales_daterangepicker']) }}
                            {{--                            <input class="form-control" placeholder="Escolha um período" id="kt_ecommerce_report_sales_daterangepicker"/>--}}
                        </div>
                    </div>

                    <div class="form-group col-12 col-sm-2">
                        <label for="city_id" class="required">Cidade</label>
                        {{ Form::select('city_id',$cities, $data['city_id'], ['data-url'=>route('api.cities.search'),'class' => 'form-control' . ($errors->has('city_id') ? ' is-invalid ' : ''), 'id' => 'city_id','placeholder'=>'Selecione  uma cidade','autocomplete'=>'off']) }}
                        @if ($errors->has('city_id'))
                            <span class="invalid-feedback">
                            <i class="fa fa-fw fa-triangle-exclamation"></i>
                            {{ $errors->first('city_id') }}
                        </span>
                        @endif
                    </div>


                    <div class="form-group col-12 col-sm-2 pt-6">

                        <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i></button>
                        @if(request()->query())
                            <a class="btn btn-danger" href="{{ route('relatorios.vendas.vendedores') }}"><i class="fa fa-trash"></i></a>
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
            <div class="row gy-5 gx-xl-10">
                <!--begin::Col-->
                <div class="col-sm-6 col-xl-3 mb-xl-10">

                    <!--begin::Card widget 2-->
                    <div class="card">
                        <!--begin::Body-->
                        <div class="card-body d-flex justify-content-between align-items-start flex-column">
                            <!--begin::Icon-->

                            <!--begin::Section-->
                            <div class="d-flex flex-column my-7">
                                <!--begin::Number-->
                                <span class="fw-semibold fs-3x text-gray-800 lh-1 ls-n2">{{ formatMoedaReal($dashboard_geral->sum('valor_liquido'),true) }}</span>
                                <!--end::Number-->

                                <!--begin::Follower-->
                                <div class="m-0">
                                        <span class="fw-semibold fs-6 text-gray-500">
                                            Valor total de vendas
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
                <div class="col-sm-6 col-xl-3 mb-xl-10">

                    <!--begin::Card widget 2-->
                    <div class="card">
                        <!--begin::Body-->
                        <div class="card-body d-flex justify-content-between align-items-start flex-column">
                            <!--begin::Icon-->

                            <!--begin::Section-->
                            <div class="d-flex flex-column my-7">
                                <!--begin::Number-->
                                <span class="fw-semibold fs-3x text-gray-800 lh-1 ls-n2">{{ formatMoedaReal($dashboard_geral->sum('valor_medio'),true) }}</span>
                                <!--end::Number-->

                                <!--begin::Follower-->
                                <div class="m-0">
                                        <span class="fw-semibold fs-6 text-gray-500">
                                            Valor Médio das Vendas
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
                <div class="col-sm-6 col-xl-3 mb-xl-10">

                    <!--begin::Card widget 2-->
                    <div class="card h-lg-100">
                        <!--begin::Body-->
                        <div class="card-body d-flex justify-content-between align-items-start flex-column">
                            <!--begin::Icon-->

                            <!--begin::Section-->
                            <div class="d-flex flex-column my-7">
                                <!--begin::Number-->
                                <span class="fw-semibold fs-3x text-gray-800 lh-1 ls-n2">{{ $dashboard_geral->sum('notas') }}</span>
                                <!--end::Number-->

                                <!--begin::Follower-->
                                <div class="m-0">
                                        <span class="fw-semibold fs-6 text-gray-500">
                                            Total de Notas
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
                <div class="col-sm-6 col-xl-3 mb-xl-10">

                    <!--begin::Card widget 2-->
                    <div class="card h-lg-100">
                        <!--begin::Body-->
                        <div class="card-body d-flex justify-content-between align-items-start flex-column">
                            <!--begin::Icon-->

                            <!--begin::Section-->
                            <div class="d-flex flex-column my-7">
                                <!--begin::Number-->
                                <span class="fw-semibold fs-3x text-gray-800 lh-1 ls-n2">{{ $dashboard_geral->sum('clientes') }}</span>
                                <!--end::Number-->

                                <!--begin::Follower-->
                                <div class="m-0">
                                        <span class="fw-semibold fs-6 text-gray-500">
                                            Total de Clientes
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
    <div class="card card-flush">
        <!--begin::Card header-->
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body pt-0">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_ecommerce_report_sales_table">
                <!--begin::Table head-->
                <thead>
                <!--begin::Table row-->
                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                    <th class="min-w-100px">@sortablelink('cod_vendedor', 'Vendedor', ['parameter' => 'smile'], ['rel' => 'nofollow'])</th>
                    <th class="text-center min-w-75px">@sortablelink('valor_liquido', 'Valor Total', ['parameter' => 'smile'], ['rel' => 'nofollow'])</th>
                    <th class="text-center min-w-75px">@sortablelink('valor_medio', 'Valor Médio', ['parameter' => 'smile'], ['rel' => 'nofollow'])</th>
                    <th class="text-center min-w-75px">@sortablelink('notas', 'Total de Notas', ['parameter' => 'smile'], ['rel' => 'nofollow'])</th>
                    <th class="text-center min-w-75px">@sortablelink('clientes', 'Total de Clientes', ['parameter' => 'smile'], ['rel' => 'nofollow'])</th>
                    {{--                    <th class="text-center min-w-75px">Valor Liquido</th>--}}
                    {{--                    <th class="text-center min-w-75px">Cancelado</th>--}}
                    {{--                    <th class="text-center min-w-100px"></th>--}}
                </tr>
                <!--end::Table row-->
                </thead>
                <!--end::Table head-->
                <!--begin::Table body-->
                <tbody class="fw-semibold text-gray-600">
                <!--begin::Table row-->
                @foreach($dashboard as $item)
                    <tr>
                        <!--begin::Date=-->
                        <td>{{$item->cod_vendedor}} - {{$item->vendedor}}</td>
                        <td class="text-center">{{formatMoedaReal($item->valor_liquido,true)}}</td>
                        <td class="text-center">{{formatMoedaReal($item->valor_medio,true)}}</td>
                        <td class="text-center">{{$item->notas}}</td>
                        <td class="text-center">{{$item->clientes}}</td>
                        {{--                        <td class="text-center">{{formatMoedaReal($item->valor_cancelada,true)}}</td>--}}
                    </tr>

                @endforeach
                <!--end::Table row-->
                </tbody>
                <!--end::Table body-->
            </table>
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
