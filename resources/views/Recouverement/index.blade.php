@extends('Dashboard.app')
@section('content')
<script src="{{asset('js/Script_recouverement/script.js')}}"></script>
<script>
    var IdCompanyActiveExtren       = @Json($CompanyIsActive);
    var GetRecouvementClient        = "{{url('GetRecouvementClient')}}";
    var GetDataSelectedRecouvement  = "{{url('GetDataSelectedRecouvement')}}";
    var ModePaiement                = @Json($ModePaiement);
    var csrf_token                  = "{{csrf_token()}}";
    var StoreRecouvement            = "{{url('StoreRecouvement')}}";
    var TrashCredit                 = "{{url('TrashCredit')}}";
</script> 
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

    <div class="widget-content searchable-container list">
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
    </div>
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
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card card-body">
        <h5 class="card-title border p-2 bg-light rounded-2">Recouverement sélectionner</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-striped TableRecouverementSelected">
                <thead>
                    <tr>

                        <th>Client</th>
                        <th>Montant Vente</th>
                        <th>Montant Payé</th>
                        <th>Montant Rest</th>
                        <th>Type</th>
                        <th>Mode paiement</th>
                        <th>Montant saisir</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row DivCheque" style="display: none">
        <div class="col-sm-12 col-md-12 col-xl-12">
            <div class="card card-body border rounded-2 p-2">
                <h5 class="card-title border p-2 bg-light rounded-2 text-center text-uppercase fst-italic">Tableau information cheque</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="fs-2 text-nowrap">
                            <tr>
                                <th class="col-xxl-2">Numéro</th>
                                <th class="col-xxl-2" style="text-align: center">Date Cheque</th>
                                <th class="col-xxl-2" style="text-align: center">Date Promise</th>
                                <th class="col-xxl-2" style="text-align: center">Total</th>
                                <th class="col-xxl-2" style="text-align: center">Type</th>
                                <th class="col-xxl-2" style="text-align: center">Nom complet</th>
                                <th class="col-xxl-2" style="text-align: center">Banque</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="col-xxl-2">
                                    <input type="number" class="form-control numero">
                                </td>
                                <td class="col-xxl-2">
                                    <input type="date" class="form-control datecheque">
                                </td>
                                <td class="col-xxl-2">
                                    <input type="date" class="form-control datepromise">
                                </td>
                                <td class="col-xxl-2">
                                    <input type="number" class="form-control montant"  min="1">
                                </td>
                                <td class="col-xxl-2">
                                    <input type="text" class="form-control type" >
                                </td>
                                <td class="col-xxl-2">
                                    <input type="text" class="form-control  name" >
                                </td>
                                <td class="col-xxl-2">
                                    <input type="text" class="form-control bank" >
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

    </div>
    @can('recouverement-payé')
        <button class="btn btn-success" id="Encaissement">Encaissement</button>
    @endcan

</div>
@endsection
