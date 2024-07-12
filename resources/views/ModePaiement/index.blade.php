@extends('Dashboard.app')
@section('content')
<script src="{{asset('js/Script_ModePaiement/script.js')}}"></script>
<script>
    var StoreModePaiement   = "{{url('StoreModePaiement')}}";
    var TrashModePaiement   = "{{url('TrashModePaiement')}}";
    var UpdateModePaiement  = "{{url('UpdateModePaiement')}}";
    var FetchModePaiementByCompanyActive = "{{url('FetchModePaiementByCompanyActive')}}";
    var csrf_token   = "{{csrf_token()}}";
</script>

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
                                    Mode de paiement
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
                <div class="col-md-5 col-xl-5">
                    @can('mode paiement-ajoute')
                        <a href="#" id="BtnShowModalAddModePaiement" class="btn btn-primary">
                            <i class="ti ti-coin text-white me-1 fs-5"></i> Ajouter le mode paiement
                        </a>
                    @endcan

                </div>
            </div>
        </div>
    </div>


    <div class="card card-body">
        <h5 class="card-title border p-2 bg-light rounded-2">Fiche mode de paiement</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-striped TableModePaiement">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Compagnie</th>
                        <th>Créer par</th>
                        <th>Créer le</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>


    <div class="modal fade" id="AddModePaiement" tabindex="-1" role="dialog" aria-labelledby="addContactModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h5 class="modal-title">Ajoute mode paiement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="add-contact-box">
                        <div class="add-contact-content">
                            <ul class="ValidationPaiement"></ul>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3 contact-name">
                                        <input type="text" id="title" name="name" class="form-control" placeholder="titre (obligatoire)" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex gap-6 m-0">
                        <button  class="btn btn-success" id="BtnSaveModePaiement">Sauvegarder</button>
                        <button class="btn bg-danger-subtle text-danger" data-bs-dismiss="modal"> fermer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Model Edit --}}

    <div class="modal fade" id="EditModePaiement" tabindex="-1" role="dialog" aria-labelledby="addContactModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h5 class="modal-title">Modifier mode paiement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="add-contact-box">
                        <div class="add-contact-content">
                            <ul class="ValidationPaiement"></ul>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3 contact-name">
                                        <input type="text" id="Edittitle" name="name" class="form-control" placeholder="titre (obligatoire)" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex gap-6 m-0">
                        <button  class="btn btn-success" id="BtnUpdateModePaiement">Sauvegarder</button>
                        <button class="btn bg-danger-subtle text-danger" data-bs-dismiss="modal"> fermer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
