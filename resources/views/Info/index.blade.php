@extends('Dashboard.app')
@section('content')
<script src="{{asset('js/Script_Information/script.js')}}"></script>
<script>
    var FetchInformation = "{{url('FetchInformation')}}";
    var StoreInformation = "{{url('StoreInformation')}}";
    var UpdateInformation = "{{url('UpdateInformation')}}";
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
                                    Information
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
                    @can('information-ajoute')
                        <a href="#" id="BtnShowModalAddInformation" class="btn btn-primary d-flex align-items-center">
                            <i class="ti ti-users text-white me-1 fs-5"></i> Ajouter information
                        </a>
                    @endcan

                </div>
            </div>
        </div>
    </div>
    <div class="card card-body">
        <h5 class="card-title border p-2 bg-light rounded-2">Fiche Information</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-striped TableInformation">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>ICE</th>
                        <th>ٌCNSS</th>
                        <th>Adresse</th>
                        <th>ٌRC</th>
                        <th>IF</th>
                        <th>Téléphone</th>
                        <th>Fix</th>
                        <th>Compangine</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade " id="ModelAddInformation" tabindex="-1" role="dialog" aria-labelledby="addContactModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h5 class="modal-title">Ajouter Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="add-contact-box">
                        <div class="add-contact-content">

                            <ul class="ValidationInformation"></ul>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 contact-name">
                                        <input type="text" id="title" name="title" class="form-control" placeholder="titre (obligatoire)" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 contact-email">
                                        <input type="text" id="ICE" name="ICE" class="form-control" placeholder="ICE (obligatoire)" required>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 contact-occupation">
                                        <input type="text" id="CNSS" name="CNSS" class="form-control" placeholder="CNSS (obligatoire)" >

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 contact-phone">
                                    <input type="text" id="RC" name="RC" class="form-control" placeholder="RC (obligatoire)" required>

                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="mb-3 contact-location">
                                        <input type="text" id="IF" class="form-control" placeholder="IF (obligatoire)" >
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 contact-location">
                                        <input type="text" id="Adresse" class="form-control" placeholder="Adresse (obligatoire)" >
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="mb-3 contact-location">
                                        <input type="tel" id="phone" class="form-control" placeholder="Téléphone (obligatoire)" maxlength="10" >
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 contact-location">
                                        <input type="tel" id="Fix" class="form-control" placeholder="Fixe (obligatoire)" maxlength="10" >
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex gap-6 m-0">
                        <button  class="btn btn-success" id="BtnSaveInfo">Sauvegarder</button>
                        <button class="btn bg-danger-subtle text-danger" data-bs-dismiss="modal"> fermer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade " id="ModelUpdateInformation" tabindex="-1" role="dialog" aria-labelledby="addContactModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h5 class="modal-title">Modifier Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="add-contact-box">
                        <div class="add-contact-content">

                            <ul class="ValidationInformationEdit"></ul>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 contact-name">
                                        <input type="text" id="titleEdit" name="title" class="form-control" placeholder="titre (obligatoire)" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 contact-email">
                                        <input type="text" id="ICEEdit" name="ICE" class="form-control" placeholder="ICE (obligatoire)" required>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 contact-occupation">
                                        <input type="text" id="CNSSEdit" name="CNSS" class="form-control" placeholder="CNSS (obligatoire)" >

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 contact-phone">
                                    <input type="text" id="RCEdit" name="RC" class="form-control" placeholder="RC (obligatoire)" required>

                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="mb-3 contact-location">
                                        <input type="text" id="IFEdit" class="form-control" placeholder="IF (obligatoire)" >
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 contact-location">
                                        <input type="text" id="AdresseEdit" class="form-control" placeholder="Adresse (obligatoire)" >
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="mb-3 contact-location">
                                        <input type="tel" id="phoneEdit" class="form-control" placeholder="Téléphone (obligatoire)" maxlength="10" >
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 contact-location">
                                        <input type="tel" id="FixEdit" class="form-control" placeholder="Fixe (obligatoire)" maxlength="10" >
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex gap-6 m-0">
                        <button  class="btn btn-success" id="BtnEditInfo">Sauvegarder</button>
                        <button class="btn bg-danger-subtle text-danger" data-bs-dismiss="modal"> fermer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
@endsection
