<nav class="main-header navbar navbar-expand navbar-white navbar-light">

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <span class="nav-link">
            {{ Auth::user()->name  ?? ''}}
        </span>
        </li>
        @if(isset(Auth::user()->name))
        <li class="nav-item">

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-link nav-link" style="color: inherit; text-decoration: none;">
                    {{ __('Logout') }}
                </button>
            </form>
        </li>
        @endif
        @if(!isset(Auth::user()->name))
            <li class="nav-item">

                <form id="logout-form" action="{{ route('login') }}" method="GET" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-link nav-link" style="color: inherit; text-decoration: none;">
                        {{ __('Login') }}
                    </button>
                </form>
            </li>
        @endif
    </ul>
</nav>
