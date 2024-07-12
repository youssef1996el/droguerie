@extends('Dashboard.app')
@section('content')
<script src="{{asset('js/Script_Setting/script.js')}}"></script>
<script>
    var csrf_token                      = "{{csrf_token()}}";
    var FetchSetting                     = "{{url('FetchSetting')}}";
    var StoreSetting                    = "{{url('StoreSetting')}}";
    var UpdateSetting                    = "{{url('UpdateSetting')}}";
    var TrashSetting                    = "{{url('TrashSetting')}}";
    var getNameProductByBonAndCategory  = "{{url('getNameProductByBonAndCategory')}}";
    var getSettingByID                  = "{{url('getSettingByID')}}";

</script>
<div class="container-fluid">
    <div class="card card-body py-3">
        <div class="row align-items-center">
            <div class="col-12">
                <div class="d-sm-flex align-items-center justify-space-between">
                    <h4 class="mb-4 mb-sm-0 card-title">Gestion de Paramètre</h4>
                    <nav aria-label="breadcrumb" class="ms-auto">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item d-flex align-items-center">
                                <a class="text-muted text-decoration-none d-flex" href="{{url('/home')}}">
                                    <iconify-icon icon="solar:home-2-line-duotone" class="fs-6"></iconify-icon>
                                </a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">
                                <span class="badge fw-medium fs-2 bg-primary-subtle text-primary">
                                    Paramètre
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
                    @can('paramètre-ajoute')
                        <a href="#" id="BtnShowModalAddSetting" class="btn btn-primary d-flex align-items-center">
                            <i class="ti ti-settings text-white me-1 fs-5"></i> Ajouter le paramètre
                        </a>
                    @endcan

                </div>
            </div>
        </div>
    </div>

    <div class="card card-body">
        <h5 class="card-title border p-2 bg-light rounded-2">Fiche paramètre</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-striped TableSetting">
                <thead class="text-nowrap fs-2">
                    <tr>
                        <th>N° Bon</th>
                        <th>Nom Produit</th>
                        <th>Unité</th>
                        <th>Quantité</th>
                        <th>Poids</th>
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

    <div class="modal fade " id="AddSetting" tabindex="-1" role="dialog" aria-labelledby="addContactModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h5 class="modal-title">Ajouter paramatère</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="add-contact-box">
                        <div class="add-contact-content">

                            <ul class="ValidationSetting"></ul>
                            <div class="row">
                                <div class="col-sm-12 col-md-12 col-xl-6">
                                    <div class="mb-3 contact-name">
                                        <label for="">Catégorie :</label>
                                        <select name="" id="DropDownCategory" class="form-select">
                                            <option value="0">Veuillez sélectionner le catégorie</option>
                                            @foreach ($CategoryCompanyActive as $item)
                                                <option value="{{$item->id}}">{{$item->name}}</option>
                                            @endforeach
                                        </select>


                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-xl-6">
                                    <div class="mb-3 contact-name">
                                        <label for="">N° Bon</label>
                                        <select name="" id="" class="form-select DrowDownBon">
                                            <option value="0">Veuillez sélectionner N° bon</option>
                                            @foreach ($BonEntre as $item)
                                                <option value="{{$item->id}}">{{$item->numero_bon}}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-xl-6">
                                    <div class="mb-3 contact-name">
                                        <label for="">Nom produit :</label>
                                        <select name="" id="name_products" class="form-select"></select>
                                        {{-- <input type="text" class="form-control" id="name_product" placeholder="Nom (obligatoire)"> --}}
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-xl-6">
                                    <div class="mb-3 contact-name">
                                        <label for="">unité (1) :</label>
                                        <input type="text" class="form-control" id="unite" name="type" placeholder="Unité (obligatoire)">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-12 col-xl-6">
                                    <div class="mb-3 contact-name">
                                        <label for="">Taux de conversion (Poids KG) :</label>
                                        <input type="number" class="form-control" id="conversion_rate" name="conversion_rate" placeholder="taux de conversion (obligatoire)">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex gap-6 m-0">
                        <button  class="btn btn-success" id="BtnSaveSetting">Sauvegarder</button>
                        <button class="btn bg-danger-subtle text-danger" data-bs-dismiss="modal"> fermer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>




    {{-- Modal Edit --}}
    <div class="modal fade " id="EditSetting" tabindex="-1" role="dialog" aria-labelledby="addContactModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h5 class="modal-title">Modification paramètre</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="add-contact-box">
                        <div class="add-contact-content">

                            <ul class="ValidationSettingEdit"></ul>
                            <div class="row">
                                <div class="col-sm-12 col-md-12 col-xl-6">
                                    <label for="">Catégorie :</label>
                                    <select name="" id="DropDownCategoryEdit" class="form-select">
                                        <option value="0">Veuillez sélectionner le catégorie</option>
                                        @foreach ($CategoryCompanyActive as $item)
                                            <option value="{{$item->id}}">{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-12 col-md-12 col-xl-6">

                                    <div class="mb-3 contact-name">
                                        <label for="">N° Bon</label>
                                        <select name="" id="" class="form-select DrowDownBonEdit">
                                            <option value="0">Veuillez sélectionner N° bon</option>
                                            @foreach ($BonEntre as $item)
                                                <option value="{{$item->id}}">{{$item->numero_bon}}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-12 col-xl-6">
                                    <div class="mb-3 contact-name">
                                        <label for="">Nom produit :</label>
                                        <select name="" id="name_productEdit" class="form-select"></select>
                                        {{-- <input type="text" class="form-control" id="name_productEdit" placeholder="Nom (obligatoire)"> --}}

                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-xl-6">
                                    <div class="mb-3 contact-name">
                                        <label for="">unité (1) :</label>
                                        <input type="text" class="form-control" id="uniteEdit" name="type" placeholder="Unité (obligatoire)">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-12 col-xl-6">
                                    <div class="mb-3 contact-name">
                                        <label for="">Taux de conversion :</label>
                                        <input type="text" class="form-control" id="convertEdit" name="conversion_rate" placeholder="taux de conversion (obligatoire)">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex gap-6 m-0">
                        <button  class="btn btn-success" id="BtnEditSetting">Sauvegarder</button>
                        <button class="btn bg-danger-subtle text-danger" data-bs-dismiss="modal"> fermer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
