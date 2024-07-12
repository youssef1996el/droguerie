@extends('Dashboard.app')
@section('content')
<script src="{{asset('js/Script_Stock/script.js')}}"></script>
<script>
    var StoreStock   = "{{url('StoreStock')}}";
    var csrf_token   = "{{csrf_token()}}";
    var getStock     = "{{url('getStock')}}";
    var GetRowSelectedByTable = "{{url('GetRowSelectedByTable')}}";
    var UpdateStock  = "{{url('UpdateStock')}}";
    var getUniteByCategory = "{{url('getUniteByCategory')}}";
    var listNameProducts = @Json($Product->pluck('name_product'));
    var CategoryCompanyActive = @Json($CategoryCompanyActive);
    var TrashStock = "{{url('TrashStock')}}";
</script>
<style>
    .ui-autocomplete { z-index:2147483647; }
    .TableStock th
    {
        font-size: 12px
    }
</style>
<div class="container-fluid">
    <div class="card card-body py-3">
        <div class="row align-items-center">
            <div class="col-12">
                <div class="d-sm-flex align-items-center justify-space-between">
                    <h4 class="mb-4 mb-sm-0 card-title">Gestion de Stock</h4>
                    <nav aria-label="breadcrumb" class="ms-auto">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item d-flex align-items-center">
                                <a class="text-muted text-decoration-none d-flex" href="{{url('/home')}}">
                                    <iconify-icon icon="solar:home-2-line-duotone" class="fs-6"></iconify-icon>
                                </a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">
                                <span class="badge fw-medium fs-2 bg-primary-subtle text-primary">
                                    Stock
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
                    @can('stock-ajoute')
                        <a href="#" id="BtnShowModalAddStock" class="btn btn-primary d-flex align-items-center">
                            <i class="ti ti-package text-white me-1 fs-5"></i> Ajouter le stock
                        </a>
                    @endcan

                </div>
            </div>
        </div>
    </div>

    <div class="card card-body">
        <h5 class="card-title border p-2 bg-light rounded-2">Fiche Stock</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-striped TableStock">
                <thead>
                    <tr>
                        <th></th> <!-- Column for toggle icon -->
                        <th>N° Bon</th>
                        <th>Date Bon</th>
                        <th>Numéro</th>
                        <th>ٌCommercial</th>
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
</div>


<div class="modal fade " id="AddStock" tabindex="-1" role="dialog" aria-labelledby="addContactModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex align-items-center">
                <h5 class="modal-title">Ajouter de  Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="add-contact-box">
                    <div class="add-contact-content">

                        <ul class="ValidationStock"></ul>
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-xl-6">
                                <label for="">N° Bon :</label>
                                <input type="text" class="form-control" id="numero_bon" placeholder="N° bon (obligatore)">
                            </div>
                            <div class="col-sm-12 col-md-12 col-xl-6">
                                <label for="">Date :</label>
                                <input type="date" class="form-control" id="date" placeholder="Date (obligatore)">
                            </div>

                            <div class="col-sm-12 col-md-12 col-xl-6">
                                <label for="">Numéro :</label>
                                <input type="number" class="form-control" id="numero" placeholder="Numero (obligatore)">
                            </div>
                            <div class="col-sm-12 col-md-12 col-xl-6">
                                <label for="">Commercial :</label>
                                <input type="text" class="form-control" id="commercial" placeholder="Commercial (obligatore)">
                            </div>

                            <div class="col-sm-12 col-md-12 col-xl-6">
                                <label for="">Mode et délai paiement :</label>
                                <input type="text" class="form-control" id="modePaiement">
                            </div>

                            <div class="col-sm-12 col-md-12 col-xl-6">
                                <label for="">N° Immatriculation :</label>
                                <input type="text" class="form-control" id="matricule" placeholder="Immatriculation (obligatore)">
                            </div>

                            <div class="col-sm-12 col-md-12 col-xl-6">
                                <label for="">Chauffeur :</label>
                                <input type="text" class="form-control" id="chauffeur" placeholder="Chauffeur (obligatore)">
                            </div>

                            <div class="col-sm-12 col-md-12 col-xl-6">
                                <label for="">C.I.N Chauffeur :</label>
                                <input type="text" class="form-control" id="cin">
                            </div>


                            <hr class="mt-3">

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped ">
                                    <thead>
                                        <tr>
                                            <th>Nom Produit</th> <!-- Column for toggle icon -->
                                            <th>Catégorie</th>
                                            <th>Prix</th>
                                            <th>Quantitié calculer</th>
                                            <th>ٌQuantité société</th>
                                            <th>ٌQuantité min de stock</th>
                                            <th>
                                                <button class="btn btn-sm btn-success mt-2 add-row">+</button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="productTableBody">
                                        <tr>
                                            <td>
                                                <input type="text" id="name" name="name[]" class="form-control name" placeholder="(obligatoire)" autocomplete="on" required>
                                            </td>
                                            <td>
                                                <select name="DropDownCategory[]" id="DropDownCategory" class="form-select DropDownCategory">
                                                    <option value="0">Veuillez sélectionner le catégorie</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" id="price" name="price[]" class="form-control price" placeholder="(obligatoire)" required>
                                            </td>
                                            <td>
                                                <input type="number" min="1" id="qte" name="qte[]" class="form-control qte" placeholder="(obligatoire)" required>
                                            </td>
                                            <td>
                                                <input type="number" min="1" id="qte_company" name="qte_company[]" class="form-control qte_company" placeholder="(obligatoire)" required>
                                            </td>
                                            <td>
                                                <input type="number" min="0" id="qte_notification" name="qte_notification[]" class="form-control qte_notification" placeholder="(obligatoire)" required>
                                            </td>
                                            <td>

                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>


                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="d-flex gap-6 m-0">
                    <button  class="btn btn-success" id="BtnSaveStock">Sauvegarder</button>
                    <button class="btn bg-danger-subtle text-danger" data-bs-dismiss="modal"> fermer</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit Stock --}}
<div class="modal fade " id="EditStock" tabindex="-1" role="dialog" aria-labelledby="addContactModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex align-items-center">
                <h5 class="modal-title">Modification de stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="add-contact-box">
                    <div class="add-contact-content">

                        <ul class="ValidationStockEdit"></ul>
                        <div class="row">

                            <div class="col-sm-12 col-md-12 col-xl-6">
                                <label for="">N° Bon :</label>
                                <input type="text" class="form-control" id="numero_bonEdit" placeholder="N° bon (obligatore)">
                            </div>
                            <div class="col-sm-12 col-md-12 col-xl-6">
                                <label for="">Date :</label>
                                <input type="date" class="form-control" id="dateEdit" placeholder="Date (obligatore)">
                            </div>

                            <div class="col-sm-12 col-md-12 col-xl-6">
                                <label for="">Numéro :</label>
                                <input type="number" class="form-control" id="numeroEdit" placeholder="Numero (obligatore)">
                            </div>
                            <div class="col-sm-12 col-md-12 col-xl-6">
                                <label for="">Commercial :</label>
                                <input type="text" class="form-control" id="commercialEdit" placeholder="Commercial (obligatore)">
                            </div>

                            <div class="col-sm-12 col-md-12 col-xl-6">
                                <label for="">Mode et délai paiement :</label>
                                <input type="text" class="form-control" id="modePaiementEdit">
                            </div>

                            <div class="col-sm-12 col-md-12 col-xl-6">
                                <label for="">N° Immatriculation :</label>
                                <input type="text" class="form-control" id="matriculeEdit" placeholder="Immatriculation (obligatore)">
                            </div>

                            <div class="col-sm-12 col-md-12 col-xl-6">
                                <label for="">Chauffeur :</label>
                                <input type="text" class="form-control" id="chauffeurEdit" placeholder="Chauffeur (obligatore)">
                            </div>

                            <div class="col-sm-12 col-md-12 col-xl-6">
                                <label for="">C.I.N Chauffeur :</label>
                                <input type="text" class="form-control" id="cinEdit">
                            </div>


                            <hr class="mt-3">

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped TableStockEdit">
                                    <thead>
                                        <tr>
                                            <th>Nom Produit</th> <!-- Column for toggle icon -->
                                            <th>Catégorie</th>
                                            <th>Prix</th>
                                            <th>Quantitié calculer</th>
                                            <th>ٌQuantité société</th>
                                            <th>ٌQuantité min de stock</th>
                                            <th>
                                                <button class="btn btn-sm btn-success mt-2 add-rowAppend">+</button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="productTableBodyEdit">
                                        <tr>
                                            <td>
                                                <input type="text" id="name" name="name[]" class="form-control name" placeholder="(obligatoire)" autocomplete="on" required>
                                            </td>
                                            <td>
                                                <select name="DropDownCategory[]" id="DropDownCategory" class="form-select DropDownCategory">
                                                    <option value="0">Veuillez sélectionner le catégorie</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" id="price" name="price[]" class="form-control price" placeholder="(obligatoire)" required>
                                            </td>
                                            <td>
                                                <input type="number" min="1" id="qte" name="qte[]" class="form-control qte" placeholder="(obligatoire)" required>
                                            </td>
                                            <td>
                                                <input type="number" min="1" id="qte_company" name="qte_company[]" class="form-control qte_company" placeholder="(obligatoire)" required>
                                            </td>
                                            <td>
                                                <input type="number" min="0" id="qte_notification" name="qte_notification[]" class="form-control qte_notification" placeholder="(obligatoire)" required>
                                            </td>
                                            <td>

                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>



                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="d-flex gap-6 m-0">
                    <button  class="btn btn-success" id="BtnEditStock">Sauvegarder</button>
                    <button class="btn bg-danger-subtle text-danger" data-bs-dismiss="modal"> fermer</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
