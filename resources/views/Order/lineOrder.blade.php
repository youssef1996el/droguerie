@extends('Dashboard.app')
@section('content')
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
                                    Détail ventes
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
            <h5 class="card-title border p-2 bg-light rounded-2 mb-4">Information client par commande N° {{$id}}</h5>
            <div class="row">
                <div class="col-md-12 col-xl-6">
                    <div class="form-group">
                        <div class="mb-4">
                            <label for="" style="min-width: 115px">Nom client :</label>
                            <span class="border p-2 bg-light rounded-2">{{$client->nom}}</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="mb-4">
                            <label for="" style="min-width: 115px">C.I.N client :</label>
                            <span class="border p-2 bg-light rounded-2">{{$client->cin}}</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="mb-4">
                            <label for="" style="min-width: 115px">Ville client :</label>
                            <span class="border p-2 bg-light rounded-2">{{$client->ville}}</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="mb-4">
                            <label for="" style="min-width: 115px">Plafonnier client :</label>
                            <span class="border p-2 bg-light rounded-2">{{$client->plafonnier}} DH</span>
                        </div>
                    </div>

                </div>
                <div class="col-md-12 col-xl-6">
                    <div class="form-group">
                        <div class="mb-4">
                            <label for="" style="min-width: 115px">Prénom client :</label>
                            <span class="border p-2 bg-light rounded-2">{{$client->prenom}}</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="mb-4">
                            <label for="" style="min-width: 115px">Adresse client :</label>
                            <span class="border p-2 bg-light rounded-2">{{$client->adresse}}</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="mb-4">
                            <label for="" style="min-width: 115px">Téléphone client :</label>
                            <span class="border p-2 bg-light rounded-2">{{$client->phone}}</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="mb-4">
                            <label for="" style="min-width: 115px">Numéro commande :</label>
                            <span class="border p-2 bg-light rounded-2">{{$id}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-body">
        <h5 class="card-title border p-2 bg-light rounded-2">Fiche détail ventes</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-striped TableLineOrder">
                <thead>
                    <tr>
                        <th>Produit</th>

                        <th>Quantite</th>

                        <th>Prix</th>

                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalHT = 0;
                        $TotalAccessoire = 0;
                        $numberLines = 0;
                        $TotalPlus = 0;
                    @endphp
                    @foreach ($DataLineOrder as $item)

                        @php

                            $totalHT += $item->total + $item->accessoire ;
                            $TotalAccessoire = $item->accessoire;
                            $AccessoireParUnit   = 0;
                            $Price               = 0;
                        @endphp
                        <tr>
                            <td>{{$item->name}}</td>

                            <td>{{$item->qte}}</td>

                            <td>{{number_format($item->price_new,2,","," ")}}</td>
                            <td>{{number_format($item->totalnew ,2,","," ")}}</td>






                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex">
                <div class="flex-fill"></div>
                <div class="flex-fill">
                    <table class="table table-striped table-bordered">
                        <tr>
                            <th>Total HT</th>
                            <th class="text-end">{{number_format($totalHT /* + $item->accessoire */,2,","," ")}} DH</th>
                        </tr>
                        @if ($CheckFacutreOrBon->idfacture)
                            @php

                                $taxRate                    = floatval(rtrim($Tva->name, '%')) / 100;
                                $taxAmount                  = ($totalHT ) * $taxRate;
                                $totalIncludingTax          = ($totalHT ) * (1 + $taxRate);
                            @endphp
                            <tr>
                                <th>TVA {{ $Tva->name }}</th>
                                <th class="text-end">{{number_format($taxAmount,2,","," ")  }} DH</th>
                            </tr>
                            <tr>
                                <th>Total TTC</th>
                                <th class="text-end">{{ number_format($totalIncludingTax,2,","," ")  }} DH</th>
                            </tr>
                        @endif

                    </table>
                </div>
            </div>



        </div>
    </div>
</div>
@endsection
