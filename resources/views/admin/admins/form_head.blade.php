<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ $title }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Головна</a></li>
                    @foreach ($steps as $step)
                        @if ($step['path'] == '#')
                            <li class="breadcrumb-item active">
                                {{ $step['name'] }}
                            </li>
                        @else
                            <li class="breadcrumb-item">
                                <a href="{{ $step['path'] }}">{{ $step['name'] }}</a>
                            </li>
                        @endif
                    @endforeach
                </ol>
            </div>
        </div>
    </div>
</section>
