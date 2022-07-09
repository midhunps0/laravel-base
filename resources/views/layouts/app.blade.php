<!DOCTYPE html>
<html x-data="{theme: $persist('newdark'), href: '', currentpath: '{{url()->current()}}', currentroute: '{{ Route::currentRouteName() }}'}" @themechange.window="theme = $event.detail.darktheme ? 'newdark' : 'light';" lang="{{ str_replace('_', '-', app()->getLocale()) }}"
x-init="window.landingUrl = '{{\Request::getRequestUri()}}'"
@pagechanged.window="console.log('pcanged');console.log($event.detail);
currentpath=$event.detail.currentpath;
currentroute=$event.detail.currentroute;"
@routechange.window="currentroute=$event.detail.route;"
:data-theme="theme">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
    </head>
    <body x-data="initPage" x-init="initAction();"
        @linkaction.window="fetchLink($event.detail);"
        @popstate.window="historyAction($event)"
        class="font-sans antialiased text-sm">
        <div class="min-h-screen bg-base-200 flex flex-col">
            @include('layouts.navigation')
{{--
            <!-- Page Heading -->
            <header class="bg-base-100 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header> --}}

            <!-- Page Content -->
            <main class="flex flex-col items-stretch flex-grow w-full">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
