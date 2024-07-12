@extends('Dashboard.app')
@section('content')
    <script src="{{asset('js/Script_Client/script.js')}}"></script>
    <script>
        var csrf_token   = "{{csrf_token()}}";
        var StoreClient  = "{{url('StoreClient')}}";
        var FicheClient  = "{{url('FicheClient')}}";
        var UpdateClient  = "{{url('UpdateClient')}}";
        var TrashClient  = "{{url('TrashClient')}}";
    </script>
    <div class="container-fluid">
        <div class="card card-body py-3">
            <div class="row align-items-center">
                <div class="col-12">
                    <div class="d-sm-flex align-items-center justify-space-between">
                        <h4 class="mb-4 mb-sm-0 card-title">Gestion de Client</h4>
                        <nav aria-label="breadcrumb" class="ms-auto">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item d-flex align-items-center">
                                    <a class="text-muted text-decoration-none d-flex" href="{{url('/home')}}">
                                        <iconify-icon icon="solar:home-2-line-duotone" class="fs-6"></iconify-icon>
                                    </a>
                                </li>
                                <li class="breadcrumb-item" aria-current="page">
                                    <span class="badge fw-medium fs-2 bg-primary-subtle text-primary">
                                        Client
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
                        @can('clients-ajoute')
                            <a href="#" id="BtnShowModalAddClient" class="btn btn-primary d-flex align-items-center">
                                <i class="ti ti-users text-white me-1 fs-5"></i> Ajouter le client
                            </a>
                        @endcan

                    </div>
                </div>
            </div>
        </div>
        <div class="card card-body">
            <h5 class="card-title border p-2 bg-light rounded-2">Fiche Client</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped TableClient">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>ٌCin</th>
                            <th>Adresse</th>
                            <th>ٌVille</th>
                            <th>Téléphone</th>
                            <th>Plafonnier</th>
                            <th>Compagnie</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>


    </div>
    <div class="modal fade " id="AddClient" tabindex="-1" role="dialog" aria-labelledby="addContactModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h5 class="modal-title">Ajoute Client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="add-contact-box">
                        <div class="add-contact-content">

                            <ul class="ValidationClient"></ul>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 contact-name">
                                        <input type="text" id="nom" name="nom" class="form-control" placeholder="Nom (obligatoire)" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 contact-email">
                                        <input type="text" id="prenom" name="prenom" class="form-control" placeholder="Prénom (obligatoire)" required>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 contact-occupation">
                                        <input type="text" id="cin" name="cin" class="form-control" placeholder="Cin" >

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 contact-phone">
                                    <input type="tel" id="phone" name="phone" class="form-control" placeholder="Téléphone (obligatoire)" maxlength="10" required>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 contact-location">
                                        <select name="ville" id="ville" class="form-select">
                                            <option value="0">Veuillez entrer votre ville</option>
                                            <option value="agadir">Agadir</option>
                                            <option value="beni-mellal">Beni Mellal</option>
                                            <option value="berrechid">Berrechid</option>
                                            <option value="casablanca">Casablanca</option>
                                            <option value="chefchaouen">Chefchaouen</option>
                                            <option value="el-jadida">El Jadida</option>
                                            <option value="essaouira">Essaouira</option>
                                            <option value="fes">Fes</option>
                                            <option value="guelmim">Guelmim</option>
                                            <option value="kenitra">Kenitra</option>
                                            <option value="khenifra">Khenifra</option>
                                            <option value="larache">Larache</option>
                                            <option value="marrakech">Marrakech</option>
                                            <option value="meknes">Meknes</option>
                                            <option value="nador">Nador</option>
                                            <option value="ouarzazate">Ouarzazate</option>
                                            <option value="oujda">Oujda</option>
                                            <option value="rabat">Rabat</option>
                                            <option value="safi">Safi</option>
                                            <option value="saidia">Saidia</option>
                                            <option value="tangier">Tangier</option>
                                            <option value="taroudant">Taroudant</option>
                                            <option value="taza">Taza</option>
                                            <option value="tetouan">Tetouan</option>
                                            <option value="tiznit">Tiznit</option>
                                            <option value="zagora">Zagora</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 contact-location">
                                        <input type="text" id="adresse" class="form-control" placeholder="Adresse" >
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 contact-location">
                                        <input type="number" id="plafonnier" class="form-control" placeholder="Limite maximale de crédit par client." >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex gap-6 m-0">
                        <button  class="btn btn-success" id="BtnSaveClient">Sauvegarder</button>
                        <button class="btn bg-danger-subtle text-danger" data-bs-dismiss="modal"> fermer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade " id="EditClient" tabindex="-1" role="dialog" aria-labelledby="addContactModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h5 class="modal-title">Modifier client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="add-contact-box">
                        <div class="add-contact-content">

                            <ul class="ValidationClientEdit"></ul>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 contact-name">
                                        <input type="text" id="nomEdit" name="nom" class="form-control" placeholder="Nom (obligatoire)" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 contact-email">
                                        <input type="text" id="prenomEdit" name="prenom" class="form-control" placeholder="Prénom (obligatoire)" required>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 contact-occupation">
                                        <input type="text" id="cinEdit" name="cin" class="form-control" placeholder="Cin" >

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 contact-phone">
                                    <input type="tel" id="phoneEdit" name="phone" class="form-control" placeholder="Téléphone (obligatoire)" maxlength="10" required>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 contact-location">
                                        <select name="ville" id="villeEdit" class="form-select">
                                            <option value="0">Veuillez entrer votre ville</option>
                                            <option value="agadir">Agadir</option>
                                            <option value="beni-mellal">Beni Mellal</option>
                                            <option value="berrechid">Berrechid</option>
                                            <option value="casablanca">Casablanca</option>
                                            <option value="chefchaouen">Chefchaouen</option>
                                            <option value="el-jadida">El Jadida</option>
                                            <option value="essaouira">Essaouira</option>
                                            <option value="fes">Fes</option>
                                            <option value="guelmim">Guelmim</option>
                                            <option value="kenitra">Kenitra</option>
                                            <option value="khenifra">Khenifra</option>
                                            <option value="larache">Larache</option>
                                            <option value="marrakech">Marrakech</option>
                                            <option value="meknes">Meknes</option>
                                            <option value="nador">Nador</option>
                                            <option value="ouarzazate">Ouarzazate</option>
                                            <option value="oujda">Oujda</option>
                                            <option value="rabat">Rabat</option>
                                            <option value="safi">Safi</option>
                                            <option value="saidia">Saidia</option>
                                            <option value="tangier">Tangier</option>
                                            <option value="taroudant">Taroudant</option>
                                            <option value="taza">Taza</option>
                                            <option value="tetouan">Tetouan</option>
                                            <option value="tiznit">Tiznit</option>
                                            <option value="zagora">Zagora</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 contact-location">
                                        <input type="text" id="adresseEdit" class="form-control" placeholder="Adresse" >
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 contact-location">
                                        <input type="number" id="plafonnierEdit" class="form-control" placeholder="plafonnier" >
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex gap-6 m-0">
                        <button  class="btn btn-success" id="BtnUpdateClient">Sauvegarder</button>
                        <button class="btn bg-danger-subtle text-danger" data-bs-dismiss="modal"> fermer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
