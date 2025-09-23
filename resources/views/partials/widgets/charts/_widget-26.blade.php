<!--begin::Chart widget 26-->
<div class="card card-flush overflow-hidden h-xl-100">
    <!--begin::Header-->
    <div class="card-header pt-7 mb-2">
        <!--begin::Title-->
        <h3 class="card-title text-gray-800 fw-bold">Transaction History</h3>
        <!--end::Title-->

        <!--begin::Toolbar-->
        <div class="card-toolbar">
            <!--begin::Daterangepicker(defined in src/js/layout/app.js)-->
            <div data-kt-daterangepicker="true" data-kt-daterangepicker-opens="left" class="btn btn-sm btn-light d-flex align-items-center px-4">
                <!--begin::Display range-->
                <div class="text-gray-600 fw-bold">
                    Loading date range...
                </div>
                <!--end::Display range-->

                <i class="ki-duotone ki-calendar-8 text-gray-500 lh-0 fs-2 ms-2 me-0"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></i>
            </div>
            <!--end::Daterangepicker-->
        </div>
        <!--end::Toolbar-->
    </div>
    <!--end::Header-->

    <!--begin::Card body-->
    <div class="card-body d-flex justify-content-between flex-column pt-0 pb-1 px-0">
        <!--begin::Info-->
        <div class="px-9 mb-5">
            <!--begin::Statistics-->
            <div class="d-flex align-items-center mb-2">
                <!--begin::Currency-->
                <span class="fs-4 fw-semibold text-gray-500 align-self-start me-1">$</span>
                <!--end::Currency-->

                <!--begin::Value-->
                <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1 ls-n2">12,706</span>
                <!--end::Value-->

                <!--begin::Label-->
                <span class="badge badge-success fs-base">
                    <i class="ki-duotone ki-arrow-up fs-5 text-white ms-n1"><span class="path1"></span><span class="path2"></span></i>
                    4.5%
                </span>
                <!--end::Label-->
            </div>
            <!--end::Statistics-->

            <!--begin::Description-->
            <span class="fs-6 fw-semibold text-gray-500">Transactions in April</span>
            <!--end::Description-->
        </div>
        <!--end::Info-->

        <!--begin::Chart-->
        <div id="kt_charts_widget_26" class="min-h-auto ps-4 pe-6" data-kt-chart-info="Transactions" style="height: 300px"></div>
        <!--end::Chart-->
    </div>
    <!--end::Card body-->
</div>
<!--end::Chart widget 26-->    </div>
<!--end::Col-->