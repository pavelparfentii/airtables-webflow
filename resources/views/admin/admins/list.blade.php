@extends('admin.admin_layout')

@section('title', 'Список користувачів')

@section('content')

    @include('admin.admins.form_head', [
        'title' => 'Адміністрація',
        'steps'  => [
            [
                'path' => '#',
                'name' => 'Адміністратори'
            ]
        ]
    ])

    <!-- Default box -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title " style="margin-right: 30px;">Список адміністраторів</h3>

            <form action="{{ route('admins.index') }}" method="GET" class="form-inline">
                <input type="text" name="name" class="form-control" placeholder="Пошук за ім'ям" value="{{ request('name') }}" style="width: 200px; margin-right: 10px;">
                <input type="text" name="email" class="form-control" placeholder="Пошук за імейлом" value="{{ request('email') }}" style="margin-right: 10px;">
                <select name="status" class="form-control" style="margin-right: 10px;">
                    <option value="">Виберіть статус</option>
                    <option value="1" {{ request('status') === 1 ? 'selected' : '' }}>Активний</option>
                    <option value="0" {{ request('status') === 0 ? 'selected' : '' }}>Неактивний</option>
                </select>
                <button type="submit" class="btn btn-primary">Фільтрувати</button>
            </form>

        </div>

        <div class="card-body p-0">

            <table class="table table-striped projects">
                <thead>
                <tr>
                    <th width="1%">
                        #
                    </th>
                    <th width="20%">
                        Ім'я
                    </th>
                    <th width="10%">
                        Імейл
                    </th>
                    <th width="10%">
                        Аватар
                    </th>
                    <th width="8%" class="text-center">
                        Статус
                    </th>
                    <th width="7%">
                        Дія
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($admins as $admin)
                    <tr>
                        <td>
                            {{$admin->id}}
                        </td>
                        <td>
{{--                            <a href="/admins/{{$admin->id}}">--}}

                                {{$admin->name}}
{{--                            </a>--}}
                            <br/>
                            <small>
                                Created {{$admin->created_at}}
                            </small>
                        </td>

                        <td>
                            <a>
                                {{$admin->email}}
                            </a>
                            <br/>

                        </td>
                        <td>

                            <img src="{{ $admin->avatar ? asset($admin->avatar) : asset('admin/images/123.jpg') }}" alt="Avatar" style="width: 50px; height: 50px;">
                        </td>
                        <td class="project-state">
                            <span class="badge badge-success">{{$admin->is_active ? "Активний":'Неактивний'}}</span>
                        </td>
                        <td class="project-actions text-right">
                            <a class="btn btn-info btn-sm btn-block" href="{{route('admins.edit', ['admin' => $admin])}}">
                                <i class="fas fa-pencil-alt">
                                </i>

                            </a>
                            <form id="delete-form" method="POST" action="{{route('admins.destroy', ['admin' => $admin])}}">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-danger btn-sm mt-2 btn-block"><i class="fas fa-trash">
                                    </i></button>
                            </form>
                        </td>
                    </tr>

                @endforeach

                </tbody>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
@endsection
