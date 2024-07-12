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
                                    Utilisateurs
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
                    @can('utilisateur-ajoute')
                        <a href="{{ route('users.create') }}" id="BtnShowModalAddClient" class="btn btn-primary d-flex align-items-center">
                            <i class="ti ti-users text-white me-1 fs-5"></i> Ajouter le utilisateur
                        </a>
                    @endcan

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

            <tr>

                <th>No</th>

                <th>Name</th>

                <th>Email</th>

                <th>Roles</th>

                <th >Action</th>

            </tr>

            @foreach ($data as $key => $user)

                <tr>

                    <td>{{ ++$i }}</td>

                    <td>{{ $user->name }}</td>

                    <td>{{ $user->email }}</td>

                    <td>


                        @if(!empty($user->getRoleNames()))

                            @foreach($user->getRoleNames() as $role)
                                <label class="badge badge-success text-success">{{ $role }}</label>
                            @endforeach

                        @endif

                    </td>

                    <td>
                        @can('utilisateur-voir')
                            <a class="btn btn-info" href="{{ route('users.show',$user->id) }}">Voir</a>
                        @endcan

                        @can('utilisateur-modifier')
                            <a class="btn btn-primary" href="{{ route('users.edit',$user->id) }}">Modifier</a>
                        @endcan

                        @can('utilisateur-supprimer')
                        {!! Form::open(['method' => 'DELETE','route' => ['users.destroy', $user->id],'style'=>'display:inline']) !!}

                            {!! Form::submit('Supprimer', ['class' => 'btn btn-danger ']) !!}

                        {!! Form::close() !!}
                        @endcan


                    </td>

                </tr>

            @endforeach

        </table>
        {!! $data->render() !!}
        </div>
    </div>
</div>



@endsection
