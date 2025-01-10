@extends('admin.admin_layout')

@section('title', 'Створити користувача')

@section('content')

    @include('admin.admins.form_head', [
        'title' => 'Новий адміністратор',
        'steps'  => [
            [
                'path' => route('admins.index'),
                'name' => 'Список '
            ],
            [
                'path' => '#',
                'name' => 'Додати адміністратора'
            ]
        ]
    ])

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Додати нового адмністратора</h3>

        </div>

        <form action="{{route('admins.store')}}" method="post">
            <div class="form-row">
                @include('admin.admins.form', ['admin', $admin])
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Додати</button>
            </div>
        </form>
@endsection
