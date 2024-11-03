@extends('Dashboard.app')
@section('content')
<script>
   
    var ListPaiement = "{{url('ListPaiement')}}";
</script>
<script src="{{asset('js/Script_ListPaiement/script.js')}}"></script>
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
                                    List de paiements
                                </span>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="card card-body">
        <h5 class="card-title border p-2 bg-light rounded-2">Fiche List de paiements</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-striped TableListPaiement">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Montant Payé</th>
                        
                        <th>Date Payé</th>
                        <th>Status</th>
                        <th>Mode paiement</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody> 
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection