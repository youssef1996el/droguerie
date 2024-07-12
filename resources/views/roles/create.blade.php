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
                                    Ajoute Role
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
                    <a href="{{ route('roles.index') }}" id="BtnShowModalAddClient" class="btn btn-primary d-flex align-items-center">
                        <i class="s text-white me-1 fs-5"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card card-body">
        <h5 class="card-title border p-2 bg-light rounded-2 text-center text-uppercase ">Information utilisateur</h5>
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {!! Form::open(array('route' => 'roles.store','method'=>'POST')) !!}
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Name:</strong>
                    {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control')) !!}
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label for="permissions">Permissions:</label>
                    <div class="form-check">
                       {{--  @foreach($permission as $permission)
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="permission[]" value="{{ $permission->id }}">
                                    {{ $permission->name }}
                                </label>
                            </div>
                        @endforeach --}}
                    </div>
                    <div class="form-check">
                        <div class="row">
                            @foreach($permission as $group => $groupPermissions)
                                <div class="col-sm-12 col-md-12 col-xl-3">
                                    <div class="panel panel-default card bg-white p-2 shadow" style="min-height: 149px">
                                        <div class="panel-heading">
                                            <h3 class="panel-title bg-light text-center fs-4 rounded-2">{{ ucfirst($group) }}</h3>
                                        </div>
                                        <div class="panel-body">
                                            @foreach($groupPermissions as $permission)
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="permission[]" value="{{ $permission->id }}" >
                                                        {{ $permission->name }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>


                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>
@endsection
