<!--begin::Page title-->
<div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
    <!--begin::Title-->
    <!--end::Title-->
    @if (count($breadcrumbs))

        <ul class="breadcrumb text-muted fs-6 fw-semibold">
            @foreach ($breadcrumbs as $breadcrumb)

                @if ($breadcrumb->url && !$loop->last)
                    <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
                @else
                    <li class="breadcrumb-item text-muted active">{{ $breadcrumb->title }}</li>
                @endif

            @endforeach
        </ul>

    @endif

    <!--begin::Breadcrumb-->
    {{--    <!--begin::Item-->--}}
    {{--    <li class="breadcrumb-item text-muted">--}}
    {{--        <a href="/" class="text-muted text-hover-primary">Home</a>--}}
    {{--    </li>--}}
    {{--    <!--end::Item-->--}}
    {{--    <!--begin::Item-->--}}
    {{--    <li class="breadcrumb-item">--}}
    {{--        <span class="bullet bg-gray-400 w-5px h-2px"></span>--}}
    {{--    </li>--}}
    {{--    <!--end::Item-->--}}
    {{--    <!--begin::Item-->--}}
    {{--    <li class="breadcrumb-item text-muted">Dashboards</li>--}}
    {{--    <!--end::Item-->--}}
    {{--    </ul>--}}
    <!--end::Breadcrumb-->
</div>
<!--end::Page title-->