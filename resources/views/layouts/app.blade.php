<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Удобный инструмент для постановки задач и управления ими. Назначайте задачи пользователям прямо в мессенджере, отслеживайте выполнение и организуйте работу без лишних сложностей. Просто, быстро и эффективно">
    <meta name="keywords" content="управление задачами в telegram, телеграм задачи, trello в telegram, делегирование задач, планировщик задач, организация работы, задачи онлайн, бот для задач">
    <meta name="author" content="Alexey">
    <title>@yield('title')</title>
    <link rel="apple-touch-icon" href="/assets/images/favicon/apple-touch-icon-152x152.png">
    <link rel="shortcut icon" type="image/x-icon" href="/assets/images/favicon/favicon-32x32.png">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- BEGIN: VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="/assets/vendors/vendors.min.css">
    <!-- END: VENDOR CSS-->
    <!-- BEGIN: Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="/assets/css/themes/vertical-dark-menu-template/materialize.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/themes/vertical-dark-menu-template/style.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/pages/dashboard.css">
    <!-- END: Page Level CSS-->
    <!-- BEGIN: Custom CSS-->
    {{--    <link rel="stylesheet" type="text/css" href="/assets/css/custom/custom.css">--}}
    <!-- END: Custom CSS-->
    @yield('meta')
    <style>
        body{
            background-image: url("/img/background.jpg");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
        }

        .sidenav-main{
            opacity: 0.9;
        }
        #main{
            overflow-x: hidden;
        }
        .column-container{
            overflow-x: scroll;
        }
    </style>
</head>
<!-- END: Head-->

<body class="vertical-layout page-header-light vertical-menu-collapsible vertical-dark-menu preload-transitions 2-columns   " data-open="click" data-menu="vertical-dark-menu" data-col="2-columns">

<!-- BEGIN: SideNav-->
<aside class="sidenav-main nav-expanded nav-lock nav-collapsible sidenav-dark sidenav-active-rounded">
    <div class="brand-sidebar">
        <h1 class="logo-wrapper">
            <a class="brand-logo darken-1" href="/projects?uid={{request()->uid}}">
                <span class="logo-text hide-on-med-and-down"><span style="color: red">Tg</span>Task</span>
            </a>
        </h1>
    </div>
    <ul class="sidenav sidenav-collapsible leftside-navigation collapsible sidenav-fixed menu-shadow" id="slide-out" data-menu="menu-navigation" data-collapsible="accordion">
        <li class="navigation-header"><a class="navigation-header-text">Проекты</a><i class="navigation-header-icon material-icons">more_horiz</i>
        </li>

        @foreach($projects as $item)
            <li class="bold">
                <a class="waves-effect waves-cyan " href="{{route('project.show', $item->id)}}?uid={{request()->uid}}">
                    <i class="material-icons">radio_button_unchecked</i>
                    <span class="menu-title" data-i18n="Form Layouts">{{$item->title}}</span>
                </a>
            </li>
        @endforeach
    </ul>
    <div class="navigation-background"></div><a class="sidenav-trigger btn-sidenav-toggle btn-floating btn-medium waves-effect waves-light hide-on-large-only" href="#" data-target="slide-out"><i class="material-icons">menu</i></a>
</aside>
<!-- END: SideNav-->
<!-- BEGIN: Page Main-->
<div id="main">
    @yield('content')
</div>
<!-- END: Page Main-->
</body>
@yield('scripts')
</html>
