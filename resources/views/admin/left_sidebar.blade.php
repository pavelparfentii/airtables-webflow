<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{route('home')}}" class="brand-link">
        <img src="{{url('/admin/images/123.jpg')}}" alt="{{ config('app.name')}}" class="brand-image img-circle elevation-3">
        <span class="brand-text font-weight-light">{{ config('app.name')}}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            {{--                <div class="image">--}}
            {{--                    <img src="/admin/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">--}}
            {{--                </div>--}}
            <div class="info">
{{--                <a href="#" class="d-block">Пользователь <br>{{\Illuminate\Support\Facades\Auth::user()->name}}</a>--}}
            </div>
        </div>
{{--        <div class="user-panel mt-3 pb-3 mb-3 d-flex">--}}

{{--            <div class="info ">--}}
{{--                <a href="#" class="d-block">Последний визит <br>{{\Illuminate\Support\Facades\Auth::user()->last_visit}}</a>--}}
{{--            </div>--}}
{{--        </div>--}}

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" role="menu">
                <li class="nav-item">
                    <a href="{{ route('admins.create') }}" class="nav-link">
                        <i class="fas fa-user-plus nav-icon"></i>
                        <p>Додати адміністратора</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admins.index') }}" class="nav-link">
                        <i class="fas fa-users nav-icon"></i>
                        <p>Список адміністраторів</p>
                    </a>
                </li>
            </ul>
        </nav>


        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

