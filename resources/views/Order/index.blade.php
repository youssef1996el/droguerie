@extends('Dashboard.app')
@section('content')
<script src="{{asset('js/Script_Order/script.js')}}"></script>
<script>
    var csrf_token                      = "{{csrf_token()}}";
    var DisplayProductStock             = "{{url('DisplayProductStock')}}";
    var sendDataToTmpOrder              = "{{url('sendDataToTmpOrder')}}";
    var GetTotalByClientCompany         = "{{url('GetTotalByClientCompany')}}";
    var GetDataTmpOrderByClient         = "{{url('GetDataTmpOrderByClient')}}";
    var IdCompanyActiveExtren           = @Json($CompanyIsActive);
    var checkQteProduct                 = "{{url('CheckQteProduct')}}";
    var TrashTmpOrder                   = "{{url('TrashTmpOrder')}}";
    var ChangeQuantityTmp               = "{{url('ChangeQuantityTmp')}}";
    var ModePaiement                    = @Json($ModePaiement);
    var StoreOrder                      = "{{url('StoreOrder')}}";
    var GetMyVente                      = "{{url('GetMyVente')}}";
    var ShowOrder                       = "{{url('ShowOrder')}}";
    var ChangeQteTmpPlus                = "{{url('ChangeQteTmpPlus')}}";
    var ChangeQteTmpMinus               = "{{url('ChangeQteTmpMinus')}}";
    var StoreClient                     = "{{url('StoreClient')}}";
    var getClientByCompany              = "{{url('getClientByCompany')}}";
    var getUniteVenteByProduct          = "{{url('getUniteVenteByProduct')}}";
    var tvaFromDataBase                 = @Json($tva);
    var checkTableTmpHasDataNotThisClient = "{{url('checkTableTmpHasDataNotThisClient')}}";
    var StoreRemark                     = "{{url('StoreRemark')}}";
    var changeAccessoireTmp             = "{{url('changeAccessoireTmp')}}";
</script>
<style>
    @keyframes slideDown
    {
        from {
            transform: translateY(-100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .slide-down
    {
        animation: slideDown 2s ease-out forwards;
    }
    .input-icon
    {
        position: relative;
    }
    .input-icon input
    {
        padding-right: 30px; /* Adjust this value based on the icon size */
    }
    .input-icon .icon
    {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer; /* Add cursor pointer */
    }
    .TableStock tbody tr:hover
    {
        cursor: pointer;
    }
    .quantity
    {
        display: flex;
        border: 2px solid #635bff;
        border-radius: 4px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .quantity button
    {
        background-color: #3498db;
        color: #fff;
        border: none;
        cursor: pointer;
        font-size: 20px;
        width: 30px;
        height: auto;
        text-align: center;
        transition: background-color 0.2s;
    }

        .quantity button:hover {
            background-color: #635bff;
        }

        .input-box {
            width: 40px;
            text-align: center;
            border: none;
            padding: 8px 10px;
            font-size: 16px;
            outline: none;
        }

        /* Hide the number input spin buttons */
        .input-box::-webkit-inner-spin-button,
        .input-box::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
        }

        .input-box[type="number"] {
        -moz-appearance: textfield;
        }

        .buttonAddModePaiement {
  position: relative;
  width: 205px;
  height: 40px;
  cursor: pointer;
  display: flex;
  align-items: center;
  border: 1px solid #2ea95c;
  background-color: #2ea95c;
}

.buttonAddModePaiement, .button__icon, .button__text {
  transition: all 0.3s;
}

.buttonAddModePaiement .button__text {
  transform: translateX(30px);
  color: #fff;
  font-weight: 600;
}

.buttonAddModePaiement .button__icon {
  position: absolute;
  transform: translateX(-8px);
  height: 86%;
  width: 32px;
  background-color: #2ea95c;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 10px;
}

.buttonAddModePaiement .svg {
  width: 30px;
  stroke: #fff;
}

.buttonAddModePaiement:hover {
  background: #2ea95c;
}

.buttonAddModePaiement:hover .button__text {
  color: transparent;
}

.buttonAddModePaiement:hover .button__icon {
  width: 180px;
  transform: translateX(0);
}

.buttonAddModePaiement:active .buttonAddModePaiement {
  background-color: #2ea95c;
}

.buttonAddModePaiement:active {
  border: 1px solid #2ea95c;
}
</style>
<style>
    .tableInforPrice th, .tableInforPrice td {
        border: 1px solid #17a2b8; /* Use Bootstrap info color for borders */
    }
    .TableVente thead th
    {
        white-space: nowrap;
        font-size: 12px;
    }
</style>

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
                                    Vente
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
                    @can('vente-ajoute')
                        <a href="#" id="BtnShowModalAddCompany" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#AddOrder">
                            <i class="ti ti-building-store text-white me-1 fs-5"></i> Ajouter le vente
                        </a>
                    @endcan

                </div>
            </div>
        </div>
    </div>
    <div class="card card-body">
        <h5 class="card-title border p-2 bg-light rounded-2">Fiche les ventes</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-striped TableVente">
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

    <div class="modal fade" id="AddOrder" tabindex="-1" role="dialog" aria-labelledby="addContactModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h5 class="modal-title card-title border p-2 bg-light rounded-2 w-100 text-center">Creation de vente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="add-contact-box">
                        <div class="add-contact-content">
                            <div class="row">
                                <div class="col-5">
                                    <h5 class="modal-title card-title border p-2 bg-light rounded-2 w-100 text-center">Compagnie est active : {{$CompanyIsActive->title}}</h5>
                                </div>
                                <div class="col-7">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-xl-4">
                                            <div class="mb-3 border bg-light p-2 d-flex justify-content-between align-items-center shadow rounded-1">
                                                <span>Total HT:</span>
                                                <span class="text-end flex-grow-1"  id="TotalHT">0.00 DH</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-xl-4">
                                            <div class="mb-3 border bg-light p-2 d-flex justify-content-between align-items-center shadow rounded-1">
                                                <span for="">TVA {{$tva}}:</span>
                                                <span class="text-end flex-grow-1" id="CalculTva">0.00 DH</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-xl-4">
                                            <div class="mb-3 border bg-light p-2 d-flex justify-content-between align-items-center shadow rounded-1">
                                                <span for="">Total TTC:</span>
                                                <span class="text-end flex-grow-1" id="TotalTTC">0.00 DH</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-xl-4">
                                            <div class="mb-3 border bg-light p-2 d-flex justify-content-between align-items-center shadow rounded-1">
                                                <span>Plafonnier:</span>
                                                <span class="text-end flex-grow-1"  id="Plafonnier">0.00 DH</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-xl-4">
                                            <div class="mb-3 border bg-light p-2 d-flex justify-content-between align-items-center shadow rounded-1">
                                                <span>Total crédit:</span>
                                                <span class="text-end flex-grow-1"  id="TotalCredit">0.00 DH</span>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="d-felx ">
                                        <label for="">Client :</label>
                                        <i class="ti ti-user-plus float-end fs-5 cursor-pointer border rounded-2 border-danger" title="Ajouter client" id="OpenModelAddClient"></i>
                                    </div>
                                    <div class="mb-3 contact-name">
                                        <select name="" id="IdClient" class="form-select ">
                                           {{--  <option value="0">veuillez sélectionner le client</option>
                                            @foreach ($Clients as $item)
                                                <option value="{{$item->id}}">{{$item->nom .' '. $item->prenom}} </option>
                                            @endforeach --}}
                                        </select>
                                    </div>
                                    <div class="mb-3 contact-name" id="DivTypeVente" style="display: none">
                                        <label for="">Type de vente</label>
                                        <select name="" id="DropDownTypeVente" class="form-select">

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <label for="">Produit :</label>
                                    <div class="mb-3 contact-name">
                                        <select name="" id="DropDownProduct" class="form-select select2">
                                            <option value="0">veuillez sélectionner le produit</option>
                                            @foreach ($Product as $item)
                                                <option value="{{$item->name}}">{{$item->name}}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-12 col-xl-5 mb-3">
                                    <div class="card card-body border rounded-2 p-2">
                                        <h5 class="card-title border p-2 bg-light rounded-2 text-center text-uppercase fst-italic">Tableau du stock</h5>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped TableStock">
                                                <thead>
                                                    <tr>
                                                        <th>N°Bon</th>
                                                        <th>Produit</th>
                                                        <th>Quantité</th>
                                                        <th>Prix</th>
                                                       {{--  <th>Compagnie</th> --}}


                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="card card-body border rounded-2 p-2">
                                        <h5 class="card-title border p-2 bg-light rounded-2 d-flex justify-content-between align-items-center">
                                            Tableau du mode paiement
                                           {{--  <button class="btn btn-sm btn-primary" >&plus;</button> --}}
                                            <button type="button" class="buttonAddModePaiement btn btn-sm">
                                                <span class="button__text">Ajouter mode paiement</span>
                                                <span class="button__icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" viewBox="0 0 24 24" stroke-width="2" stroke-linejoin="round" stroke-linecap="round" stroke="currentColor" height="24" fill="none" class="svg"><line y2="19" y1="5" x2="12" x1="12"></line><line y2="12" y1="12" x2="19" x1="5"></line></svg></span>
                                              </button>
                                        </h5>

                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped TableModePaiement">
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            <select name="mode_paiement" id="mode_paiement" class="form-select mode_paiement">
                                                                @foreach ($ModePaiement as $item)
                                                                <option value="{{$item->id}}">{{$item->name}}</option>
                                                                @endforeach

                                                            </select>
                                                        </th>
                                                        <th>
                                                            <input type="number" class="form-control TotalModePaiement" placeholder="Saisir montant paiement">
                                                        </th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="card card-body border rounded-2 p-2 CardRemark" style="display: none">
                                        <h5 class="card-title border p-2 bg-light rounded-2 text-center text-uppercase fst-italic">Remarque client</h5>
                                        <div class="form-group FormRemaruqe">
                                            <ul class="ValidationRemark"></ul>
                                            <textarea name="" id="remark" cols="30" rows="5" class="form-control" placeholder="Remarque ... ">{{-- {{$remark}} --}}</textarea>
                                            <button class="btn btn-success mt-3 float-end" id="SaveRemark">Sauvegarder Remarque</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-xl-7 mb-3">
                                    <div class="card card-body border rounded-2 p-2">
                                        <h5 class="card-title border p-2 bg-light rounded-2 text-center text-uppercase fst-italic">Tableau panier par client</h5>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped TableTmpVente">
                                                <thead>
                                                    <tr>
                                                        <th>Produit</th>
                                                        <th style="text-align: center">Quantité</th>
                                                        <th style="text-align: center">Prix</th>
                                                        <th style="text-align: center">Accessoire</th>
                                                        <th style="text-align: center">Total</th>

                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
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
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex gap-6 m-0">
                        <button  class="btn btn-success" id="BtnSaveVente">Vente</button>
                        <button  class="btn btn-success" id="BtnSaveVenteInvocie">Vente avec facture</button>
                        <button class="btn bg-danger-subtle text-danger" data-bs-dismiss="modal"> fermer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade " id="AddClient" tabindex="-1" role="dialog" aria-labelledby="addContactModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content bg-light">
                <div class="modal-header d-flex align-items-center">
                    <h5 class="modal-title card-title border p-2 bg-white rounded-2 w-100 text-center">Fiche Client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="add-contact-box">
                        <div class="add-contact-content">

                            <ul class="ValidationClient"></ul>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 contact-name">
                                        <input type="text" id="nom" name="nom" class="form-control bg-white" placeholder="Nom (obligatoire)" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 contact-email">
                                        <input type="text" id="prenom" name="prenom" class="form-control bg-white" placeholder="Prénom (obligatoire)" required>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 contact-occupation">
                                        <input type="text" id="cin" name="cin" class="form-control bg-white" placeholder="Cin" >

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 contact-phone">
                                    <input type="tel" id="phone" name="phone" class="form-control bg-white" placeholder="Téléphone (obligatoire)" maxlength="10" required>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 contact-location">
                                        <select name="ville" id="ville" class="form-select bg-white">
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
                                        <input type="text" id="adresse" class="form-control bg-white" placeholder="Adresse" >
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 contact-location">
                                        <input type="number" id="plafonnier" class="form-control bg-white" placeholder="Limite maximale de crédit par client." >
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



</div>
@endsection
