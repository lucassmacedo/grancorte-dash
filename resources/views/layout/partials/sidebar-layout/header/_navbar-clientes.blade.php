<!--begin::Navbar-->
<div class="app-navbar flex-shrink-0">

    <div class="app-navbar-item ms-1 ms-lg-3">
        @include('partials/theme-mode/_main')
    </div>

    <!--end::Theme mode-->
    @auth

    <!--begin::User menu-->
    <div class="app-navbar-item ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
        <!--begin::Menu wrapper-->
        <div class="cursor-pointer symbol symbol-35px symbol-circle symbol-md-40px"
             data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent"
             data-kt-menu-placement="bottom-end">
            <span class="symbol-label  bg-gray-800 bg-opacity-50 fs-6 fw-bolder ">
               <i class="fa fa-user  text-white"></i>
            </span>
        </div>
            @include('partials/menus/_user-account-menu-clientes')
        <!--end::Menu wrapper-->
    </div>
    @endauth

    <!--end::User menu-->
    <!--begin::Header menu toggle-->
    {{--    <div class="app-navbar-item d-lg-none ms-2 me-n3" title="Show header menu">--}}
    {{--        <div class="btn btn-icon btn-active-color-primary w-35px h-35px" id="kt_app_header_menu_toggle">--}}
    {{--            <!--begin::Svg Icon | path: icons/duotune/text/txt001.svg-->--}}
    {{--            {!! getSvgIcon('duotune/text/txt001.svg', 'svg-icon svg-icon-1') !!}--}}
    {{--            <!--end::Svg Icon-->--}}
    {{--        </div>--}}
    {{--    </div>--}}
    <!--end::Header menu toggle-->
</div>
<!--end::Navbar-->
