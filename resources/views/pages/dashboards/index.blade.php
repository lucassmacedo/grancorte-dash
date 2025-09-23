<x-default-layout>
    @section('title')
        Dashboard
    @endsection

    @section('breadcrumbs-actions')
        <span class="fs-7 fw-bold text-gray-700 flex-shrink-0 pe-4 d-none d-md-block">Filtrar Ano:</span>
        {{ Form::select('ano', rangeYear(2021,date('Y')),$ano,['class'=>'form-select w-125px form-select-solid me-6 dashboard-ano']) }}

            <!--begin::Daterangepicker(defined in src/js/layout/app.js)-->
            <div data-kt-daterangepicker="true" data-kt-daterangepicker-opens="left" class="btn btn-sm fw-bold bg-body btn-color-gray-700 btn-active-color-primary d-flex align-items-center px-4">
                <!--begin::Display range-->
                <div class="text-gray-600 fw-bold">Loading date range...</div>
                <!--end::Display range-->
                <!--begin::Svg Icon | path: icons/duotune/general/gen014.svg-->
                <span class="svg-icon svg-icon-1 ms-2 me-0">
												<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path opacity="0.3" d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z" fill="currentColor" />
													<path d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z" fill="currentColor" />
													<path d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.59999 10.8 8.39999 10.9C8.19999 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.09999 12.4 6.89999 12.4C6.69999 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.49999 10.1 7.89999 10C8.29999 9.90003 8.60001 9.80003 9.10001 9.80003C9.50001 9.80003 9.80001 9.90003 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.10001 16.3 6.10001 16.1C6.10001 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7.00001 15.4 7.10001 15.5C7.20001 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.80001 14.4 9.50001 14.3 9.10001 14.3C9.00001 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.39999 14.3 8.39999 14.3C8.19999 14.3 7.99999 14.2 7.89999 14.1C7.79999 14 7.7 13.8 7.7 13.7C7.7 13.5 7.79999 13.4 7.89999 13.2C7.99999 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.80003 15.9 9.70003C15.9 9.60003 16.1 9.60004 16.3 9.60004C16.5 9.60004 16.7 9.70003 16.8 9.80003C16.9 9.90003 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z" fill="currentColor" />
												</svg>
											</span>
                <!--end::Svg Icon-->
            </div>
    @endsection


    <!--begin::Row-->
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <!--begin::Col-->
        <!--begin::Col-->
        <div class="col-xl-12">
            <!--begin::Chart widget 36-->
            <div class="card card-flush overflow-hidden h-lg-100">
                <!--begin::Header-->
                <div class="card-header pt-5">
                    <!--begin::Title-->
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">Gráfico de Recebimento {{ $ano }}</span>
                        <span class="text-gray-400 mt-1 fw-semibold fs-6">Total de Recebimento
                            <div class="badge badge-light-primary fs-base">
                            R$: {{ formatMoedaReal($data['total']['recebimento']) }}
                            </div>
                        </span>
                    </h3>
                    <!--end::Title-->
                </div>
                <!--end::Header-->
                <!--begin::Card body-->
                <div class="card-body d-flex align-items-end p-0">
                    <!--begin::Chart-->
                    <div id="kt_charts_widget_36" class="min-h-auto w-100 ps-4 pe-6" style="height: 300px"></div>
                    <!--end::Chart-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Chart widget 36-->

        </div>
        <div class="col-xl-12">
            <!--begin::Chart widget 36-->
            <div class="card card-flush overflow-hidden h-lg-100">
                <!--begin::Header-->
                <div class="card-header pt-5">
                    <!--begin::Title-->
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">Gráfico de Previsão de Comissão {{ $ano }}</span>
                        <span class="text-gray-400 mt-1 fw-semibold fs-6">Total de Previsão de Comissão 
                            <div class="badge badge-light-success fs-base">
                            R$: {{ formatMoedaReal($data['total']['comissao']) }}
                            </div>
                        </span>
                    </h3>
                    <!--end::Title-->
                </div>
                <!--end::Header-->
                <!--begin::Card body-->
                <div class="card-body d-flex align-items-end p-0">
                    <!--begin::Chart-->
                    <div id="kt_charts_widget_37" class="min-h-auto w-100 ps-4 pe-6" style="height: 300px"></div>
                    <!--end::Chart-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Chart widget 36-->

        </div>
{{--        <div class="col-xl-12">--}}
{{--            <!--begin::Chart widget 36-->--}}
{{--            <div class="card card-flush overflow-hidden h-lg-100">--}}
{{--                <!--begin::Header-->--}}
{{--                <div class="card-header pt-5">--}}
{{--                    <!--begin::Title-->--}}
{{--                    <h3 class="card-title align-items-start flex-column">--}}
{{--                        <span class="card-label fw-bold text-dark">Gráfico de Faturamento {{ $ano }}</span>--}}
{{--                        <span class="text-gray-400 mt-1 fw-semibold fs-6">Total de Faturamento--}}
{{--                            <div class="badge badge-light-primary fs-base">--}}
{{--                            R$: {{ formatMoedaReal($data['total']['faturamento']) }}--}}
{{--                            </div>--}}
{{--                        </span>--}}
{{--                    </h3>--}}
{{--                    <!--end::Title-->--}}
{{--                </div>--}}
{{--                <!--end::Header-->--}}
{{--                <!--begin::Card body-->--}}
{{--                <div class="card-body d-flex align-items-end p-0">--}}
{{--                    <!--begin::Chart-->--}}
{{--                    <div id="kt_charts_widget_38" class="min-h-auto w-100 ps-4 pe-6" style="height: 300px"></div>--}}
{{--                    <!--end::Chart-->--}}
{{--                </div>--}}
{{--                <!--end::Card body-->--}}
{{--            </div>--}}
{{--            <!--end::Chart widget 36-->--}}

{{--        </div>--}}
        <div class="row g-5 g-xl-8">
            <div class="col-xl-4">
                <!--begin::Mixed Widget 17-->
                <div class="card card-xl-stretch mb-xl-8">
                    <!--begin::Body-->
                    <div class="card-body pt-5">
                        <!--begin::Heading-->
                        <div class="d-flex flex-stack">
                            <!--begin::Title-->
                            <h4 class="fw-bold text-gray-800 m-0">Percentual de Entregas</h4>
                            <!--end::Title-->
                            <!--begin::Menu-->
                            <!--begin::Menu 3-->
                            <!--end::Menu 3-->
                            <!--end::Menu-->
                        </div>
                        <!--end::Heading-->
                        <!--begin::Chart-->
                        <div class="d-flex flex-center w-100">
                            <div class="mixed-widget-17-chart" data-kt-chart-color="success" style="height: 400px"></div>
                        </div>
                    </div>
                    <!--end::Body-->
                    <!--begin::Footer-->
                    <div class="card-footer d-flex flex-center py-5 mt-n10">
                        <!--begin::Item-->
                        <div class="d-flex align-items-center flex-shrink-0 me-7 me-lg-12">
                            <!--begin::Bullet-->
                            <span class="bullet bullet-dot bg-primary me-2 h-10px w-10px"></span>
                            <!--end::Bullet-->
                            <!--begin::Label-->
                            <span class="fw-semibold text-gray-400 fs-6">{{ $entregas->entregas }} Entregas</span>
                            <!--end::Label-->
                        </div>
                        <!--ed::Item-->
                        <!--begin::Item-->
                        <div class="d-flex align-items-center flex-shrink-0">
                            <!--begin::Bullet-->
                            <span class="bullet bullet-dot bg-success me-2 h-10px w-10px"></span>
                            <!--end::Bullet-->
                            <!--begin::Label-->
                            <span class="fw-semibold text-gray-400 fs-6">{{ $entregas->entregas_com_canhoto }} Realizadas</span>
                            <!--end::Label-->
                        </div>
                        <!--ed::Item-->
                    </div>
                    <!--ed::Info-->
                </div>
                <!--end::Mixed Widget 17-->
            </div>
        </div>
        <!--end::Col-->
    </div>
    <!--end::Row-->

    @section('scripts')
        <script>
            "use strict";

            // Class definition
            var KTChartsWidget36 = function () {
                var chart = {
                    self: null,
                    rendered: false
                };

                // Private methods
                var initChart = function (chart) {
                    var element = document.getElementById("kt_charts_widget_36");

                    if (!element) {
                        return;
                    }

                    var height = parseInt(KTUtil.css(element, 'height'));
                    var labelColor = KTUtil.getCssVariableValue('--bs-gray-500');
                    var borderColor = KTUtil.getCssVariableValue('--bs-border-dashed-color');

                    var baseprimaryColor = KTUtil.getCssVariableValue('--bs-primary');
                    var lightprimaryColor = KTUtil.getCssVariableValue('--bs-primary');

                    var basesuccessColor = KTUtil.getCssVariableValue('--bs-success');
                    var lightsuccessColor = KTUtil.getCssVariableValue('--bs-success');

                    var baseinfoColor = KTUtil.getCssVariableValue('--bs-danger');
                    var lightinfoColor = KTUtil.getCssVariableValue('--bs-danger');

                    var options = {
                        series: [
                            {
                                name: '{{ 'Recebimento '. ' - ' . \App\Models\Filial::listFiliais()['10101'] }}',
                                data: {!! json_encode($data['100']['recebimento']) !!}
                            },
                            {
                                name: '{{ 'Recebimento '. ' - ' . \App\Models\Filial::listFiliais()['20101'] }}',
                                data: {!! json_encode($data['200']['recebimento']) !!}
                            },
                            {
                                name: 'Recebimento - Total Geral',
                                data: {!! json_encode($data['geral']['recebimento']) !!}
                            }
                        ],
                        chart: {
                            fontFamily: 'inherit',
                            type: 'area',
                            height: height,
                            toolbar: {
                                show: true
                            },

                        },
                        plotOptions: {},
                        legend: {
                            show: false
                        },
                        dataLabels: {
                            enabled: false
                        },
                        fill: {
                            type: "gradient",
                            gradient: {
                                shadeIntensity: 1,
                                opacityFrom: 0.4,
                                opacityTo: 0.2,
                                // stops: [15, 120, 100]
                            }
                        },
                        stroke: {
                            curve: 'smooth',
                            show: true,
                            width: 3,
                            colors: [baseprimaryColor, basesuccessColor, baseinfoColor]
                        },
                        xaxis: {
                            categories: {!! json_encode($data['mes']) !!},
                            axisBorder: {
                                show: false,
                            },
                            axisTicks: {
                                show: false
                            },
                            tickAmount: 12,
                            labels: {
                                rotate: 0,
                                rotateAlways: true,
                                style: {
                                    colors: labelColor,
                                    fontSize: '12px'
                                }
                            },
                            crosshairs: {
                                position: 'front',
                                stroke: {
                                    color: [baseprimaryColor, basesuccessColor, baseinfoColor],
                                    width: 1,
                                    dashArray: 3
                                }
                            },
                            tooltip: {
                                enabled: true,
                                formatter: undefined,
                                offsetY: 0,
                                style: {
                                    fontSize: '12px'
                                }
                            }
                        },
                        yaxis: {
                            labels: {
                                style: {
                                    colors: labelColor,
                                    fontSize: '12px'
                                },
                                formatter: function (value) {
                                    return value.toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'});
                                }

                            }
                        },
                        states: {
                            normal: {
                                filter: {
                                    type: 'none',
                                    value: 0
                                }
                            },
                            hover: {
                                filter: {
                                    type: 'none',
                                    value: 0
                                }
                            },
                            active: {
                                allowMultipleDataPointsSelection: false,
                                filter: {
                                    type: 'none',
                                    value: 0
                                }
                            }
                        },
                        colors: [lightprimaryColor, lightsuccessColor, lightinfoColor],
                        grid: {
                            borderColor: borderColor,
                            strokeDashArray: 4,
                            yaxis: {
                                lines: {
                                    show: true
                                }
                            }
                        },
                        markers: {
                            strokeColor: [baseprimaryColor, basesuccessColor, baseinfoColor],
                            strokeWidth: 3
                        }
                    };

                    chart.self = new ApexCharts(element, options);

                    // Set timeout to properly get the parent elements width
                    setTimeout(function () {
                        chart.self.render();
                        chart.rendered = true;
                    }, 200);
                }

                // Public methods
                return {
                    init: function () {
                        initChart(chart);

                        // Update chart on theme mode change
                        KTThemeMode.on("kt.thememode.change", function () {
                            if (chart.rendered) {
                                chart.self.destroy();
                            }

                            initChart(chart);
                        });
                    }
                }
            }();

            // Class definition
            var KTChartsWidget37 = function () {
                var chart = {
                    self: null,
                    rendered: false
                };

                // Private methods
                var initChart = function (chart) {
                    var element = document.getElementById("kt_charts_widget_37");

                    if (!element) {
                        return;
                    }

                    var height = parseInt(KTUtil.css(element, 'height'));
                    var labelColor = KTUtil.getCssVariableValue('--bs-gray-500');
                    var borderColor = KTUtil.getCssVariableValue('--bs-border-dashed-color');

                    var baseprimaryColor = KTUtil.getCssVariableValue('--bs-primary');
                    var lightprimaryColor = KTUtil.getCssVariableValue('--bs-primary');

                    var basesuccessColor = KTUtil.getCssVariableValue('--bs-success');
                    var lightsuccessColor = KTUtil.getCssVariableValue('--bs-success');

                    var baseinfoColor = KTUtil.getCssVariableValue('--bs-danger');
                    var lightinfoColor = KTUtil.getCssVariableValue('--bs-danger');

                    var options = {
                        series: [
                            {
                                name: '{{ 'Previsão de Comissão '. ' - ' . \App\Models\Filial::listFiliais()['10101'] }}',
                                data: {!! json_encode($data['100']['comissao']) !!}
                            },
                            {
                                name: '{{ 'Previsão de Comissão '. ' - ' .  \App\Models\Filial::listFiliais()['20101']}}',
                                data: {!! json_encode($data['200']['comissao']) !!}
                            },
                            {
                                name: 'Previsão de Comissão - Total Geral',
                                data: {!! json_encode($data['geral']['comissao']) !!}
                            }
                        ],
                        chart: {
                            fontFamily: 'inherit',
                            type: 'area',
                            height: height,
                            toolbar: {
                                show: true
                            }
                        },
                        plotOptions: {},
                        legend: {
                            show: false
                        },
                        dataLabels: {
                            enabled: false
                        },
                        fill: {
                            type: "gradient",
                            gradient: {
                                shadeIntensity: 1,
                                opacityFrom: 0.4,
                                opacityTo: 0.2,
                                // stops: [15, 120, 100]
                            }
                        },
                        stroke: {
                            curve: 'smooth',
                            show: true,
                            width: 3,
                            colors: [baseprimaryColor, basesuccessColor, baseinfoColor]
                        },
                        xaxis: {
                            categories: {!! json_encode($data['mes']) !!},
                            axisBorder: {
                                show: false,
                            },
                            axisTicks: {
                                show: false
                            },
                            tickAmount: 12,
                            labels: {
                                rotate: 0,
                                rotateAlways: true,
                                style: {
                                    colors: labelColor,
                                    fontSize: '12px'
                                }
                            },
                            crosshairs: {
                                position: 'front',
                                stroke: {
                                    color: [baseprimaryColor, basesuccessColor, baseinfoColor],
                                    width: 1,
                                    dashArray: 3
                                }
                            },
                            tooltip: {
                                enabled: true,
                                formatter: undefined,
                                offsetY: 0,
                                style: {
                                    fontSize: '12px'
                                }
                            }
                        },
                        yaxis: {
                            labels: {
                                style: {
                                    colors: labelColor,
                                    fontSize: '12px'
                                },
                                formatter: function (value) {
                                    return value.toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'});
                                }

                            }
                        },
                        states: {
                            normal: {
                                filter: {
                                    type: 'none',
                                    value: 0
                                }
                            },
                            hover: {
                                filter: {
                                    type: 'none',
                                    value: 0
                                }
                            },
                            active: {
                                allowMultipleDataPointsSelection: false,
                                filter: {
                                    type: 'none',
                                    value: 0
                                }
                            }
                        },
                        tooltip: {
                            style: {
                                fontSize: '12px'
                            },
                        },
                        colors: [lightprimaryColor, lightsuccessColor, lightinfoColor],
                        grid: {
                            borderColor: borderColor,
                            strokeDashArray: 4,
                            yaxis: {
                                lines: {
                                    show: true
                                }
                            }
                        },
                        markers: {
                            strokeColor: [baseprimaryColor, basesuccessColor, baseinfoColor],
                            strokeWidth: 3
                        }
                    };

                    chart.self = new ApexCharts(element, options);

                    // Set timeout to properly get the parent elements width
                    setTimeout(function () {
                        chart.self.render();
                        chart.rendered = true;
                    }, 200);
                }

                // Public methods
                return {
                    init: function () {
                        initChart(chart);

                        // Update chart on theme mode change
                        KTThemeMode.on("kt.thememode.change", function () {
                            if (chart.rendered) {
                                chart.self.destroy();
                            }

                            initChart(chart);
                        });
                    }
                }
            }();
            var KTChartsWidget38 = function () {
                var chart = {
                    self: null,
                    rendered: false
                };

                // Private methods
                var initChart = function (chart) {
                    var element = document.getElementById("kt_charts_widget_38");

                    if (!element) {
                        return;
                    }

                    var height = parseInt(KTUtil.css(element, 'height'));
                    var labelColor = KTUtil.getCssVariableValue('--bs-gray-500');
                    var borderColor = KTUtil.getCssVariableValue('--bs-border-dashed-color');

                    var baseprimaryColor = KTUtil.getCssVariableValue('--bs-primary');
                    var lightprimaryColor = KTUtil.getCssVariableValue('--bs-primary');

                    var basesuccessColor = KTUtil.getCssVariableValue('--bs-success');
                    var lightsuccessColor = KTUtil.getCssVariableValue('--bs-success');

                    var baseinfoColor = KTUtil.getCssVariableValue('--bs-danger');
                    var lightinfoColor = KTUtil.getCssVariableValue('--bs-danger');

                    var options = {
                        series: [
                            {
                                name: 'Faturamento',
                                data: {!! json_encode($data['faturamento']) !!}
                            }
                        ],
                        chart: {
                            fontFamily: 'inherit',
                            type: 'area',
                            height: height,
                            toolbar: {
                                show: true
                            }
                        },
                        plotOptions: {},
                        legend: {
                            show: false
                        },
                        dataLabels: {
                            enabled: false
                        },
                        fill: {
                            type: "gradient",
                            gradient: {
                                shadeIntensity: 1,
                                opacityFrom: 0.4,
                                opacityTo: 0.2,
                                // stops: [15, 120, 100]
                            }
                        },
                        stroke: {
                            curve: 'smooth',
                            show: true,
                            width: 3,
                            colors: [baseprimaryColor, basesuccessColor, baseinfoColor]
                        },
                        xaxis: {
                            categories: {!! json_encode($data['faturamento_mes']) !!},
                            axisBorder: {
                                show: false,
                            },
                            axisTicks: {
                                show: false
                            },
                            tickAmount: 12,
                            labels: {
                                rotate: 0,
                                rotateAlways: true,
                                style: {
                                    colors: labelColor,
                                    fontSize: '12px'
                                }
                            },
                            crosshairs: {
                                position: 'front',
                                stroke: {
                                    color: [baseprimaryColor, basesuccessColor, baseinfoColor],
                                    width: 1,
                                    dashArray: 3
                                }
                            },
                            tooltip: {
                                enabled: true,
                                formatter: undefined,
                                offsetY: 0,
                                style: {
                                    fontSize: '12px'
                                }
                            }
                        },
                        yaxis: {
                            labels: {
                                style: {
                                    colors: labelColor,
                                    fontSize: '12px'
                                },
                                formatter: function (value) {
                                    return value.toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'});
                                }

                            }
                        },
                        states: {
                            normal: {
                                filter: {
                                    type: 'none',
                                    value: 0
                                }
                            },
                            hover: {
                                filter: {
                                    type: 'none',
                                    value: 0
                                }
                            },
                            active: {
                                allowMultipleDataPointsSelection: false,
                                filter: {
                                    type: 'none',
                                    value: 0
                                }
                            }
                        },
                        tooltip: {
                            style: {
                                fontSize: '12px'
                            },
                        },
                        colors: [lightprimaryColor, lightsuccessColor, lightinfoColor],
                        grid: {
                            borderColor: borderColor,
                            strokeDashArray: 4,
                            yaxis: {
                                lines: {
                                    show: true
                                }
                            }
                        },
                        markers: {
                            strokeColor: [baseprimaryColor, basesuccessColor, baseinfoColor],
                            strokeWidth: 3
                        }
                    };

                    chart.self = new ApexCharts(element, options);

                    // Set timeout to properly get the parent elements width
                    setTimeout(function () {
                        chart.self.render();
                        chart.rendered = true;
                    }, 200);
                }

                // Public methods
                return {
                    init: function () {
                        initChart(chart);

                        // Update chart on theme mode change
                        KTThemeMode.on("kt.thememode.change", function () {
                            if (chart.rendered) {
                                chart.self.destroy();
                            }

                            initChart(chart);
                        });
                    }
                }
            }();
            var initMixedWidget17 = function () {
                var charts = document.querySelectorAll('.mixed-widget-17-chart');

                [].slice.call(charts).map(function(element) {
                    var height = parseInt(KTUtil.css(element, 'height'));

                    if ( !element ) {
                        return;
                    }

                    var color = element.getAttribute('data-kt-chart-color');

                    var baseColor = KTUtil.getCssVariableValue('--bs-' + color);
                    var lightColor = KTUtil.getCssVariableValue('--bs-' + color + '-light' );
                    var labelColor = KTUtil.getCssVariableValue('--bs-' + 'gray-700');

                    var options = {
                        series: [{{$entregas->porcentagem}}],
                        chart: {
                            fontFamily: 'inherit',
                            height: height,
                            type: 'radialBar',
                        },
                        plotOptions: {
                            radialBar: {
                                hollow: {
                                    margin: 0,
                                    size: "65%"
                                },
                                dataLabels: {
                                    showOn: "always",
                                    name: {
                                        show: false,
                                        fontWeight: '700'
                                    },
                                    value: {
                                        color: labelColor,
                                        fontSize: "30px",
                                        fontWeight: '700',
                                        offsetY: 12,
                                        show: true,
                                        formatter: function (val) {
                                            return val + '%';
                                        }
                                    }
                                },
                                track: {
                                    background: lightColor,
                                    strokeWidth: '100%'
                                }
                            }
                        },
                        colors: [baseColor],
                        stroke: {
                            lineCap: "round",
                        },
                        labels: ["Progress"]
                    };

                    var chart = new ApexCharts(element, options);
                    chart.render();
                });
            }
            // Class definition
            var KTChartsWidget35 = function () {
                var chart1 = {
                    self: null,
                    rendered: false
                };

                var chart2 = {
                    self: null,
                    rendered: false
                };

                var chart3 = {
                    self: null,
                    rendered: false
                };

                var chart4 = {
                    self: null,
                    rendered: false
                };

                var chart5 = {
                    self: null,
                    rendered: false
                };


                // Private methods
                var initChart = function(chart, toggle, chartSelector, data, labels, initByDefault) {
                    var element = document.querySelector(chartSelector);

                    if (!element) {
                        return;
                    }

                    var height = parseInt(KTUtil.css(element, 'height'));
                    var color = element.getAttribute('data-kt-chart-color');

                    var labelColor = KTUtil.getCssVariableValue('--bs-gray-500');
                    var borderColor = KTUtil.getCssVariableValue('--bs-border-dashed-color');
                    var baseColor = KTUtil.getCssVariableValue('--bs-' + color);

                    var options = {
                        series: [{
                            name: 'Earnings',
                            data: data
                        }],
                        chart: {
                            fontFamily: 'inherit',
                            type: 'area',
                            height: height,
                            toolbar: {
                                show: false
                            }
                        },
                        legend: {
                            show: false
                        },
                        dataLabels: {
                            enabled: false
                        },
                        fill: {
                            type: "gradient",
                            gradient: {
                                shadeIntensity: 1,
                                opacityFrom: 0.4,
                                opacityTo: 0.2,
                                stops: [15, 120, 100]
                            }
                        },
                        stroke: {
                            curve: 'smooth',
                            show: true,
                            width: 3,
                            colors: [baseColor]
                        },
                        xaxis: {
                            categories: labels,
                            axisBorder: {
                                show: false,
                            },
                            axisTicks: {
                                show: false
                            },
                            offsetX: 20,
                            tickAmount: 4,
                            labels: {
                                rotate: 0,
                                rotateAlways: false,
                                show: false,
                                style: {
                                    colors: labelColor,
                                    fontSize: '12px'
                                }
                            },
                            crosshairs: {
                                position: 'front',
                                stroke: {
                                    color: baseColor,
                                    width: 1,
                                    dashArray: 3
                                }
                            },
                            tooltip: {
                                enabled: true,
                                formatter: undefined,
                                offsetY: 0,
                                style: {
                                    fontSize: '12px'
                                }
                            }
                        },
                        yaxis: {
                            tickAmount: 4,
                            max: 4000,
                            min: 1000,
                            labels: {
                                show: false
                            }
                        },
                        states: {
                            normal: {
                                filter: {
                                    type: 'none',
                                    value: 0
                                }
                            },
                            hover: {
                                filter: {
                                    type: 'none',
                                    value: 0
                                }
                            },
                            active: {
                                allowMultipleDataPointsSelection: false,
                                filter: {
                                    type: 'none',
                                    value: 0
                                }
                            }
                        },
                        tooltip: {
                            style: {
                                fontSize: '12px'
                            },
                            y: {
                                formatter: function (val) {
                                    return val + '$';
                                }
                            }
                        },
                        colors: [baseColor],
                        grid: {
                            borderColor: borderColor,
                            strokeDashArray: 3,
                            yaxis: {
                                lines: {
                                    show: true
                                }
                            }
                        },
                        markers: {
                            strokeColor: baseColor,
                            strokeWidth: 3
                        }
                    };

                    chart.self = new ApexCharts(element, options);
                    var tab = document.querySelector(toggle);

                    if (initByDefault === true) {
                        // Set timeout to properly get the parent elements width
                        setTimeout(function() {
                            chart.self.render();
                            chart.rendered = true;
                        }, 200);
                    }

                    tab.addEventListener('shown.bs.tab', function (event) {
                        if (chart.rendered === false) {
                            chart.self.render();
                            chart.rendered = true;
                        }
                    });
                }

                // Public methods
                return {
                    init: function () {
                        var chart1Data = [2100, 3100, 3100, 2400, 2400, 1800, 1800, 2400, 2400, 3200, 3200, 2800, 2800, 3250, 3250];
                        var chart1Labels = ['10AM', '10.30AM', '11AM', '11.15AM', '11.30AM', '12PM', '1PM', '2PM', '3PM', '4PM', '5PM', '6PM', '7PM', '8PM', '9PM'];

                        initChart(chart1, '#kt_charts_widget_35_tab_1', '#kt_charts_widget_35_chart_1', chart1Data, chart1Labels, true);

                        var chart2Data = [2300, 2300, 2000, 3200, 3200, 2800, 2400, 2400, 3100, 2900, 3200, 3200, 2600, 2600, 3200];
                        var chart2Labels = ['Apr 01', 'Apr 02', 'Apr 03', 'Apr 04', 'Apr 05', 'Apr 06', 'Apr 07', 'Apr 08', 'Apr 09', 'Apr 10', 'Apr 11', 'Apr 12', 'Apr 13', 'Apr 14', 'Apr 15'];

                        initChart(chart2, '#kt_charts_widget_35_tab_2', '#kt_charts_widget_35_chart_2', chart2Data, chart2Labels, false);

                        var chart3Data = [2600, 3200, 2300, 2300, 2000, 3200, 3100, 2900, 3400, 3400, 2600, 3200, 2800, 2400, 2400];
                        var chart3Labels = ['Apr 02', 'Apr 03', 'Apr 04', 'Apr 05', 'Apr 06', 'Apr 09', 'Apr 10', 'Apr 12', 'Apr 14', 'Apr 17', 'Apr 18', 'Apr 18', 'Apr 20', 'Apr 22', 'Apr 24'];

                        initChart(chart3, '#kt_charts_widget_35_tab_3', '#kt_charts_widget_35_chart_3', chart3Data, chart3Labels, false);

                        var chart4Data =  [1800, 1800, 2400, 2400, 3200, 3200, 3000, 2100, 3200, 3200, 2400, 2400, 3000, 3200, 3100];
                        var chart4Labels = ['Jun 2021', 'Jul 2021', 'Aug 2021', 'Sep 2021', 'Oct 2021', 'Nov 2021', 'Dec 2021', 'Jan 2022', 'Feb 2022', 'Mar 2022', 'Apr 2022', 'May 2022', 'Jun 2022', 'Jul 2022', 'Aug 2022'];

                        initChart(chart4, '#kt_charts_widget_35_tab_4', '#kt_charts_widget_35_chart_4', chart4Data, chart4Labels, false);

                        var chart5Data = [3200, 2100, 3200, 3200, 3200, 3500, 3000, 2400, 3250, 2400, 2400, 3250, 3000, 2400, 2800];
                        var chart5Labels = ['Sep 2021', 'Oct 2021', 'Nov 2021', 'Dec 2021', 'Jan 2022', 'Feb 2022', 'Mar 2022', 'Apr 2022', 'May 2022', 'Jun 2022', 'Jul 2022', 'Aug 2022', 'Sep 2022', 'Oct 2022', 'Nov 2022'];

                        initChart(chart5, '#kt_charts_widget_35_tab_5', '#kt_charts_widget_35_chart_5', chart5Data, chart5Labels, false);

                        // Update chart on theme mode change
                        KTThemeMode.on("kt.thememode.change", function() {
                            if (chart1.rendered) {
                                chart1.self.destroy();
                            }

                            if (chart2.rendered) {
                                chart2.self.destroy();
                            }

                            if (chart3.rendered) {
                                chart3.self.destroy();
                            }

                            if (chart4.rendered) {
                                chart4.self.destroy();
                            }

                            if (chart5.rendered) {
                                chart5.self.destroy();
                            }

                            initChart(chart1, '#kt_charts_widget_35_tab_1', '#kt_charts_widget_35_chart_1', chart1Data, chart1Labels, chart1.rendered);
                            initChart(chart2, '#kt_charts_widget_35_tab_2', '#kt_charts_widget_35_chart_2', chart2Data, chart2Labels, chart2.rendered);
                            initChart(chart3, '#kt_charts_widget_35_tab_3', '#kt_charts_widget_35_chart_3', chart3Data, chart3Labels, chart3.rendered);
                            initChart(chart4, '#kt_charts_widget_35_tab_4', '#kt_charts_widget_35_chart_4', chart4Data, chart4Labels, chart4.rendered);
                            initChart(chart5, '#kt_charts_widget_35_tab_5', '#kt_charts_widget_35_chart_5', chart5Data, chart5Labels, chart5.rendered);
                        });
                    }
                }
            }();


            // Webpack support
            if (typeof module !== 'undefined') {
                module.exports = KTChartsWidget36;
                module.exports = KTChartsWidget37;
            }

            // On document ready
            KTUtil.onDOMContentLoaded(function () {
                KTChartsWidget36.init();
                KTChartsWidget37.init();
                KTChartsWidget38.init();
                initMixedWidget17();
                KTChartsWidget35.init();
            });


            $(function () {
                $('.dashboard-ano').on('change', function () {
                    window.location.href = '{{ route('home') }}?ano=' + $(this).val();
                });
            });

        </script>
    @endsection
</x-default-layout>
