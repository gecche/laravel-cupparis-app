<head>
    <meta charset="UTF-8">
    <title>{!! Config::get('app.name') !!}</title>
    <meta name="description" content="Contatti Espad">

    <meta name="viewport" content="width=device-width, maximum-scale=5, initial-scale=1, user-scalable=0">
    <!--[if IE]><meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'><![endif]-->

    <!-- up to 10% speed up for external res -->
    <link rel="dns-prefetch" href="https://fonts.googleapis.com/">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com/">
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/">
    <!-- preloading icon font is helping to speed up a little bit -->
    <link rel="preload" href="{!! Theme::url('assets/fonts/flaticon/Flaticon.woff2') !!}" as="font" type="font/woff2" crossorigin>

    <!-- non block rendering : page speed : js = polyfill for old browsers missing `preload` -->
    <link rel="stylesheet" href="{!! Theme::url('assets/css/core.css') !!}">
    <link rel="stylesheet" href="{!! Theme::url('assets/css/vendor_bundle.min.css') !!}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&amp;display=swap">

    <!-- favicon -->
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="apple-touch-icon" href="{!! Theme::url('images/logopic2.png') !!}">

    <link rel="manifest" href="{!! Theme::url('assets/images/manifest/manifest.json') !!}">
    <meta name="theme-color" content="#377dff">

    <!-- CUPPARIS -->

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- minimal crud-vue css -->
    <link href="{!! Theme::url('crud-vue/crud-vue.css') !!}" rel="stylesheet">
    <!-- font-awesome -->
    {!! Theme::css('assets/css/zwicon/zwicon.min.css') !!}
    {!! Theme::css('assets/css/jqvmap/jqvmap.min.css') !!}
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    {!! Theme::css('css/select2-bootstrap4.css') !!}
    {!! Theme::css('css/app.css') !!}


</head>
