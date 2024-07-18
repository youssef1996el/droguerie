@extends('Dashboard.app')
@section('content')
    <script src="{{asset('js/Script_Cheque/script.js')}}"></script>
    <script>
        var getCheque       = "{{url('Cheque')}}";
        var ChangeStatus    = "{{url('ChangeStatus')}}";
    </script>
    <style>
        .TableCheque thead
        {
            white-space: nowrap;
            font-size: 14px;
        }
    </style>
    <div class="container-fluid">
        <div class="card card-body py-3">
            <div class="row align-items-center">
                <div class="col-12">
                    <div class="d-sm-flex align-items-center justify-space-between">
                        <h4 class="mb-4 mb-sm-0 card-title">TRÉSORIE</h4>
                        <nav aria-label="breadcrumb" class="ms-auto">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item d-flex align-items-center">
                                    <a class="text-muted text-decoration-none d-flex" href="{{url('/home')}}">
                                        <iconify-icon icon="solar:home-2-line-duotone" class="fs-6"></iconify-icon>
                                    </a>
                                </li>
                                <li class="breadcrumb-item" aria-current="page">
                                    <span class="badge fw-medium fs-2 bg-primary-subtle text-primary">
                                        Chèque
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
                <div class="row align-items-end">
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
                            <button type="button" class="btn btn-primary btn-block" id="BtnSearchCheque">Recherche</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-body">
            <h5 class="card-title border p-2 bg-light rounded-2">Fiche Chèque</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped TableCheque">
                    <thead>
                        <tr>
                            <th>Numéro</th>
                            <th>Date Chèque</th>
                            <th>Date Promise</th>
                            <th>Montant</th>
                            <th>Type</th>
                            <th>Nom complet</th>
                            <th>Statut</th>
                            <th>Bank</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade " id="UpdateStatusCheque" tabindex="-1" role="dialog" aria-labelledby="addContactModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h5 class="modal-title border p-2 bg-light rounded-2 text-center w-100 text-uppercase">Modifier Statut Chèque</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="add-contact-box">
                        <div class="add-contact-content">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 contact-name">
                                        <input type="text" id="numero" name="numero" class="form-control" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 contact-email">
                                        <select name="" id="Status" class="form-select">
                                            <option value="En cours">En cours</option>
                                            <option value="Validé">Validé</option>
                                            <option value="Non Validé">Non Validé</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex gap-6 m-0">
                        <button  class="btn btn-success" id="BtnChangeStatusCheque">Sauvegarder</button>
                        <button class="btn bg-danger-subtle text-danger" data-bs-dismiss="modal"> fermer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
