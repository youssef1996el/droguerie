@extends('Dashboard.app')
@section('content')
<script src="{{asset('js/Script_Facture/scriptGeneratedInvocei.js')}}"></script>
<script>
    var csrf_token                      = "{{csrf_token()}}";
    var invoicegenerated                = "{{url('invoicegenerated')}}";
    var getInfoFactureRandom            = "{{ url('getInfoFactureRandom') }}";
</script>
<div class="container-fluid">
    <div class="card card-body py-3">
        <div class="row align-items-center">
            <div class="col-12">
                <div class="d-sm-flex align-items-center justify-space-between">
                    <h4 class="mb-4 mb-sm-0 card-title">Gestion de Production</h4>
                    <nav aria-label="breadcrumb" class="ms-auto">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item d-flex align-items-center">
                                <a class="text-muted text-decoration-none d-flex" href="{{url('/home')}}">
                                    <iconify-icon icon="solar:home-2-line-duotone" class="fs-6"></iconify-icon>
                                </a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">
                                <span class="badge fw-medium fs-2 bg-primary-subtle text-primary">
                                    Facture
                                </span>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>


    <div class="card card-body">
        <h5 class="card-title border p-2 bg-light rounded-2">Fiche les factures</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-striped TableFactureGenerated">
                <thead>
                    <tr>
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

    <div class="modal fade " id="ModalGeneratedFacture" tabindex="-1" role="dialog" aria-labelledby="addContactModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen" role="document">
            <div class="modal-content ">
                <div class="modal-header d-flex align-items-center">
                    <h5 class="modal-title card-title border p-2 bg-white rounded-2 w-100 text-center">- Entre information dans facture</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="add-contact-box">
                        <div class="add-contact-content">
                            <div class="row ">
                                <div class="col-sm-12 col-md-12 col-xl-12">
                                    <div class="row mb-3">
                                        <div class="col-sm-6 col-md-6 col-xl-6">
                                            <button class="btn btn-info BtnAddNewline">Ajouter une nouvelle ligne</button>
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-xl-6">
                                            <button class="btn btn-danger BtnRemoveLastLine">Supprimer la dernière ligne</button>
                                        </div>
                                    </div>
                                    
                                    <table class="table table-striped table-bordered" id="TableFactureGenerated">
                                        <thead>
                                            <tr>
                                                <th>Référnce</th>
                                                <th>Libelle</th>
                                                <th>Prix unitaire</th>
                                                <th>Qte</th>
                                                <th>TVA (%)</th>
                                                <th>Total TTC (DH)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-sm-12 col-md-12 col-xl-6">
                                    <label for="">Date de facture :</label>
                                    <input type="date" class="form-control" name="date" value="{{ date('Y-m-d') }}" required>

                                    <label for="">Montant de facture :</label>
                                    <input type="number" class="form-control" name="montant" placeholder="Ex : 1000 " step="0.01" required>

                                    <label for="">ICE du client :</label>
                                    <input type="text" class="form-control" name="ice" placeholder="EX : 12345678900" required>

                                    <label for="">Mode de livrasion :</label>
                                    <input type="text" class="form-control" name="modelivraison" >

                                </div>
                                <div class="col-sm-12 col-md-12 col-xl-6">
                                    <label for="">Nom et Prénom du client :</label>
                                    <input type="text" class="form-control" name="client"  placeholder="Jack Jhon" required>

                                    <label for="">Mode de réglement</label>
                                    <input type="text" name="modepaiement" class="form-control">

                                    <label for="">N° de chèque :</label>
                                    <input type="text" name="numerocheque" class="form-control">

                                    <label for="" style="display: none">Numéro de facture :</label>
                                    <input type="button" class="form-control" name="numero" placeholder="Ex : 000001" id="IDFacutre" >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex gap-6 m-0">
                        <button  class="btn btn-success " type="submit">Sauvegarder</button>
                        <button class="btn bg-danger-subtle text-danger " data-bs-dismiss="modal"> fermer</button>
                    </div>
                </div>
                
                
            </div>
        </div>
    </div>
</div>
@endsection
