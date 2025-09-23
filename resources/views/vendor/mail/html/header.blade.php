@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'Laravel')
                <img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
            @else
                <img alt="Logo" src="{{ image('logos/logo.png') }}"
                     class="theme-light-show h-100px app-sidebar-logo-default" style="max-width: 150px"/>
            @endif
</a>
</td>
</tr>
