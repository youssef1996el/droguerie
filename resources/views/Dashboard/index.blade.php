@extends('Dashboard.app')
@section('content')
<script src="{{asset('js/Script_index/script.js')}}"></script>
<script>
    var groupedByYear = @Json($groupedByYear);


</script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-5">

                <div class="card text-bg-primary">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-7">
                                <div class="d-flex flex-column h-100">
                                    <div class="hstack gap-3">
                                        <span class="d-flex align-items-center justify-content-center round-48 bg-white rounded flex-shrink-0">
                                        <iconify-icon icon="solar:course-up-outline" class="fs-7 text-muted"></iconify-icon>
                                        </span>
                                        <h5 class="text-white fs-6 mb-0 text-nowrap ">Content de te revoir
                                        <br />{{$NameUser}}
                                        </h5>
                                    </div>
                                    <div class="mt-4 mt-sm-auto">
                                        <div class="row">
                                            <div class="col-6">
                                                <span class="opacity-75">Total</span>
                                                <h5 class="mb-0 text-white mt-1 text-nowrap fs-4">{{$AllTotal - $AlltotalReglementPersonnel}} DH</h5>
                                            </div>
                                            <div class="col-6 border-start border-light" style="--bs-border-opacity: .15;">
                                                <span class="opacity-75 text-nowrap">Total par jour</span>
                                                <h5 class="mb-0 text-white mt-1 text-nowrap">{{$totalToday - $totalReglementPersonnelEveryDay}} DH</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <div class="col-sm-5 text-center text-md-end">
                            <img style="margin-left:26px" src="{{asset('images/welcome-bg.png')}}" alt="welcome" class="img-fluid mb-n7 mt-2" width="180" />
                        </div>
                    </div>


                </div>
            </div>
            <div class="row">
                <!-- -------------------------------------------- -->
                <!-- Customers -->
                <!-- -------------------------------------------- -->
                <div class="col-md-6">
                    <div class="card bg-secondary-subtle overflow-hidden shadow-none">
                        <div class="card-body p-4">
                            <span class="text-dark">Clientes</span>
                            <div class="hstack gap-6 align-items-end mt-1">
                                <h5 class="card-title fw-semibold mb-0 fs-7 mt-1">{{$CountClient}}</h5>
                                <span class="fs-11 text-dark fw-semibold">{{$percentClient}}%</span>
                            </div>
                        </div>
                        <div id="customers"></div>
                    </div>
                </div>
              <!-- -------------------------------------------- -->
              <!-- Projects -->
              <!-- -------------------------------------------- -->
              <div class="col-md-6">
                <div class="card bg-danger-subtle overflow-hidden shadow-none">
                  <div class="card-body p-4">
                    <span class="text-dark">Projects</span>
                    <div class="hstack gap-6 align-items-end mt-1 mb-4">
                      <h5 class="card-title fw-semibold mb-0 fs-7 mt-1">78,298</h5>
                      <span class="fs-11 text-dark fw-semibold">+31.8%</span>
                    </div>
                    <div class="me-n3">
                      <div id="projects"></div>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-7">
            <!-- -------------------------------------------- -->
            <!-- Revenue Forecast -->
            <!-- -------------------------------------------- -->
            <div class="card">
              <div class="card-body">
                <div class="d-md-flex align-items-center justify-content-between mb-4">
                  <div class="hstack align-items-center gap-3">
                    <span class="d-flex align-items-center justify-content-center round-48 bg-primary-subtle rounded flex-shrink-0">
                      <iconify-icon icon="solar:layers-linear" class="fs-7 text-primary"></iconify-icon>
                    </span>
                    <div>
                      <h5 class="card-title">Prévisions de revenus</h5>
                      <p class="card-subtitle mb-0">Aperçu des bénéfices</p>
                    </div>
                  </div>

                  <div class="hstack gap-9 mt-4 mt-md-0">
                    @foreach ($groupedByYear as $key => $value)
                        <div class="d-flex align-items-center gap-2">
                            <span class="d-block flex-shrink-0 round-8  rounded-circle"></span>
                            <span class="text-nowrap text-muted border border-light rounded-circle p-2 bg-light">{{$key}}</span>
                        </div>
                    @endforeach


                  </div>
                </div>
                <div style="height: 285px;" class="me-n7">
                  <div id="revenue-forecast"></div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-5">
            <!-- -------------------------------------------- -->
            <!-- Your Performance -->
            <!-- -------------------------------------------- -->
            <div class="card">
              <div class="card-body">
                <h5 class="card-title fw-semibold">Votre performance</h5>
               {{--  <p class="card-subtitle mb-0">Last check on 25 february</p> --}}

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="vstack gap-9 mt-2">
                            <div class="hstack align-items-center gap-3">
                                <div class="d-flex align-items-center justify-content-center round-48 rounded bg-primary-subtle flex-shrink-0">
                                    <iconify-icon icon="solar:shop-2-linear" class="fs-7 text-primary"></iconify-icon>
                                </div>
                                <div>
                                    <h6 class="mb-0 ">{{$CalcuLNumberBon}} Bons</h6>
                                </div>

                            </div>
                            <div class="hstack align-items-center gap-3">
                                <div class="d-flex align-items-center justify-content-center round-48 rounded bg-danger-subtle">
                                    <iconify-icon icon="solar:filters-outline" class="fs-7 text-danger"></iconify-icon>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{$CalcuLNumberFacture}} Factures</h6>
                                </div>
                            </div>
                            <div class="hstack align-items-center gap-3">
                                <div class="d-flex align-items-center justify-content-center round-48 rounded bg-secondary-subtle">
                                    <iconify-icon icon="solar:pills-3-linear" class="fs-7 text-secondary"></iconify-icon>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{$CalcuLNumberBon + $CalcuLNumberFacture}} Commandes</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-center">
                            <div id="your-preformance"></div>
                            <h2 class="fs-8">{{$TotalOrderStartApp}}</h2>
                            <p class="mb-0 fs-2">
                                Total des opérations depuis le démarrage du système
                            </p>
                        </div>
                    </div>
                </div>

              </div>
            </div>
          </div>
          <div class="col-lg-7">
            <div class="row">
              <div class="col-md-6">
                <!-- -------------------------------------------- -->
                <!-- Customers -->
                <!-- -------------------------------------------- -->
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                      <div>
                        <h5 class="card-title fw-semibold">Customers</h5>
                        <p class="card-subtitle mb-0">Last 7 days</p>
                      </div>
                      <span class="fs-11 text-success fw-semibold lh-lg">+26.5%</span>
                    </div>
                    <div class="py-4 my-1">
                      <div id="customers-area"></div>
                    </div>
                    <div class="d-flex flex-column align-items-center gap-2 w-100 mt-3">
                      <div class="d-flex align-items-center gap-2 w-100">
                        <span class="d-block flex-shrink-0 round-8 bg-primary rounded-circle"></span>
                        <h6 class="fs-3 fw-normal text-muted mb-0">April 07 - April 14</h6>
                        <h6 class="fs-3 fw-normal mb-0 ms-auto text-muted">6,380</h6>
                      </div>
                      <div class="d-flex align-items-center gap-2 w-100">
                        <span class="d-block flex-shrink-0 round-8 bg-light rounded-circle"></span>
                        <h6 class="fs-3 fw-normal text-muted mb-0">Last Week</h6>
                        <h6 class="fs-3 fw-normal mb-0 ms-auto text-muted">4,298</h6>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <!-- -------------------------------------------- -->
                <!-- Sales Overview -->
                <!-- -------------------------------------------- -->
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title fw-semibold">Sales Overview</h5>
                    <p class="card-subtitle mb-1">Last 7 days</p>

                    <div class="position-relative labels-chart">
                      <span class="fs-11 label-1">0%</span>
                      <span class="fs-11 label-2">25%</span>
                      <span class="fs-11 label-3">50%</span>
                      <span class="fs-11 label-4">75%</span>
                      <div id="sales-overview"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>

          <div class="col-lg-12">
            <!-- -------------------------------------------- -->
            <!-- Revenue by Product -->
            <!-- -------------------------------------------- -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-3 mb-2 justify-content-between align-items-center">
                        <h5 class="card-title fw-semibold mb-0">Revenus par commande</h5>
                        <select class="form-select w-auto fw-semibold">
                            <option value="1">Sep 2024</option>
                            <option value="2">Oct 2024</option>
                            <option value="3">Nov 2024</option>
                        </select>
                    </div>

                    <div class="table-responsive">
                        <table class="table  table-striped TableVente">
                            <thead class="fs-2 text-nowrap">
                                <tr>
                                    <th>Client</th>
                                    <th style="text-align: center">Montant Vente</th>
                                    <th style="text-align: center">Montant Payé</th>
                                    <th style="text-align: center">Montant Rest</th>
                                    <th style="text-align: center">Type</th>
                                    <th style="text-align: center">Compagnie</th>
                                    <th style="text-align: center">Créer par</th>
                                    <th style="text-align: center">Créer le</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $item)
                                    <tr>
                                        <td>{{$item->client}}</td>
                                        <td class="text-end">{{$item->totalvente}} DH</td>
                                        <td class="text-end">{{$item->totalpaye}} DH</td>
                                        <td class="text-end">{{$item->reste}} DH</td>
                                        @if($item->idfacture)
                                            <td>
                                                <span class="badge bg-success-subtle text-success">Facture</span>
                                            </td>

                                        @else
                                            <td class="text-center">
                                                <span class="badge bg-danger-subtle text-danger">Bon</span>
                                            </td>

                                        @endif
                                        <td>{{$item->company}}</td>
                                        <td>{{$item->user}}</td>
                                        <td>{{$item->created_at_formatted}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
