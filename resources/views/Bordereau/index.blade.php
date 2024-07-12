@extends('Dashboard.app')
@section('content')
<script src="{{asset('js/Script_Bordereau/script.js')}}"></script>

<script>
    var GetMyBordereau  = "{{url('GetMyBordereau')}}";
    var ShowOrder       = "{{url('ShowOrder')}}";
</script>
<style>
    .TableBordereau thead tr th
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
                    <h4 class="mb-4 mb-sm-0 card-title">Exploitation</h4>
                    <nav aria-label="breadcrumb" class="ms-auto">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item d-flex align-items-center">
                                <a class="text-muted text-decoration-none d-flex" href="{{url('/home')}}">
                                    <iconify-icon icon="solar:home-2-line-duotone" class="fs-6"></iconify-icon>
                                </a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">
                                <span class="badge fw-medium fs-2 bg-primary-subtle text-primary">
                                    Bordereau journalier de production
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
                @can('bordereau journalier-recherche')
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
                            <button type="button" class="btn btn-primary btn-block" id="BtnSearchOrder">Search</button>
                        </div>
                    </div>
                @endcan



            </div>
        </div>
    </div>

    <div class="card card-body">
        <h5 class="card-title border p-2 bg-light rounded-2">Fiche bordereau journalier</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-striped TableBordereau">
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

                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>







</div>
@endsection
