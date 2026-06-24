<!DOCTYPE html>
<!--
Template Name: NobleUI - Laravel Admin Dashboard Template
Author: NobleUI
Website: https://www.nobleui.com
Portfolio: https://themeforest.net/user/nobleui/portfolio
Contact: nobleui123@gmail.com
Purchase: https://1.envato.market/nobleui_laravel
License: For each use you must have a valid license purchased only from above link in order to legally use the theme for your project.
-->
<html>
<head>
  <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="description" content="Responsive Laravel Admin Dashboard Template based on Bootstrap 5">
	<meta name="author" content="NobleUI">
	<meta name="keywords" content="nobleui, bootstrap, bootstrap 5, bootstrap5, admin, dashboard, template, responsive, css, sass, html, laravel, theme, front-end, ui kit, web">

  <title>TERRANOVA</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
  <!-- End fonts -->
  
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  
  {{-- <link rel="shortcut icon" href="{{ asset('/favicon.ico') }}"> --}}
  <link rel="icon" type="image/png" href="{{ asset('/assets/images/terranova_logo.png') }}">

  <!-- plugin css -->
  <link href="{{ asset('assets/fonts/feather-font/css/iconfont.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.css') }}" rel="stylesheet" />
  <!-- end plugin css -->

  @stack('plugin-styles')

  <!-- common css -->
  <link href="{{ asset('css/app.css') }}" rel="stylesheet" />
  <!-- end common css -->

  <style>
    .sidebar { box-shadow: 2px 0 12px rgba(0,0,0,0.04); }
    .sidebar .sidebar-header {
      background: var(--ins-blanco);
      border-bottom: 1px solid rgba(0,0,0,0.06);
      border-right: none;
      padding: 18px 20px;
    }
    .sidebar .sidebar-header .sidebar-brand {
      color: var(--ins-negro);
      font-size: 18px;
      font-family: 'ND LOGOS REGULAR', sans-serif;
      letter-spacing: 1px;
      -webkit-text-stroke: 0.6px currentColor;
      text-shadow: 0 0 0.3px currentColor;
      font-weight: 800;
    }
    .sidebar .sidebar-header .sidebar-toggler span { background: var(--ins-negro); }
    .sidebar .sidebar-body {
      background: var(--ins-azul);
      border-right: none;
      padding: 12px 0;
    }
    .sidebar .sidebar-body .nav .nav-item { padding: 2px 12px; }
    .sidebar .sidebar-body .nav .nav-item .nav-link {
      color: rgba(255,255,255,0.75);
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 10px 14px;
      border-radius: 10px;
      transition: all 0.25s ease;
      font-weight: 450;
      font-size: 0.9rem;
    }
    .sidebar .sidebar-body .nav .nav-item .nav-link .link-icon {
      width: 18px;
      height: 18px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: rgba(255,255,255,0.75);
    }
    .sidebar .sidebar-body .nav .nav-item:hover .nav-link,
    .sidebar .sidebar-body .nav .nav-item.active .nav-link {
      color: var(--ins-azul);
      background: var(--ins-blanco);
    }
    .sidebar .sidebar-body .nav .nav-item:hover .nav-link .link-icon,
    .sidebar .sidebar-body .nav .nav-item.active .nav-link .link-icon {
      color: var(--ins-azul);
      fill: rgba(255,255,255,0.2);
    }
    .sidebar .sidebar-body .nav .nav-item.active .nav-link::before { display: none; }
    .sidebar .sidebar-body .nav .nav-item .nav-link .link-title {
      position: relative;
      top: 1px;
    }
  </style>

  @stack('style')
</head>
<body data-base-url="{{url('/')}}">

  <script src="{{ asset('assets/js/spinner.js') }}"></script>

  <div class="main-wrapper" id="app">
    @include('layout.sidebar')
    <div class="page-wrapper">
      @include('layout.header')
      <div class="page-content">
        @yield('content')
      </div>
      @include('layout.footer')
    </div>
  </div>

    <!-- base js -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('assets/plugins/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <!-- end base js -->

    <!-- plugin js -->
    @stack('plugin-scripts')
    <!-- end plugin js -->

    <!-- common js -->
    <script src="{{ asset('assets/js/template.js') }}"></script>
    <!-- end common js -->

    @stack('custom-scripts')
</body>
</html>