<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" {{ printHtmlAttributes('html') }}>
<!--begin::Head-->
<head>
    <base href=""/>
    <title>@yield('title') - {{ config('app.name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8"/>
    <meta name="description" content=""/>
    <meta name="keywords" content=""/>
    <meta name="viewport" content="width=device-width"/>
    <link rel="canonical" href=""/>
    <meta name="robots" content="noindex, nofollow"/>
    <link rel="manifest" href="/manifest.json">

    <link rel="icon" type="image/png" sizes="72x72" href="/assets/media/manifest/icon-72x72.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/assets/media/manifest/icon-96x96.png">

    <meta name="theme-color" content="#ffffff">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="mobile-web-app-title" content="{{ config('app.name') }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-status-bar-style" content="default">

    <!--begin::Fonts-->
    {{--    {!! includeFonts() !!}--}}
    <!--end::Fonts-->

    <!--begin::Global Stylesheets Bundle(used by all pages)-->
    @foreach(getGlobalAssets('css') as $path)
        {!! sprintf('<link rel="stylesheet" href="%s">', mix($path)) !!}
    @endforeach
    <!--end::Global Stylesheets Bundle-->

    <!--begin::Vendor Stylesheets(used by this page)-->
    @foreach(getVendors('css') as $path)
        {!! sprintf('<link rel="stylesheet" href="%s">', mix($path)) !!}
    @endforeach
    <!--end::Vendor Stylesheets-->

    <!--begin::Custom Stylesheets(optional)-->
    @foreach(getCustomCss() as $path)
        {!! sprintf('<link rel="stylesheet" href="%s">', mix($path)) !!}
    @endforeach
    <!--end::Custom Stylesheets-->

    <style>
        /*.page-loading .page-loader,[data-kt-app-page-loading=on] .page-loader {*/
        /*    display: flex;*/
        /*    justify-content: center;*/
        /*    align-items: center*/
        /*}*/
    </style>
    @livewireStyles
    @yield('styles')

</head>
<!--end::Head-->

<!--begin::Body-->
<body id="kt_app_body page-loading" {!! printHtmlClasses('body') !!} {!! printHtmlAttributes('body') !!}  data-kt-app-page-loading-enabled="true" data-kt-app-page-loading="off">
<!--begin::Page loading(append to body)-->
@include('partials/theme-mode/_init')

@yield('content')

<!--begin::Javascript-->
<!--begin::Global Javascript Bundle(mandatory for all pages)-->
@foreach(getGlobalAssets() as $path)
    {!! sprintf('<script src="%s"></script>', mix($path)) !!}
@endforeach
<!--end::Global Javascript Bundle-->

<!--begin::Vendors Javascript(used by this page)-->
@foreach(getVendors('js') as $path)
    {!! sprintf('<script src="%s"></script>', mix($path)) !!}
@endforeach
<!--end::Vendors Javascript-->

<!--begin::Custom Javascript(optional)-->
@foreach(getCustomJs() as $path)
    {!! sprintf('<script src="%s"></script>', mix($path)) !!}
@endforeach
<!--end::Custom Javascript-->

@yield('scripts')

</body>
<!--end::Body-->

</html>
