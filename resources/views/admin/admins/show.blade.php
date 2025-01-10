@extends('admin.admin_layout')

@section('title', 'admin')

@section('content')
{{--   @php dd($admin) @endphp--}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">User Details</h3>

            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                    <i class="fas fa-minus"></i></button>
                <button type="button" class="btn btn-tool" data-card-widget="remove" data-toggle="tooltip" title="Remove">
                    <i class="fas fa-times"></i></button>
            </div>
        </div>
    </div>

    <div class="card-body pt-0">
        <div class="row">
            <div class="col-7">
                <h2 class="lead"><b>User Name </b> {{$admin->name}}</h2>
                <p class="text-muted text-sm"><b>Email: </b> {{$admin->email}} </p>
                <p class="text-muted text-sm"><b>Status: </b> {{$admin->status ? "Active":'Inactive'}} </p>
                <p class="text-muted text-sm"><b>About: </b> Web Designer / UX / Graphic Artist / Coffee Lover </p>
                <ul class="ml-4 mb-0 fa-ul text-muted">
                    <li class="small"><span class="fa-li"><i class="fas fa-lg fa-building"></i></span> Address: Demo Street 123, Demo City 04312, NJ</li>
                    <li class="small"><span class="fa-li"><i class="fas fa-lg fa-phone"></i></span> Phone #: + 800 - 12 12 23 52</li>
                </ul>
            </div>
            <div class="col-5 text-center">
                <img src="../../dist/img/user1-128x128.jpg" alt="" class="img-circle img-fluid">
            </div>
        </div>
    </div>

@endsection
