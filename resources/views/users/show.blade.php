@extends('Dashboard.app')
@section('content')
<div class="container-fluid">
    <div class="card card-body py-3">
        <div class="row align-items-center">
            <div class="col-12">
                <div class="d-sm-flex align-items-center justify-space-between">
                    <h4 class="mb-4 mb-sm-0 card-title">Gestion de paramètre</h4>
                    <nav aria-label="breadcrumb" class="ms-auto">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item d-flex align-items-center">
                                <a class="text-muted text-decoration-none d-flex" href="{{url('/home')}}">
                                    <iconify-icon icon="solar:home-2-line-duotone" class="fs-6"></iconify-icon>
                                </a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">
                                <span class="badge fw-medium fs-2 bg-primary-subtle text-primary">
                                    Voir Utilisateurs
                                </span>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="widget-content searchable-container list">
        <div class="card card-body">
            <div class="row">
                <div class="col-md-4 col-xl-3">
                    <a href="{{ route('users.index') }}" id="BtnShowModalAddClient" class="btn btn-primary d-flex align-items-center">
                        <i class="ti ti-users text-white me-1 fs-5"></i> les utilisateurs
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-body">
        <h5 class="card-title border p-2 bg-light rounded-2">Fiche Utilisateur</h5>
        <div class="table-responsive">
            @if ($message = Session::get('success'))
            <div class="alert alert-success mt-3">
                <p>{{ $message }}</p>
            </div>
        @endif
        <table class="table table-bordered m-3">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Roles</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                      {{--  @if(!empty($user->getRoleNames()))
                            @foreach($user->getRoleNames() as $v)
                                <label class="badge badge-success">{{ $v }}</label>
                            @endforeach
                        @endif --}}
                        @if(!empty($user->getRoleNames()))

                        @foreach($user->getRoleNames() as $role)
                            <label class="badge badge-success text-success">{{ $role }}</label>
                        @endforeach

                    @endif

                    </td>
                </tr>
            </tbody>
        </table>

        </div>
    </div>
</div>


@endsection



{{-- <div class="page-content">
    <div class="container-fluid"> --}}
       {{--  <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Authentication</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Authentication</a></li>
                            <li class="breadcrumb-item active">Users Management</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div> --}}

       {{--  <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2> Show User</h2>
                </div>
                <div class="pull-right">
                    <a class="btn btn-primary" href="{{ route('users.index') }}"> Back</a>
                </div>
            </div>
        </div> --}}

        {{-- <div class="row">
            <table class="table table-bordered m-3">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Roles</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td> --}}
                           {{--  @if(!empty($user->getRoleNames()))
                                @foreach($user->getRoleNames() as $v)
                                    <label class="badge badge-success">{{ $v }}</label>
                                @endforeach
                            @endif --}}
                            {{-- @if(!empty($user->getRoleNames()))

                            @foreach($user->getRoleNames() as $role)
                                <label class="badge badge-success text-success">{{ $role }}</label>
                            @endforeach

                        @endif

                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>
</div> --}}





