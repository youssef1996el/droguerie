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
                                    Les roles
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
                    @can('rôles-ajoute')
                        <a href="{{ route('roles.create') }}" id="BtnShowModalAddClient" class="btn btn-primary d-flex align-items-center">
                            <i class="text-white me-1 fs-5"></i>Ajouter role
                        </a>
                    @endcan

                </div>
            </div>
        </div>
    </div>
    <div class="card card-body">
        <h5 class="card-title border p-2 bg-light rounded-2 text-center text-uppercase">Fiche Roles</h5>
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                   <th>No</th>
                   <th>Name</th>
                   <th >Action</th>
                </tr>
                @foreach ($roles as $key => $role)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $role->name }}</td>
                        <td>
                            @can('rôles-voir')
                                <a class="btn btn-info" href="{{ route('roles.show',$role->id) }}">Voir</a>
                            @endcan

                            @can('rôles-modifier')
                                <a class="btn btn-primary" href="{{ route('roles.edit',$role->id) }}">Modifier</a>
                            @endcan
                            @can('rôles-supprimer')
                                {!! Form::open(['method' => 'DELETE','route' => ['roles.destroy', $role->id],'style'=>'display:inline']) !!}
                                    {!! Form::submit('Supprimer', ['class' => 'btn btn-danger']) !!}
                                {!! Form::close() !!}
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </table>
            {!! $roles->render() !!}
        </div>
    </div>
</div>
@endsection
