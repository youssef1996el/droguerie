@extends('Dashboard.app')
@section('content')
<script src="{{asset('js/Script_Versement/script.js')}}"></script>
<script>
    var StoreVersement  = "{{url('StoreVersement')}}";
    var versement       = "{{url('versement')}}";
    var updateVersement       = "{{url('updateVersement')}}";
    var TrashVersement       = "{{url('TrashVersement')}}";
    var csrf_token                      = "{{csrf_token()}}";
</script>
<div class="container-fluid">
    <div class="card card-body py-3">
        <div class="row align-items-center">
            <div class="col-12">
                <div class="d-sm-flex align-items-center justify-space-between">
                    <h4 class="mb-4 mb-sm-0 card-title">Gestion de dépenses</h4>
                    <nav aria-label="breadcrumb" class="ms-auto">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item d-flex align-items-center">
                                <a class="text-muted text-decoration-none d-flex" href="{{url('/home')}}">
                                    <iconify-icon icon="solar:home-2-line-duotone" class="fs-6"></iconify-icon>
                                </a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">
                                <span class="badge fw-medium fs-2 bg-primary-subtle text-primary">
                                    Versement
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
                    @can('Versement-ajoute')
                        <a href="#"  class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#ModelVersement">
                            <i class="ti ti-building-bank text-white me-1 fs-5"></i> Ajouter le versement
                        </a>
                    @endcan

                </div>
            </div>
        </div>
    </div>

    <div class="card card-body">
        <h5 class="card-title border p-2 bg-light rounded-2">Fiche versement</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-striped TableVersement">
                <thead>
                    <tr>
                        <th>Comptable</th>
                        <th>Total</th>
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


    <div class="modal fade " id="ModelVersement" tabindex="-1" role="dialog" aria-labelledby="addContactModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h5 class="modal-title">Fiche Versement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="add-contact-box">
                        <div class="add-contact-content">

                            <ul class="ValidationVersement"></ul>
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3 contact-name">
                                        <input type="text" id="Comptable" name="Comptable" class="form-control" placeholder="(obligatoire)" required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3 contact-name">
                                        <input type="number" id="total" name="total" class="form-control" placeholder="montant versé" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex gap-6 m-0">
                        <button  class="btn btn-success" id="BtnSaveVersement">Sauvegarder</button>
                        <button class="btn bg-danger-subtle text-danger" data-bs-dismiss="modal"> fermer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade " id="ModelVersementEdit" tabindex="-1" role="dialog" aria-labelledby="addContactModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h5 class="modal-title">Modifier  Versement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="add-contact-box">
                        <div class="add-contact-content">

                            <ul class="ValidationVersementEdit"></ul>
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3 contact-name">
                                        <input type="text" id="comptableEdit" name="name" class="form-control" placeholder="Comptable (obligatoire)" required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3 contact-name">
                                        <input type="number" id="totalEdit" name="total" class="form-control" placeholder="montant versé" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex gap-6 m-0">
                        <button  class="btn btn-success" id="BtnEditVersement">Sauvegarder</button>
                        <button class="btn bg-danger-subtle text-danger" data-bs-dismiss="modal"> fermer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
