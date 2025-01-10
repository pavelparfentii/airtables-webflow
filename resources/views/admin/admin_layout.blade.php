<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admins | @yield('title')</title>
    <meta name="csrf-token" content="@csrf">
    @include('admin.styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed dark ">
<div class="wrapper">

    @include('admin.header')

    @include('admin.left_sidebar')

    <div class="content-wrapper">
        @if(session()->has('message'))
            <div class="alert {{session('alert') ?? 'alert-info'}}" role="alert">
                {{ session('message') }}
            </div>
        @endif
        <div class="container">
            @yield('content')
        </div>
    </div>

</div>

@include('admin.scripts')
@yield('scripts')

</body>
</html>
