@extends('Dashboard.app')
@section('content')
<div class="container-fluid">
    <div class="card card-body py-3">
        <div class="row align-items-center">
            <div class="col-12">
                <div class="d-sm-flex align-items-center justify-space-between">
                    <h4 class="mb-4 mb-sm-0 card-title">ÉVALUATION DE LA SITUATION FINANCIÈRE</h4>
                    <nav aria-label="breadcrumb" class="ms-auto">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item d-flex align-items-center">
                                <a class="text-muted text-decoration-none d-flex" href="{{url('/home')}}">
                                    <iconify-icon icon="solar:home-2-line-duotone" class="fs-6"></iconify-icon>
                                </a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">
                                <span class="badge fw-medium fs-2 bg-primary-subtle text-primary">
                                    Recouverement
                                </span>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="widget-content searchable-container list">
        <div class="card card-body">
            <div class="row">
                <div class="col-md-4 col-xl-4">
                    <div class="form-group d-flex align-items-center">
                        <label for="" class="me-2">Clients</label>
                        <select name="" id="IdClient" class="form-select">
                            <option value="0">veuillez sélectionner le client</option>
                            @foreach ($Clients as $item)
                                <option value="{{$item->id}}">{{ $item->client}} </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

        </div>
    </div> --}}
    <div class="card card-body">
        <h5 class="card-title border p-2 bg-light rounded-2">Fiche Recouverement</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-striped TableRecouverement">
                <thead>
                    <tr>
                        <th></th>
                        <th>Client</th>
                        <th>Montant Vente</th>
                        <th>Montant Payé</th>
                        <th>Montant Rest</th>
                        <th>Type</th>
                        <th>Compagnie</th>
                        <th>Créer par</th>
                        <th>Créer Le</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>


</div>
@endsection