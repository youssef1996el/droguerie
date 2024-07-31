@extends('Dashboard.app')
@section('content')
<script src="{{asset('js/Script_Etat/script.js')}}"></script>
<link rel="stylesheet" href="{{asset('css/Etat/style.css')}}">
<script>
    var SearchEtatTable  = "{{url('SearchEtatTable')}}";
    var EtatProduction  = "{{url('EtatProduction')}}";
    var TotalUniteByDate  = "{{url('TotalUniteByDate')}}";
</script>
<div class="container-fluid">
    <div class="card card-body py-3">
        <div class="row align-items-center">
            <div class="col-12">
                <div class="d-sm-flex align-items-center justify-space-between">
                    <h4 class="mb-4 mb-sm-0 card-title">Rapport</h4>
                    <nav aria-label="breadcrumb" class="ms-auto">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item d-flex align-items-center">
                                <a class="text-muted text-decoration-none d-flex" href="{{url('/home')}}">
                                    <iconify-icon icon="solar:home-2-line-duotone" class="fs-6"></iconify-icon>
                                </a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">
                                <span class="badge fw-medium fs-2 bg-primary-subtle text-primary">
                                    État journalier
                                </span>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    @can('etat-recherche')
        <div class="widget-content searchable-container list">
            <div class="card card-body">
                <div class="row align-items-end">
                    {{-- <form id="searchForm" action="{{url('SearchEtat')}}" method="get">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="startDate" class="form-label mb-0">Date début :</label>
                                <input type="date" class="form-control" id="startDate" name="startDate" value="{{ request()->get('startDate') }}">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="endDate" class="form-label mb-0">Date fin :</label>
                                <input type="date" class="form-control" id="endDate" name="endDate" value="{{ request()->get('endDate') }}">
                            </div>

                            <div class="col-md-4 mt-4">
                                <button type="submit" class="btn btn-primary btn-block">Search</button>
                            </div>
                        </div>
                    </form> --}}

                    <form action="{{url('EtatByClient')}}" method="get">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="startDate" class="form-label mb-0">Date début :</label>
                                @php
                                    $startDate = request()->get('startDate', \Carbon\Carbon::today()->toDateString());
                                @endphp
                                <input type="date" class="form-control" id="startDate" name="startDate" value="{{ $startDate }}">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="endDate" class="form-label mb-0">Date fin :</label>
                                @php
                                    $endDate = request()->get('endDate', \Carbon\Carbon::today()->toDateString());
                                @endphp
                                <input type="date" class="form-control" id="endDate" name="endDate" value="{{ $endDate }}">
                            </div>
                            <div class="col-md-4 mt-4">
                                <button type="submit" class="btn btn-primary btn-block text-uppercase">etat</button>
                            </div>

                    </form>


                </div>
            </div>
        </div>
    @endcan




    <div class="accordion shadow">
        <div class="accordion-item">
            <div class="accordion-header ">Espece</div>
            <div class="accordion-content ">
                <table class="table bg-info table-striped ">
                    <tr>
                        <th>Total Vente</th>
                        <td class="d-flex justify-content-end">{{number_format($espèce->totalPaye - $totalReglement->totalReglement,2,',',' ') }} DH</td>
                    </tr>
                    <tr>
                        <th>Total Reglement</th>
                        <td class="d-flex justify-content-end">{{number_format($totalReglement->totalReglement,2,',',' ') }} DH</td>
                    </tr>
                    <tr>
                        <th>Charge </th>
                        <td class="d-flex justify-content-end">{{number_format($Charge,2,',',' ') }} DH</td>
                    </tr>
                    <tr>
                        <th>Total Net </th>
                        <td class="d-flex justify-content-end">
                            {{number_format(( ($espèce->totalPaye - $totalReglement->totalReglement) + $totalReglement->totalReglement) - ($Charge),2,',',' ') }} DH
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="accordion-item">
            <div class="accordion-header ">Crédit</div>
            <div class="accordion-content ">
                <table class="table bg-info table-striped ">
                    <tr>
                        <th>Total </th>
                        <td class="d-flex justify-content-end">{{number_format($totalRest->totalRest,2,',',' ') }} DH</td>
                    </tr>

                </table>
            </div>
        </div>
        <div class="accordion-item">
            <div class="accordion-header">Chèque</div>
            <div class="accordion-content">
                <table class="table bg-info table-striped ">
                    <tr>
                        <th>Total Vente </th>
                        <td class="d-flex justify-content-end">{{number_format($cheque->totalPaye,2,',',' ') }} DH</td>
                    </tr>
                    <tr>
                        <th>Total Reglement</th>
                        <td class="d-flex justify-content-end">{{number_format($totalReglementCheque->totalReglement,2,',',' ') }} DH</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="accordion-item">
            <div class="accordion-header ">Virement</div>
            <div class="accordion-content ">
                <table class="table bg-info table-striped ">
                    <tr>
                        <th>Total </th>
                        <td class="d-flex justify-content-end">{{number_format($virement->totalPaye,2,',',' ') }} DH</td>
                    </tr>

                </table>
            </div>
        </div>
    </div>
    {{-- <div class="row">
        <div class="col-sm-12 col-md-12 col-xl-12">
            <h3 class="text-uppercase rounded-2 border text-center p-2 mt-3 text-white bg-dark">
                <i class="ti ti-arrow-down"></i>
                    tableau Production
                <i class="ti ti-arrow-down"></i>
            </h3>
            <table class="table table-striped table-bordered" id="TableProduction">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Montant Vente</th>
                        <th>Montant Payé</th>
                        <th>Reste</th>
                        <th>Créer le</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="col-sm-12 col-md-12 col-xl-6">
            <h3 class="text-uppercase rounded-2 border text-center p-2 mt-3 text-white bg-dark">
                <i class="ti ti-arrow-down"></i>
                    tableau Détail produit
                <i class="ti ti-arrow-down"></i>
            </h3>
            <table class="table table-striped table-bordered" id="TableEtat">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Quantité</th>
                        <th>Créer le</th>

                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <div class="col-sm-12 col-md-12 col-xl-6">
            <h3 class="text-uppercase rounded-2 border text-center p-2 mt-3 text-white bg-dark">
                <i class="ti ti-arrow-down"></i>
                    tableau Total Unité
                <i class="ti ti-arrow-down"></i>
            </h3>
            <table class="table table-striped table-bordered" id="TableTotalUnit">
                <thead>
                    <tr>
                        <th>Unité</th>
                        <th>Total</th>

                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div> --}}



</div>
@endsection
