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
                                    Ajoute Utilisateur
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
                        <i class="text-white me-1 fs-5"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-body">
        <h5 class="card-title border p-2 bg-light rounded-2 text-center text-uppercase ">Information utilisateur</h5>
        @if (count($errors) > 0)

            <div class="alert alert-danger mt-3">
                <strong>Oups!</strong> Il y a eu quelques problèmes avec votre saisie.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {!! Form::open(array('route' => 'users.store','method'=>'POST' ,'files' => true)) !!}
        <div class="row">
            <div class="col-sm-12 col-xl-6  col-md-12">
                <div class="form-group">
                    <strong>Nom complet:</strong>
                    {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control')) !!}
                </div>
            </div>
            <div class="col-sm-12 col-xl-6  col-md-12">
                <div class="form-group">
                    <strong>Email:</strong>
                    {!! Form::text('email', null, array('placeholder' => 'Email','class' => 'form-control')) !!}
                </div>
            </div>
            <div class="col-sm-12 col-xl-6  col-md-12">
                <div class="form-group">
                    <strong>Mot de passe:</strong>
                    {!! Form::password('password', array('placeholder' => 'Password','class' => 'form-control')) !!}
                </div>
            </div>
            <div class="col-sm-12 col-xl-6  col-md-12">
                <div class="form-group">
                    <strong>Confirme Password:</strong>
                    {!! Form::password('confirm-password', array('placeholder' => 'Confirm Password','class' => 'form-control')) !!}
                </div>
            </div>
            <div class="col-sm-12 col-xl-6  col-md-12">
                <div class="form-group">
                    <strong>Statut :</strong>
                   <select name="status" id="" class="form-select">
                        <option value="active">Active</option>
                        <option value="no active">Désactiver</option>
                   </select>
                </div>
            </div>
            <div class="col-sm-12 col-xl-6  col-md-12">
                <div class="form-group">
                    <strong>Role:</strong>
                    {!! Form::select('roles[]', $roles,[], array('class' => 'form-control','multiple')) !!}
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 mt-2">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>
@endsection
