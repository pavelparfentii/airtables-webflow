@extends('admin.admin_layout')

@section('title', 'Редагувати користувача')

@section('content')

    @include('admin.admins.form_head', [
        'title' => 'Редагувати',
        'steps'  => [
            [
                'path' => route('admins.index'),
                'name' => 'Користувачі'
            ],
            [
                'path' => '#',
                'name' => 'Редагувати'
            ]
        ]
    ])


    <section class="card">
        <div class="card-header">
            <h3 class="card-title">Редагувати дані користувача</h3>
        </div>
        <div class="card-body">
            <form
                action="{{route('admins.update', [
                'admin' => $admin
            ])}}" method="post">

                @include('admin.admins.form', ['admin', $admin])

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Оновити дані</button>
                </div>
            </form>
        </div>
    </section>
@endsection
